<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class BuyNowTest extends TestCase
{
    use RefreshDatabase;

    public function test_direct_purchase_preserves_existing_cart_and_replays_once(): void
    {
        $this->actingAs(User::factory()->create());
        $saved = Product::factory()->create(['stock_on_hand' => 20]);
        $selected = Product::factory()->create(['list_price' => 50000, 'sale_price' => 40000, 'stock_on_hand' => 10]);
        $this->postJson('/api/v1/cart/items', ['product_id' => $saved->id, 'quantity' => 3])->assertOk();
        $this->postJson('/api/v1/cart/items', ['product_id' => $selected->id, 'quantity' => 4])->assertOk();
        $input = $this->input($selected, 2);
        $quote = $this->postJson('/api/v1/checkout/quotes', $input)->assertCreated()->assertJsonCount(1, 'data.items')->assertJsonPath('data.totals.payable_total', 80000)->json('data.id');
        // Unrelated saved-cart edits do not invalidate a direct purchase.
        $this->postJson('/api/v1/cart/items', ['product_id' => $saved->id, 'quantity' => 1])->assertOk();
        $key = (string) Str::uuid();
        $this->postJson('/api/v1/orders', ['quote_id' => $quote], ['Idempotency-Key' => $key])->assertCreated();
        $this->postJson('/api/v1/orders', ['quote_id' => $quote], ['Idempotency-Key' => $key])->assertOk()->assertJsonPath('meta.replayed', true);
        $this->assertDatabaseCount('orders', 1);
        $this->assertDatabaseHas('cart_items', ['product_id' => $saved->id, 'quantity' => 4]);
        $this->assertDatabaseHas('cart_items', ['product_id' => $selected->id, 'quantity' => 4]);
        $this->assertSame(2, $selected->fresh()->stock_reserved);
        $this->assertSame(0, $saved->fresh()->stock_reserved);
        $this->assertSame(1, Order::first()->items()->count());
    }

    public function test_direct_purchase_supports_empty_cart_and_rechecks_price_and_stock(): void
    {
        $this->actingAs(User::factory()->create());
        $product = Product::factory()->create(['stock_on_hand' => 5, 'list_price' => 10000, 'sale_price' => null]);
        $quote = $this->postJson('/api/v1/checkout/quotes', $this->input($product, 2))->assertCreated()->json('data.id');
        $product->update(['list_price' => 20000, 'catalog_version' => 2]);
        $this->postJson('/api/v1/orders', ['quote_id' => $quote], ['Idempotency-Key' => (string) Str::uuid()])->assertStatus(409)->assertJsonPath('error.code', 'PRICE_CHANGED');
        $quote = $this->postJson('/api/v1/checkout/quotes', $this->input($product, 2))->assertCreated()->json('data.id');
        $product->update(['stock_on_hand' => 1]);
        $this->postJson('/api/v1/orders', ['quote_id' => $quote], ['Idempotency-Key' => (string) Str::uuid()])->assertStatus(409);
        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('cart_items', 0);
    }

    public function test_direct_selection_rejects_invalid_quantity_or_extra_fields(): void
    {
        $this->actingAs(User::factory()->create());
        $product = Product::factory()->create();
        $this->postJson('/api/v1/checkout/quotes', $this->input($product, 100))->assertUnprocessable();
        $input = $this->input($product, 1);
        $input['buy_now']['price'] = 1;
        $this->postJson('/api/v1/checkout/quotes', $input)->assertUnprocessable();
    }

    public function test_cancelling_direct_order_restores_selection_to_saved_cart_only_once(): void
    {
        $this->actingAs(User::factory()->create());
        $product = Product::factory()->create(['stock_on_hand' => 20]);
        $this->postJson('/api/v1/cart/items', ['product_id' => $product->id, 'quantity' => 4])->assertOk();
        $quote = $this->postJson('/api/v1/checkout/quotes', $this->input($product, 2))->assertCreated()->json('data.id');
        $code = $this->postJson('/api/v1/orders', ['quote_id' => $quote], ['Idempotency-Key' => (string) Str::uuid()])->assertCreated()->json('data.order_code');
        $this->postJson('/api/v1/orders/'.$code.'/cancel')->assertOk();
        $this->postJson('/api/v1/orders/'.$code.'/cancel')->assertOk();
        $this->assertDatabaseHas('cart_items', ['product_id' => $product->id, 'quantity' => 6]);
        $this->assertSame(0, $product->fresh()->stock_reserved);
    }

    private function input(Product $product, int $quantity): array
    {
        return ['fulfillment_method' => 'pickup', 'recipient_name' => 'Khách kiểm thử', 'recipient_phone' => '0900000001', 'buy_now' => ['product_id' => $product->id, 'quantity' => $quantity]];
    }
}
