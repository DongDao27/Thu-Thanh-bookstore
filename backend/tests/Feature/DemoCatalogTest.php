<?php

namespace Tests\Feature;

use App\Models\Product;
use Database\Seeders\DemoCatalogSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DemoCatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_explicit_demo_seed_has_images_and_preserves_edits_without_creating_accounts(): void
    {
        Storage::fake('public');
        $this->seed(DemoCatalogSeeder::class);
        $this->assertDatabaseCount('products', 24);
        $this->assertDatabaseCount('product_images', 24);
        $this->assertDatabaseCount('users', 0);
        $first = Product::with('images')->firstOrFail();
        Storage::disk('public')->assertExists($first->images->first()->path);
        $first->update(['list_price' => 99000, 'stock_on_hand' => 7]);
        $this->seed(DemoCatalogSeeder::class);
        $this->assertDatabaseCount('products', 24);
        $this->assertDatabaseCount('inventory_movements', 24);
        $this->assertSame(99000, $first->fresh()->list_price);
        $this->assertSame(7, $first->fresh()->stock_on_hand);
        $this->getJson('/api/v1/products')->assertOk()->assertJsonCount(20, 'data');
    }

    public function test_demo_seed_refuses_production_before_writing_any_data(): void
    {
        $this->app->instance('env', 'production');
        try {
            $this->app->make(DemoCatalogSeeder::class)->run();
            $this->fail('Production demo seed was allowed.');
        } catch (\RuntimeException $exception) {
            $this->assertStringContainsString('No data changed', $exception->getMessage());
        }
        $this->assertDatabaseCount('products', 0);
        $this->assertDatabaseCount('categories', 0);
    }
}
