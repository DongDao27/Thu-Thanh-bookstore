<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Services\CatalogService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class DemoCatalogSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment('local', 'testing')
            || config('database.default') !== 'pgsql'
            || ! in_array(DB::connection()->getDatabaseName(), ['thu_thanh_demo', 'thu_thanh_test'], true)) {
            throw new RuntimeException('Demo seed requires local/testing PostgreSQL thu_thanh_demo or thu_thanh_test. No data changed.');
        }

        $rows = json_decode(file_get_contents(database_path('data/demo-catalog.json')), true, flags: JSON_THROW_ON_ERROR);
        // Validate all bundled files before making any database changes.
        foreach ($rows as $row) {
            if (! is_file(base_path('../frontend/public'.$row['image']))) {
                throw new RuntimeException('Bundled demo image missing: '.$row['image']);
            }
        }
        $this->call(CategorySeeder::class);
        $disk = Storage::disk('public');
        foreach ($rows as $row) {
            if (Product::where('sku', $row['sku'])->exists()) {
                continue; // Preserve edited price, stock, history and images on repeated seed.
            }
            $file = base_path('../frontend/public'.$row['image']);
            $path = 'products/demo/'.basename($file);
            $bytes = file_get_contents($file);
            if (! $disk->put($path, $bytes)) {
                throw new RuntimeException('Could not store demo image.');
            }
            DB::transaction(function () use ($row, $file, $path, $bytes): void {
                $product = app(CatalogService::class)->save([
                    'category_id' => Category::where('slug', $row['category'])->firstOrFail()->id,
                    'sku' => $row['sku'], 'slug' => $row['slug'], 'name' => $row['name'],
                    'description' => $row['description'], 'grade' => $row['grade'],
                    'list_price' => $row['price'], 'opening_stock' => 20,
                    'is_active' => true, 'is_bestseller' => $row['featured'],
                ], null);
                ProductImage::create([
                    'product_id' => $product->id, 'storage_disk' => 'public', 'path' => $path,
                    'mime' => mime_content_type($file), 'byte_size' => strlen($bytes),
                    'sort_order' => 0, 'alt_text' => $product->name,
                ]);
            });
        }
    }
}
