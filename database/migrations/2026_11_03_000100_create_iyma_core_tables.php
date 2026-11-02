<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('governorates', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar');
            $table->string('name_en')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('areas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('governorate_id')->constrained()->cascadeOnDelete();
            $table->string('name_ar');
            $table->string('name_en')->nullable();
            $table->decimal('delivery_fee', 10, 2)->default(0);
            $table->decimal('min_order', 10, 2)->nullable();
            $table->unsignedInteger('eta_minutes')->nullable();
            $table->decimal('free_delivery_over', 10, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['governorate_id', 'is_active']);
        });

        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('label')->nullable();
            $table->string('recipient_name');
            $table->string('phone');
            $table->foreignId('governorate_id')->constrained();
            $table->foreignId('area_id')->constrained();
            $table->string('street');
            $table->string('building')->nullable();
            $table->string('floor')->nullable();
            $table->string('apartment')->nullable();
            $table->string('landmark')->nullable();
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->text('notes_for_courier')->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        Schema::create('fcm_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('admin_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->string('token')->unique();
            $table->string('platform')->default('web');
            $table->string('user_agent')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('slug')->unique();
            $table->string('name_ar');
            $table->string('name_en')->nullable();
            $table->text('description_ar')->nullable();
            $table->text('description_en')->nullable();
            $table->string('image')->nullable();
            $table->string('icon')->nullable();
            $table->string('color', 20)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->string('seo_title_ar')->nullable();
            $table->string('seo_title_en')->nullable();
            $table->text('seo_description_ar')->nullable();
            $table->text('seo_description_en')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('slug')->unique();
            $table->string('name_ar');
            $table->string('name_en')->nullable();
            $table->string('short_description_ar')->nullable();
            $table->string('short_description_en')->nullable();
            $table->longText('description_ar')->nullable();
            $table->longText('description_en')->nullable();
            $table->decimal('base_price', 10, 2);
            $table->decimal('compare_at_price', 10, 2)->nullable();
            $table->string('availability_type')->default('available_now')->index();
            $table->unsignedInteger('stock_quantity')->nullable();
            $table->timestamp('available_from')->nullable();
            $table->timestamp('available_until')->nullable();
            $table->unsignedInteger('lead_time_hours')->nullable();
            $table->unsignedInteger('min_qty')->default(1);
            $table->unsignedInteger('max_qty_per_order')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_new')->default(false);
            $table->boolean('is_best_seller')->default(false);
            $table->boolean('is_active')->default(true)->index();
            $table->boolean('allow_customization')->default(false);
            $table->json('customization_config')->nullable();
            $table->text('ingredients_ar')->nullable();
            $table->text('ingredients_en')->nullable();
            $table->text('allergens_ar')->nullable();
            $table->text('allergens_en')->nullable();
            $table->unsignedInteger('serves_min')->nullable();
            $table->unsignedInteger('serves_max')->nullable();
            $table->unsignedInteger('weight_grams')->nullable();
            $table->text('storage_instructions_ar')->nullable();
            $table->text('storage_instructions_en')->nullable();
            $table->text('preparation_notes')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->unsignedInteger('views_count')->default(0);
            $table->decimal('avg_rating', 3, 2)->default(0);
            $table->unsignedInteger('ratings_count')->default(0);
            $table->string('seo_title_ar')->nullable();
            $table->string('seo_title_en')->nullable();
            $table->text('seo_description_ar')->nullable();
            $table->text('seo_description_en')->nullable();
            $table->text('search_text')->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->index(['category_id', 'is_active', 'availability_type']);
        });

        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('path');
            $table->string('alt_ar')->nullable();
            $table->string('alt_en')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
        });

        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('name_ar');
            $table->string('name_en')->nullable();
            $table->decimal('price', 10, 2);
            $table->string('sku')->nullable();
            $table->unsignedInteger('stock_quantity')->nullable();
            $table->boolean('is_default')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('addon_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('name_ar');
            $table->string('name_en')->nullable();
            $table->string('type')->default('single');
            $table->boolean('is_required')->default(false);
            $table->unsignedInteger('min_select')->default(0);
            $table->unsignedInteger('max_select')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('addon_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('addon_group_id')->constrained()->cascadeOnDelete();
            $table->string('name_ar');
            $table->string('name_en')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name_ar');
            $table->string('name_en')->nullable();
            $table->timestamps();
        });

        Schema::create('product_tag', function (Blueprint $table) {
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained()->cascadeOnDelete();
            $table->primary(['product_id', 'tag_id']);
        });

        Schema::create('attributes', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('name_ar');
            $table->string('name_en')->nullable();
            $table->string('type')->default('select');
            $table->boolean('is_filterable')->default(true);
            $table->boolean('show_on_product_page')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->json('applies_to_categories')->nullable();
            $table->timestamps();
        });

        Schema::create('attribute_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attribute_id')->constrained()->cascadeOnDelete();
            $table->string('value_ar');
            $table->string('value_en')->nullable();
            $table->string('color_hex', 20)->nullable();
            $table->string('icon')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('product_attribute_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('attribute_id')->constrained()->cascadeOnDelete();
            $table->foreignId('attribute_value_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('numeric_value', 10, 2)->nullable();
            $table->timestamps();
            $table->unique(['product_id', 'attribute_id', 'attribute_value_id'], 'pav_unique');
        });

        Schema::create('bundles', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name_ar');
            $table->string('name_en')->nullable();
            $table->text('description_ar')->nullable();
            $table->text('description_en')->nullable();
            $table->string('image')->nullable();
            $table->string('type')->default('fixed');
            $table->decimal('price', 10, 2);
            $table->decimal('compare_at_price', 10, 2)->nullable();
            $table->unsignedInteger('items_count')->nullable();
            $table->unsignedInteger('min_items')->nullable();
            $table->unsignedInteger('max_items')->nullable();
            $table->string('availability_type')->default('available_now');
            $table->unsignedInteger('lead_time_hours')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('bundle_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bundle_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('variant_id')->nullable()->constrained('product_variants')->nullOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            $table->timestamps();
        });

        Schema::create('bundle_allowed_products', function (Blueprint $table) {
            $table->foreignId('bundle_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->primary(['bundle_id', 'product_id']);
        });

        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('variant_id')->nullable()->constrained('product_variants')->nullOnDelete();
            $table->integer('change');
            $table->string('reason')->nullable();
            $table->foreignId('order_id')->nullable();
            $table->foreignId('admin_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('bundle_allowed_products');
        Schema::dropIfExists('bundle_items');
        Schema::dropIfExists('bundles');
        Schema::dropIfExists('product_attribute_values');
        Schema::dropIfExists('attribute_values');
        Schema::dropIfExists('attributes');
        Schema::dropIfExists('product_tag');
        Schema::dropIfExists('tags');
        Schema::dropIfExists('addon_options');
        Schema::dropIfExists('addon_groups');
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('product_images');
        Schema::dropIfExists('products');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('fcm_tokens');
        Schema::dropIfExists('addresses');
        Schema::dropIfExists('areas');
        Schema::dropIfExists('governorates');
    }
};
