<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Services\SettingsService;
use App\Services\UiTextService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PhaseOneFoundationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_home_page_renders_with_brand_and_rtl(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Home')
            ->has('settings.brand')
            ->has('ui')
            ->where('locale', 'ar')
            ->where('dir', 'rtl')
        );
    }

    public function test_helper_t_reads_ui_texts(): void
    {
        $this->assertSame('الرئيسية', t('layout.nav_home'));
        $this->assertSame('Home', t('layout.nav_home', null, 'en'));
    }

    public function test_settings_theme_variables_are_available(): void
    {
        $vars = app(SettingsService::class)->themeCssVariables();

        $this->assertSame('#F2A0B8', $vars['--color-primary']);
        $this->assertSame('#FFFAF7', $vars['--color-cream']);
    }

    public function test_admin_can_open_filament_login(): void
    {
        $response = $this->get('/admin/login');

        $response->assertOk();
    }

    public function test_admin_can_authenticate_and_open_dashboard(): void
    {
        $admin = Admin::query()->where('email', 'admin@iymasweet.com')->firstOrFail();

        $this->actingAs($admin, 'admin')
            ->get('/admin')
            ->assertOk();
    }

    public function test_admin_settings_and_ui_texts_pages_load(): void
    {
        $admin = Admin::query()->where('email', 'admin@iymasweet.com')->firstOrFail();

        $this->actingAs($admin, 'admin')
            ->get('/admin/manage-settings')
            ->assertOk();

        $this->actingAs($admin, 'admin')
            ->get('/admin/ui-texts')
            ->assertOk();
    }

    public function test_ui_text_cache_clears_on_update(): void
    {
        $service = app(UiTextService::class);
        $this->assertSame('الرئيسية', $service->get('layout.nav_home'));

        $text = \App\Models\UiText::query()
            ->where('group', 'layout')
            ->where('key', 'nav_home')
            ->firstOrFail();

        $text->update(['value_ar' => 'الرئيسيه']);

        $this->assertSame('الرئيسيه', $service->get('layout.nav_home'));
    }
}
