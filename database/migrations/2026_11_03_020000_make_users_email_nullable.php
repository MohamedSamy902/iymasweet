<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'sqlite') {
            // Rebuild users so email can be NULL (SQLite cannot ALTER NULL easily).
            DB::statement('PRAGMA foreign_keys=OFF');

            DB::statement('CREATE TABLE users_new (
                id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                name VARCHAR NOT NULL,
                email VARCHAR NULL,
                email_verified_at DATETIME NULL,
                password VARCHAR NOT NULL,
                remember_token VARCHAR NULL,
                created_at DATETIME NULL,
                updated_at DATETIME NULL,
                phone VARCHAR NULL,
                avatar VARCHAR NULL,
                is_active TINYINT(1) NOT NULL DEFAULT 1,
                last_login_at DATETIME NULL,
                marketing_opt_in TINYINT(1) NOT NULL DEFAULT 0,
                deleted_at DATETIME NULL
            )');

            DB::statement('INSERT INTO users_new (
                id, name, email, email_verified_at, password, remember_token,
                created_at, updated_at, phone, avatar, is_active, last_login_at,
                marketing_opt_in, deleted_at
            )
            SELECT
                id, name,
                CASE WHEN email IS NULL OR email = \'\' THEN NULL ELSE email END,
                email_verified_at, password, remember_token,
                created_at, updated_at, phone, avatar, is_active, last_login_at,
                marketing_opt_in, deleted_at
            FROM users');

            DB::statement('DROP TABLE users');
            DB::statement('ALTER TABLE users_new RENAME TO users');
            DB::statement('CREATE UNIQUE INDEX users_email_unique ON users (email)');
            DB::statement('CREATE UNIQUE INDEX users_phone_unique ON users (phone)');

            DB::statement('PRAGMA foreign_keys=ON');

            return;
        }

        // MySQL / MariaDB
        DB::statement('ALTER TABLE users MODIFY email VARCHAR(255) NULL');
    }

    public function down(): void
    {
        // Irreversible safely for SQLite without data loss risk; leave as-is.
    }
};
