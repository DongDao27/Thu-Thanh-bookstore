<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Services\StorefrontSnapshot;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UpdateBookDescriptions extends Command
{
    protected $signature = 'catalog:descriptions {--apply : Back up and replace metadata-only book descriptions}';

    protected $description = 'Fill book descriptions without changing manual copy, prices, stock or orders';

    public function handle(): int
    {
        $database = config('database.connections.'.config('database.default').'.database');
        if (! app()->environment('local', 'testing') || ! in_array($database, ['thu_thanh', 'thu_thanh_test', 'thu_thanh_sandbox'], true)) {
            $this->error('Only the local bookstore or isolated test database is allowed.');

            return self::FAILURE;
        }
        $manifest = json_decode(file_get_contents(database_path('data/book-descriptions.json')), true, flags: JSON_THROW_ON_ERROR);
        $changed = DB::transaction(function () use ($manifest, $database) {
            $rows = Product::whereIn('sku', array_column($manifest['products'], 'sku'))->orderBy('id')->lockForUpdate()->get()->keyBy('sku');
            $updates = [];
            foreach ($manifest['products'] as $entry) {
                $product = $rows->get($entry['sku']);
                if (! $product || $product->name !== $entry['name']) {
                    continue;
                }
                $lines = preg_split('/\R/u', trim($product->description ?? ''));
                $manual = array_filter($lines, fn ($line) => trim($line) !== '' && ! preg_match('/^(Nhóm|NXB \/ Thương hiệu|Tác giả):/u', $line));
                if ($manual || $product->description === $entry['description']) {
                    continue;
                }
                $updates[] = [$product, $entry['description']];
            }
            if (! $this->option('apply') || $updates === []) {
                return count($updates);
            }
            $directory = storage_path('app/private/catalog-description-backups');
            if (! is_dir($directory) && ! mkdir($directory, 0700, true) && ! is_dir($directory)) {
                throw new \RuntimeException('Cannot create backup directory.');
            }
            $backup = $directory.'/'.$database.'-'.Str::uuid().'.json';
            $content = json_encode(array_map(fn ($row) => $row[0]->only(['id', 'sku', 'name', 'description', 'catalog_version']), $updates), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
            if (file_put_contents($backup, $content, LOCK_EX) !== strlen($content)) {
                throw new \RuntimeException('Backup failed; no descriptions changed.');
            }
            foreach ($updates as [$product, $description]) {
                Product::whereKey($product->id)->update(['description' => $description, 'catalog_version' => $product->catalog_version + 1]);
            }
            DB::afterCommit(fn () => StorefrontSnapshot::invalidate());
            $this->line('Backup: '.$backup);

            return count($updates);
        });
        $this->line(json_encode(['database' => $database, 'descriptions' => $changed, 'applied' => (bool) $this->option('apply')], JSON_THROW_ON_ERROR));

        return self::SUCCESS;
    }
}
