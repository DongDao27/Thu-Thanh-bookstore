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
        DB::statement('CREATE EXTENSION IF NOT EXISTS unaccent');
        DB::statement('CREATE EXTENSION IF NOT EXISTS pg_trgm');
        // Fixed dictionary permits an indexed expression. Reindex if its rules ever change.
        DB::unprepared("CREATE OR REPLACE FUNCTION public.catalog_search_text(text) RETURNS text LANGUAGE sql IMMUTABLE STRICT PARALLEL SAFE AS $$ SELECT lower(public.unaccent('public.unaccent', $1)) $$");
        DB::statement('CREATE INDEX products_name_search_trgm ON products USING gin (public.catalog_search_text(name) gin_trgm_ops)');
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }
        DB::statement('DROP INDEX IF EXISTS products_name_search_trgm');
        DB::statement('DROP FUNCTION IF EXISTS public.catalog_search_text(text)');
        // Extensions may be shared with other application objects.
    }
};
