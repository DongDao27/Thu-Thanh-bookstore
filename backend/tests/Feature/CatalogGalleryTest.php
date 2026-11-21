<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Tests\TestCase;

class CatalogGalleryTest extends TestCase
{
    use RefreshDatabase;

    public function test_gallery_is_dry_run_by_default_append_only_and_idempotent(): void
    {
        Storage::fake('public');
        Storage::fake('local');
        $entries = json_decode(file_get_contents(database_path('data/product-gallery.json')), true)['products'];
        $product = Product::factory()->create(['sku' => $entries[0]['sku'], 'name' => $entries[0]['name'], 'list_price' => 50000, 'sale_price' => 45000, 'stock_on_hand' => 11, 'stock_reserved' => 2]);
        $other = Product::factory()->create(['sku' => $entries[1]['sku'], 'name' => 'Tên đã được quản trị viên thay đổi']);
        Storage::disk('public')->put('products/manual.jpg', 'manual-original');
        $original = $product->images()->create(['storage_disk' => 'public', 'path' => 'products/manual.jpg', 'mime' => 'image/jpeg', 'byte_size' => 15, 'sort_order' => 9, 'alt_text' => 'Ảnh quản trị viên']);
        $money = $product->only(['list_price', 'sale_price', 'stock_on_hand', 'stock_reserved']);
        $this->artisan('catalog:gallery')->assertSuccessful();
        $this->assertSame(1, $product->images()->count());
        $this->assertSame([], Storage::disk('local')->allFiles());
        $this->artisan('catalog:gallery --apply')->assertSuccessful();
        $this->assertSame(1 + count($entries[0]['images']), $product->images()->count());
        $this->assertSame($original->id, $product->images()->orderBy('sort_order')->orderBy('id')->first()->id);
        $this->assertSame('manual-original', Storage::disk('public')->get('products/manual.jpg'));
        $this->assertSame($money, $product->fresh()->only(array_keys($money)));
        $this->assertSame(0, $other->images()->count());
        $this->assertCount(1, Storage::disk('local')->allFiles('catalog-gallery-backups'));
        $version = $product->fresh()->catalog_version;
        $this->artisan('catalog:gallery --apply')->assertSuccessful();
        $this->assertSame($version, $product->fresh()->catalog_version);
        $this->assertCount(1, Storage::disk('local')->allFiles('catalog-gallery-backups'));
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_tampered_asset_manifest_is_rejected_before_writes(): void
    {
        Storage::fake('public');
        Storage::fake('local');
        $manifest = json_decode(file_get_contents(database_path('data/product-gallery.json')), true);
        $manifest['products'] = [$manifest['products'][0]];
        $manifest['products'][0]['images'][0]['sha256'] = str_repeat('0', 64);
        $path = storage_path('app/gallery-invalid-test.json');
        file_put_contents($path, json_encode($manifest, JSON_THROW_ON_ERROR));
        try {
            Artisan::call('catalog:gallery', ['--apply' => true, '--manifest' => $path]);
            $this->fail('Tampered asset accepted.');
        } catch (RuntimeException $exception) {
            $this->assertStringContainsString('hash or size mismatch', $exception->getMessage());
            $this->assertSame([], Storage::disk('public')->allFiles());
            $this->assertSame([], Storage::disk('local')->allFiles());
            $this->assertDatabaseCount('product_images', 0);
        } finally {
            unlink($path);
        }
    }
}
