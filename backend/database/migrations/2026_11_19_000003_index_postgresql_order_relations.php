<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }
        // Unlike MySQL, PostgreSQL does not create indexes on referencing FK columns.
        // These back order detail/eager loading, inventory reservation and image queries.
        foreach ($this->indexes() as $table => $columns) {
            Schema::table($table, fn (Blueprint $blueprint) => $blueprint->index($columns, $table.'_lookup_pg'));
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }
        foreach ($this->indexes() as $table => $columns) {
            Schema::table($table, fn (Blueprint $blueprint) => $blueprint->dropIndex($table.'_lookup_pg'));
        }
    }

    private function indexes(): array
    {
        return [
            'order_items' => ['order_id', 'product_id'],
            'product_images' => ['product_id', 'sort_order', 'id'],
            'stock_reservations' => ['product_id', 'status'],
            'reconciliation_cases' => ['order_id', 'status'],
        ];
    }
};
