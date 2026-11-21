<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccessTokenTest extends TestCase
{
    use RefreshDatabase;

    public function test_issue_requires_password_and_role_whitelist_and_returns_plaintext_only_once(): void
    {
        $user = User::factory()->create(['password' => 'private-password-1234']);
        $input = ['name' => 'Course client', 'abilities' => ['catalog:read'], 'days' => 7, 'password' => 'wrong'];
        $this->actingAs($user)->postJson('/api/v1/auth/tokens', $input)->assertUnprocessable();
        $input['password'] = 'private-password-1234';
        foreach (['*', 'catalog:write', 'operations:read'] as $forbidden) {
            $this->postJson('/api/v1/auth/tokens', [...$input, 'abilities' => [$forbidden]])->assertUnprocessable();
        }
        $response = $this->postJson('/api/v1/auth/tokens', $input)->assertCreated();
        $plain = $response->json('data.token');
        $this->assertIsString($plain);
        $this->assertTrue($user->tokens()->first()->expires_at->isFuture());
        $this->getJson('/api/v1/auth/tokens')->assertOk()->assertDontSee($plain)->assertJsonMissingPath('data.0.token');
        $this->assertNotSame(explode('|', $plain)[1], $user->tokens()->first()->token);
    }

    public function test_invalid_expiry_and_foreign_revocation_are_rejected(): void
    {
        $user = User::factory()->create(['password' => 'private-password-1234']);
        $foreign = User::factory()->create()->createToken('Other', ['catalog:read'], now()->addDay());
        $this->actingAs($user)->deleteJson('/api/v1/auth/tokens/'.$foreign->accessToken->id)->assertNotFound();
        foreach ([0, 31] as $days) {
            $this->postJson('/api/v1/auth/tokens', ['name' => 'Bad expiry', 'abilities' => ['catalog:read'], 'days' => $days, 'password' => 'private-password-1234'])->assertUnprocessable();
        }
        $this->assertNotNull($foreign->accessToken->fresh());
    }

    public function test_bearer_can_only_use_its_scoped_routes_and_never_session_routes(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('Read catalog', ['catalog:read'], now()->addDay());
        $this->withToken($token->plainTextToken)->getJson('/api/v1/integrations/account')->assertOk()->assertJsonPath('data.id', $user->id);
        $this->app['auth']->forgetGuards();
        $this->getJson('/api/v1/integrations/products')->assertOk();
        $this->app['auth']->forgetGuards();
        $this->getJson('/api/v1/integrations/orders/ANY-ORDER')->assertForbidden();
        $this->getJson('/api/v1/auth/me')->assertForbidden();
        $this->getJson('/api/v1/admin/revenue')->assertForbidden();
    }

    public function test_expired_and_revoked_tokens_are_unauthenticated(): void
    {
        $user = User::factory()->create();
        $expired = $user->createToken('Expired', ['catalog:read'], now()->subSecond());
        $this->withToken($expired->plainTextToken)->getJson('/api/v1/integrations/account')->assertUnauthorized();
        $token = $user->createToken('Revoke', ['catalog:read'], now()->addDay());
        $this->flushHeaders();
        $this->actingAs($user, 'web')->deleteJson('/api/v1/auth/tokens/'.$token->accessToken->id)->assertNoContent();
        $this->app['auth']->forgetGuards();
        $this->withToken($token->plainTextToken)->getJson('/api/v1/integrations/account')->assertUnauthorized();
    }

    public function test_scope_does_not_replace_role_or_order_ownership(): void
    {
        $customer = User::factory()->create();
        $forged = $customer->createToken('Legacy misconfigured scopes', ['catalog:write'], now()->addDay());
        $this->withToken($forged->plainTextToken)->postJson('/api/v1/integrations/products', [])->assertForbidden();
        $this->app['auth']->forgetGuards();
        $reader = $customer->createToken('Read own orders', ['orders:read'], now()->addDay());
        $this->withToken($reader->plainTextToken)->getJson('/api/v1/integrations/orders/NOT-OWNED')->assertNotFound();
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_browser_session_cannot_substitute_for_a_scoped_token(): void
    {
        $this->actingAs(User::factory()->create())->getJson('/api/v1/integrations/products')->assertForbidden();
        $this->getJson('/api/v1/auth/me')->assertOk();
    }

    public function test_scoped_reader_cannot_read_another_customers_existing_order(): void
    {
        $owner = User::factory()->create();
        $product = Product::factory()->create(['stock_on_hand' => 5]);
        $this->actingAs($owner)->postJson('/api/v1/cart/items', ['product_id' => $product->id, 'quantity' => 1])->assertOk();
        $quote = $this->postJson('/api/v1/checkout/quotes', ['fulfillment_method' => 'pickup', 'recipient_name' => 'Test Owner', 'recipient_phone' => '0912345678'])->assertCreated()->json('data.id');
        $code = $this->postJson('/api/v1/orders', ['quote_id' => $quote], ['Idempotency-Key' => 'scoped-order-owner'])->assertCreated()->json('data.order_code');
        $other = User::factory()->create()->createToken('Other reader', ['orders:read'], now()->addDay());
        $this->app['auth']->forgetGuards();
        $this->withToken($other->plainTextToken)->getJson('/api/v1/integrations/orders/'.$code)->assertNotFound();
        $own = $owner->createToken('Own reader', ['orders:read'], now()->addDay());
        $this->app['auth']->forgetGuards();
        $this->withToken($own->plainTextToken)->getJson('/api/v1/integrations/orders/'.$code)->assertOk()->assertJsonPath('data.order_code', $code);
    }

    public function test_admin_write_scope_uses_existing_validation_and_inventory_rules(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $token = $admin->createToken('Catalog writer', ['catalog:write'], now()->addDay());
        $category = Category::factory()->create();
        $input = ['category_id' => $category->id, 'sku' => 'SCOPED-SKU', 'name' => 'Scoped book', 'list_price' => 25000, 'opening_stock' => 3];
        $this->withToken($token->plainTextToken)->postJson('/api/v1/integrations/products', $input)->assertCreated();
        $this->assertDatabaseHas('products', ['sku' => 'SCOPED-SKU', 'stock_on_hand' => 3]);
        $this->app['auth']->forgetGuards();
        $this->postJson('/api/v1/integrations/products', [...$input, 'sku' => 'INVALID', 'list_price' => -1])->assertUnprocessable();
    }

    public function test_admin_can_issue_limited_catalog_token_but_cannot_use_it_for_operations(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'password' => 'private-password-1234']);
        $issued = $this->actingAs($admin)->postJson('/api/v1/auth/tokens', ['name' => 'Admin catalog', 'abilities' => ['catalog:read'], 'days' => 1, 'password' => 'private-password-1234'])->assertCreated();
        $this->app['auth']->forgetGuards();
        $this->withToken($issued->json('data.token'))->getJson('/api/v1/integrations/operations')->assertForbidden();
    }
}
