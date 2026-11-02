<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedTinyInteger('rating');
            $table->text('comment')->nullable();
            $table->json('images')->nullable();
            $table->string('status')->default('pending')->index();
            $table->text('admin_reply')->nullable();
            $table->timestamps();
        });

        Schema::create('favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['user_id', 'product_id']);
        });

        Schema::create('stock_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->timestamp('notified_at')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'product_id']);
        });

        Schema::create('notification_templates', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('title_ar');
            $table->string('title_en')->nullable();
            $table->text('body_ar');
            $table->text('body_en')->nullable();
            $table->json('variables')->nullable();
            $table->json('channels')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('icon')->nullable();
            $table->string('deep_link')->nullable();
            $table->timestamps();
        });

        Schema::create('notification_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('category');
            $table->boolean('push')->default(true);
            $table->boolean('in_app')->default(true);
            $table->boolean('email')->default(false);
            $table->timestamps();
            $table->unique(['user_id', 'category']);
        });

        Schema::create('notification_logs', function (Blueprint $table) {
            $table->id();
            $table->string('notifiable_type')->nullable();
            $table->unsignedBigInteger('notifiable_id')->nullable();
            $table->string('event')->nullable();
            $table->string('channel')->nullable();
            $table->string('status')->default('queued');
            $table->text('error')->nullable();
            $table->timestamps();
            $table->index(['notifiable_type', 'notifiable_id']);
        });

        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title_ar');
            $table->string('title_en')->nullable();
            $table->longText('content_ar')->nullable();
            $table->longText('content_en')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('seo_title_ar')->nullable();
            $table->string('seo_title_en')->nullable();
            $table->text('seo_description_ar')->nullable();
            $table->text('seo_description_en')->nullable();
            $table->timestamps();
        });

        Schema::create('home_sections', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('title_ar')->nullable();
            $table->string('title_en')->nullable();
            $table->string('subtitle_ar')->nullable();
            $table->string('subtitle_en')->nullable();
            $table->json('config')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('image_mobile')->nullable();
            $table->string('image_desktop')->nullable();
            $table->string('title_ar')->nullable();
            $table->string('title_en')->nullable();
            $table->string('subtitle_ar')->nullable();
            $table->string('subtitle_en')->nullable();
            $table->string('cta_text_ar')->nullable();
            $table->string('cta_text_en')->nullable();
            $table->string('link')->nullable();
            $table->string('position')->default('home_hero');
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->string('category')->nullable();
            $table->string('question_ar');
            $table->string('question_en')->nullable();
            $table->text('answer_ar');
            $table->text('answer_en')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('location');
            $table->string('name')->nullable();
            $table->timestamps();
            $table->unique('location');
        });

        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')->constrained()->cascadeOnDelete();
            $table->string('label_ar');
            $table->string('label_en')->nullable();
            $table->string('url')->nullable();
            $table->string('route')->nullable();
            $table->string('icon')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('social_links', function (Blueprint $table) {
            $table->id();
            $table->string('platform');
            $table->string('url');
            $table->string('icon')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('chat_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->json('context')->nullable();
            $table->timestamp('last_activity')->nullable();
            $table->timestamps();
        });

        Schema::create('chatbot_intents', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('name_ar');
            $table->string('name_en')->nullable();
            $table->json('keywords')->nullable();
            $table->json('patterns')->nullable();
            $table->unsignedInteger('priority')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('chatbot_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('intent_id')->constrained('chatbot_intents')->cascadeOnDelete();
            $table->text('text_ar');
            $table->text('text_en')->nullable();
            $table->json('buttons')->nullable();
            $table->string('action')->nullable();
            $table->unsignedInteger('weight')->default(1);
            $table->timestamps();
        });

        Schema::create('chatbot_synonyms', function (Blueprint $table) {
            $table->id();
            $table->string('canonical');
            $table->string('synonym');
            $table->timestamps();
            $table->unique(['canonical', 'synonym']);
        });

        Schema::create('chatbot_logs', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->nullable()->index();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->text('message');
            $table->string('detected_intent')->nullable();
            $table->decimal('confidence', 5, 2)->nullable();
            $table->boolean('understood')->default(false);
            $table->json('meta')->nullable();
            $table->timestamps();
        });

        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->string('log_name')->nullable();
            $table->text('description');
            $table->nullableMorphs('subject');
            $table->nullableMorphs('causer');
            $table->json('properties')->nullable();
            $table->string('event')->nullable();
            $table->uuid('batch_uuid')->nullable();
            $table->timestamps();
            $table->index('log_name');
        });

        if (! Schema::hasTable('notifications')) {
            Schema::create('notifications', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('type');
                $table->morphs('notifiable');
                $table->text('data');
                $table->timestamp('read_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('chatbot_logs');
        Schema::dropIfExists('chatbot_synonyms');
        Schema::dropIfExists('chatbot_responses');
        Schema::dropIfExists('chatbot_intents');
        Schema::dropIfExists('chat_sessions');
        Schema::dropIfExists('social_links');
        Schema::dropIfExists('menu_items');
        Schema::dropIfExists('menus');
        Schema::dropIfExists('faqs');
        Schema::dropIfExists('banners');
        Schema::dropIfExists('home_sections');
        Schema::dropIfExists('pages');
        Schema::dropIfExists('notification_logs');
        Schema::dropIfExists('notification_preferences');
        Schema::dropIfExists('notification_templates');
        Schema::dropIfExists('stock_alerts');
        Schema::dropIfExists('favorites');
        Schema::dropIfExists('reviews');
    }
};
