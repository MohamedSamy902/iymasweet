<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\ClosedDate;
use App\Models\Coupon;
use App\Models\DeliverySchedule;
use App\Models\Governorate;
use App\Models\PaymentMethod;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CommerceSeeder extends Seeder
{
    public function run(): void
    {
        $cairo = Governorate::query()->updateOrCreate(
            ['name_ar' => 'القاهرة'],
            ['name_en' => 'Cairo', 'sort_order' => 1, 'is_active' => true]
        );
        $giza = Governorate::query()->updateOrCreate(
            ['name_ar' => 'الجيزة'],
            ['name_en' => 'Giza', 'sort_order' => 2, 'is_active' => true]
        );

        foreach ([
            [$cairo->id, 'مدينة نصر', 'Nasr City', 40, 500],
            [$cairo->id, 'مصر الجديدة', 'Heliopolis', 45, 500],
            [$cairo->id, 'المعادي', 'Maadi', 50, 600],
            [$giza->id, 'المهندسين', 'Mohandessin', 35, 450],
            [$giza->id, 'الدقي', 'Dokki', 35, 450],
            [$giza->id, 'الشيخ زايد', 'Sheikh Zayed', 70, 700],
        ] as $i => [$gov, $ar, $en, $fee, $free]) {
            Area::query()->updateOrCreate(
                ['governorate_id' => $gov, 'name_ar' => $ar],
                [
                    'name_en' => $en,
                    'delivery_fee' => $fee,
                    'min_order' => 100,
                    'eta_minutes' => 90,
                    'free_delivery_over' => $free,
                    'is_active' => true,
                ]
            );
        }

        for ($day = 0; $day <= 6; $day++) {
            DeliverySchedule::query()->updateOrCreate(
                ['day_of_week' => $day],
                [
                    'is_open' => $day !== 5, // Friday closed example? keep open - use day 5 open too
                    'opens_at' => '12:00:00',
                    'closes_at' => '22:00:00',
                    'slot_duration_minutes' => 120,
                    'order_cutoff_time' => '18:00:00',
                    'next_day_cutoff_time' => '22:00:00',
                ]
            );
        }

        // Ensure Friday is open for demo
        DeliverySchedule::query()->where('day_of_week', 5)->update(['is_open' => true]);

        ClosedDate::query()->updateOrCreate(
            ['date' => now()->addMonths(2)->startOfMonth()->toDateString()],
            ['reason_ar' => 'إجازة رسمية', 'reason_en' => 'Public holiday', 'is_full_day' => true]
        );

        PaymentMethod::query()->updateOrCreate(
            ['type' => 'cash', 'name_ar' => 'كاش عند التسليم'],
            [
                'name_en' => 'Cash on delivery',
                'instructions_ar' => 'ادفعي للمندوب عند الاستلام',
                'is_active' => true,
                'sort_order' => 1,
                'max_order' => 2000,
            ]
        );

        PaymentMethod::query()->updateOrCreate(
            ['type' => 'instapay', 'name_ar' => 'InstaPay'],
            [
                'name_en' => 'InstaPay',
                'account_number' => 'iyma@instapay',
                'account_holder' => 'Iyma Sweet',
                'instructions_ar' => 'حوّلي المبلغ ثم ارفعي صورة التحويل',
                'is_active' => true,
                'sort_order' => 2,
            ]
        );

        PaymentMethod::query()->updateOrCreate(
            ['type' => 'wallet', 'name_ar' => 'فودافون كاش'],
            [
                'name_en' => 'Vodafone Cash',
                'account_number' => '01000000000',
                'account_holder' => 'Iyma Sweet',
                'instructions_ar' => 'حوّلي على فودافون كاش وارفعى الإيصال',
                'is_active' => true,
                'sort_order' => 3,
            ]
        );

        Coupon::query()->updateOrCreate(
            ['code' => 'IYMA10'],
            [
                'type' => 'percent',
                'value' => 10,
                'min_order' => 150,
                'max_discount' => 80,
                'starts_at' => now()->subDay(),
                'ends_at' => now()->addMonths(3),
                'usage_limit' => 500,
                'per_user_limit' => 3,
                'is_active' => true,
            ]
        );

        Coupon::query()->updateOrCreate(
            ['code' => 'FREESHIP'],
            [
                'type' => 'free_delivery',
                'value' => 0,
                'min_order' => 200,
                'starts_at' => now()->subDay(),
                'ends_at' => now()->addMonths(3),
                'is_active' => true,
            ]
        );

        User::query()->updateOrCreate(
            ['phone' => '01012345678'],
            [
                'name' => 'سارة أحمد',
                'email' => 'sara@example.com',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );
    }
}
