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
            $table->string('phone')->nullable()->after('name');
            $table->string('avatar')->nullable()->after('password');
            $table->boolean('is_active')->default(true)->after('avatar');
            $table->timestamp('last_login_at')->nullable()->after('is_active');
            $table->boolean('marketing_opt_in')->default(false)->after('last_login_at');
            $table->softDeletes();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->unique('phone');
        });

        // Make email nullable without doctrine/dbal (SQLite + MySQL friendly rebuild).
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            // SQLite: recreate is heavy; keep email required for now in sqlite local/dev.
            return;
        }

        DB::statement('ALTER TABLE users MODIFY email VARCHAR(255) NULL');
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['phone']);
            $table->dropSoftDeletes();
            $table->dropColumn([
                'phone',
                'avatar',
                'is_active',
                'last_login_at',
                'marketing_opt_in',
            ]);
        });
    }
};
