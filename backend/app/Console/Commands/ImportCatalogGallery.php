<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\ProductImage;
use App\Services\StorefrontSnapshot;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class ImportCatalogGallery extends Command
{
    protected $signature = 'catalog:gallery {--apply : Back up and append verified supplementary images} {--manifest= : Local manifest path, defaults to bundled catalog}';

    protected $description = 'Append verified gallery images without replacing existing product images or business data';

    public function handle(): int
    {
        $database = config('database.connections.'.config('database.default').'.database');
        if (! app()->environment('local', 'testing') || ! in_array($database, ['thu_thanh', 'thu_thanh_test', 'thu_thanh_sandbox'], true)) {
            $this->error('Only the local storefront or isolated test database is allowed.');

            return self::FAILURE;
        }
        $manifest = json_decode(file_get_contents($this->option('manifest') ?: database_path('data/product-gallery.json')), true, flags: JSON_THROW_ON_ERROR);
        if (($manifest['version'] ?? null) !== 1 || ! is_array($manifest['products'] ?? null)) {
            throw new RuntimeException('Invalid gallery manifest.');
        }
        // All remote downloads happened before this command; validate local immutable assets first.
        $assets = [];
        foreach ($manifest['products'] as $entry) {
            foreach ($entry['images'] as $image) {
                if (! preg_match('/\ATT-[A-Z]+-\d+--[a-f0-9]{16}\.(jpg|png|webp)\z/', $image['file']) || ! str_starts_with($image['file'], $entry['sku'].'--')) {
                    throw new RuntimeException('Invalid gallery filename.');
                }
                $file = database_path('data/gallery-images/'.$image['file']);
                $info = getimagesize($file);
                if (! hash_equals($image['sha256'], hash_file('sha256', $file)) || filesize($file) !== $image['byte_size']) {
                    throw new RuntimeException('Gallery asset hash or size mismatch.');
                }
                if (! $info || $info['mime'] !== $image['mime'] || ! in_array($info['mime'], ['image/jpeg', 'image/png', 'image/webp'], true) || min($info[0], $info[1]) < 160 || filesize($file) > 5 * 1024 * 1024) {
                    throw new RuntimeException('Invalid gallery image.');
                }
                $assets[$image['file']] = $file;
            }
        }
        $disk = Storage::disk('public');
        $created = [];
        try {
            $result = DB::transaction(function () use ($manifest, $database, $assets, $disk, &$created) {
                $rows = Product::whereIn('sku', array_column($manifest['products'], 'sku'))->orderBy('id')->lockForUpdate()->get()->keyBy('sku');
                $plan = [];
                foreach ($manifest['products'] as $entry) {
                    $product = $rows->get($entry['sku']);
                    if (! $product || $product->name !== $entry['name']) {
                        continue;
                    }
                    foreach ($entry['images'] as $image) {
                        $path = 'products/gallery/catalog-v1/'.$image['file'];
                        if (! $product->images()->where('storage_disk', 'public')->where('path', $path)->exists()) {
                            $plan[$product->id]['product'] = $product;
                            $plan[$product->id]['images'][$path] = $image;
                        }
                    }
                }
                $count = array_sum(array_map(fn ($row) => count($row['images']), $plan));
                if (! $this->option('apply') || $count === 0) {
                    return ['products' => count($plan), 'added_images' => $count];
                }
                $backup = 'catalog-gallery-backups/'.$database.'-'.Str::uuid().'.json';
                $snapshot = array_map(fn ($row) => [
                    'product' => $row['product']->only(['id', 'sku', 'name', 'catalog_version']),
                    'images' => $row['product']->images()->orderBy('sort_order')->orderBy('id')->get()->toArray(),
                ], array_values($plan));
                if (! Storage::disk('local')->put($backup, json_encode($snapshot, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR))) {
                    throw new RuntimeException('Gallery backup failed; no images changed.');
                }
                foreach ($plan as $row) {
                    $product = $row['product'];
                    $order = (int) $product->images()->max('sort_order');
                    foreach ($row['images'] as $path => $image) {
                        if ($disk->exists($path)) {
                            if (! hash_equals($image['sha256'], hash('sha256', $disk->get($path)))) {
                                throw new RuntimeException('Refusing to overwrite an existing image file.');
                            }
                        } else {
                            $stream = fopen($assets[$image['file']], 'rb');
                            if (! is_resource($stream)) {
                                throw new RuntimeException('Cannot open gallery image.');
                            }
                            try {
                                if (! $disk->put($path, $stream)) {
                                    throw new RuntimeException('Gallery file write failed.');
                                }
                            } finally {
                                fclose($stream);
                            }
                            $created[$path] = $image['sha256'];
                        }
                        $product->images()->create([
                            'storage_disk' => 'public', 'path' => $path, 'mime' => $image['mime'],
                            'byte_size' => $image['byte_size'], 'alt_text' => $image['alt_text'], 'sort_order' => ++$order,
                        ]);
                    }
                    $product->increment('catalog_version');
                }
                DB::afterCommit(fn () => StorefrontSnapshot::invalidate());
                $this->line('Backup: '.$backup);

                return ['products' => count($plan), 'added_images' => $count];
            });
        } catch (Throwable $exception) {
            // Only remove files created by this failed attempt and still unreferenced after rollback.
            foreach ($created as $path => $hash) {
                if (! ProductImage::where('storage_disk', 'public')->where('path', $path)->exists() && $disk->exists($path) && hash_equals($hash, hash('sha256', $disk->get($path)))) {
                    $disk->delete($path);
                }
            }
            throw $exception;
        }
        $this->line(json_encode(['database' => $database, 'applied' => (bool) $this->option('apply'), ...$result], JSON_THROW_ON_ERROR));

        return self::SUCCESS;
    }
}
