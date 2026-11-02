<?php

namespace App\Services;

use App\Enums\AvailabilityType;
use App\Models\ClosedDate;
use App\Models\DeliverySchedule;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class DeliverySlotService
{
    /**
     * @param  array<int, AvailabilityType|string|null>  $itemAvailabilities
     * @return array<int, array{date: string, label: string, slots: array<int, array{label: string, start: string, end: string}>}>
     */
    public function availableDays(array $itemAvailabilities = [], int $days = 10, ?int $leadTimeHours = null): array
    {
        $start = Carbon::today(config('app.timezone'));
        $maxRank = 1;

        foreach ($itemAvailabilities as $availability) {
            $value = $availability instanceof AvailabilityType ? $availability->value : $availability;
            $rank = match ($value) {
                AvailabilityType::AvailableTomorrow->value => 2,
                AvailabilityType::PreOrder->value => 3,
                default => 1,
            };
            $maxRank = max($maxRank, $rank);
        }

        if ($maxRank >= 2) {
            $start = $start->copy()->addDay();
        }

        if ($leadTimeHours) {
            $earliest = Carbon::now(config('app.timezone'))->addHours($leadTimeHours)->startOfDay();
            if ($earliest->gt($start)) {
                $start = $earliest;
            }
        }

        $closed = ClosedDate::query()->pluck('date')->map(fn ($d) => Carbon::parse($d)->toDateString())->all();
        $schedules = DeliverySchedule::query()->get()->keyBy('day_of_week');
        $result = [];

        foreach (CarbonPeriod::create($start, $start->copy()->addDays($days)) as $day) {
            /** @var Carbon $day */
            $date = $day->toDateString();
            if (in_array($date, $closed, true)) {
                continue;
            }

            $schedule = $schedules->get($day->dayOfWeek);
            if (! $schedule || ! $schedule->is_open) {
                continue;
            }

            // Cut-off for same-day
            if ($day->isToday() && $schedule->order_cutoff_time) {
                $cutoff = Carbon::parse($date.' '.$schedule->order_cutoff_time, config('app.timezone'));
                if (now(config('app.timezone'))->gt($cutoff)) {
                    continue;
                }
            }

            $slots = $this->buildSlots($schedule, $date);
            if ($slots === []) {
                continue;
            }

            $result[] = [
                'date' => $date,
                'label' => $day->locale(app()->getLocale())->translatedFormat('D d M'),
                'slots' => $slots,
            ];
        }

        return $result;
    }

    /** @return array<int, array{label: string, start: string, end: string}> */
    protected function buildSlots(DeliverySchedule $schedule, string $date): array
    {
        if (! $schedule->opens_at || ! $schedule->closes_at) {
            return [['label' => 'طوال اليوم', 'start' => '10:00', 'end' => '22:00']];
        }

        $duration = max(30, (int) $schedule->slot_duration_minutes);
        $cursor = Carbon::parse($date.' '.$schedule->opens_at);
        $end = Carbon::parse($date.' '.$schedule->closes_at);
        $slots = [];

        while ($cursor->copy()->addMinutes($duration)->lte($end)) {
            $slotEnd = $cursor->copy()->addMinutes($duration);
            $slots[] = [
                'label' => $cursor->format('H:i').' – '.$slotEnd->format('H:i'),
                'start' => $cursor->format('H:i:s'),
                'end' => $slotEnd->format('H:i:s'),
            ];
            $cursor = $slotEnd;
        }

        return $slots;
    }
}
