<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }
        // Equivalent to the unsigned columns on the previous engine, including future inserts.
        foreach ($this->columns() as $column) {
            DB::statement('ALTER TABLE "'.$column->table_name.'" ADD CONSTRAINT "'.$column->table_name.'_'.$column->column_name.'_nonnegative" CHECK ("'.$column->column_name.'" >= 0)');
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }
        foreach ($this->columns() as $column) {
            DB::statement('ALTER TABLE "'.$column->table_name.'" DROP CONSTRAINT IF EXISTS "'.$column->table_name.'_'.$column->column_name.'_nonnegative"');
        }
    }

    private function columns(): array
    {
        return DB::select("SELECT table_name, column_name FROM information_schema.columns WHERE table_schema = current_schema() AND data_type IN ('smallint', 'integer', 'bigint') AND (column_name IN ('list_price', 'sale_price', 'items_subtotal', 'discount_total', 'shipping_fee', 'payable_total', 'list_unit_price', 'charged_unit_price', 'discount_line_total', 'line_total', 'requested_amount', 'received_amount', 'amount', 'merchandise_amount', 'shipping_amount', 'byte_size', 'catalog_version', 'version'))");
    }
};
