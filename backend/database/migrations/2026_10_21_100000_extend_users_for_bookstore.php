<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('name', 120)->change();
            $table->string('email', 254)->nullable()->change();
            $table->string('phone', 20)->nullable()->unique();
            $table->string('role', 20)->default('customer');
            $table->unsignedTinyInteger('admin_slot')->nullable()->storedAs("CASE WHEN role = 'admin' THEN 1 ELSE NULL END")->unique();
        });
        DB::statement("ALTER TABLE users ADD CONSTRAINT users_identity CHECK (email IS NOT NULL OR phone IS NOT NULL), ADD CONSTRAINT users_role CHECK (role IN ('customer','admin'))");
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE users DROP CONSTRAINT users_identity, DROP CONSTRAINT users_role');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['admin_slot', 'phone', 'role']);
        });
        // Email remains nullable: forcing NOT NULL would destroy phone-only accounts.
    }
};
