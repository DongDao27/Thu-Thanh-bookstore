<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookDescriptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_copy_update_is_dry_run_by_default_and_preserves_prices_stock_and_manual_copy(): void
    {
        $entries = json_decode(file_get_contents(database_path('data/book-descriptions.json')), true)['products'];
        $product = Product::factory()->create(['sku' => $entries[0]['sku'], 'name' => $entries[0]['name'], 'description' => "Nhóm: Sách\nTác giả: Nhiều tác giả", 'stock_on_hand' => 11, 'stock_reserved' => 2, 'list_price' => 33000, 'sale_price' => 31000]);
        $manual = Product::factory()->create(['sku' => $entries[1]['sku'], 'name' => $entries[1]['name'], 'description' => 'Nội dung riêng do người quản trị đã biên soạn.']);
        $before = $product->only(['list_price', 'sale_price', 'stock_on_hand', 'stock_reserved']);
        $this->artisan('catalog:descriptions')->assertSuccessful();
        $this->assertSame($product->description, $product->fresh()->description);
        $this->artisan('catalog:descriptions --apply')->assertSuccessful();
        $this->assertSame($entries[0]['description'], $product->fresh()->description);
        $this->assertSame($before, $product->fresh()->only(array_keys($before)));
        $this->assertSame('Nội dung riêng do người quản trị đã biên soạn.', $manual->fresh()->description);
        $version = $product->fresh()->catalog_version;
        $this->artisan('catalog:descriptions --apply')->assertSuccessful();
        $this->assertSame($version, $product->fresh()->catalog_version);
        $this->assertDatabaseCount('orders', 0);
    }
}
