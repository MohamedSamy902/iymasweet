<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_images', function (Blueprint $table) {
            $table->string('type', 20)->default('image')->after('path'); // image|video
            $table->string('poster_path')->nullable()->after('type');
            $table->string('mime', 80)->nullable()->after('poster_path');
        });
    }

    public function down(): void
    {
        Schema::table('product_images', function (Blueprint $table) {
            $table->dropColumn(['type', 'poster_path', 'mime']);
        });
    }
};
