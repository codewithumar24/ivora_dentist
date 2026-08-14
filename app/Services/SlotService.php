<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\DoctorTimeOff;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Turns a doctor's weekly schedule into bookable slots for a given date,
 * minus breaks, minus time off, minus anything already booked, minus
 * anything that starts too soon to be realistic.
 */
class SlotService
{
    public function availableFor(Doctor $doctor, Carbon $date, ?int $ignoreAppointmentId = null): Collection
    {
        if ($this->isClosed($doctor, $date)) {
            return collect();
        }

        $schedules = $doctor->schedules()
            ->where('day_of_week', $date->dayOfWeek)
            ->where('is_active', true)
            ->orderBy('starts_at')
            ->get();

        if ($schedules->isEmpty()) {
            return collect();
        }

        $taken = Appointment::query()
            ->where('doctor_id', $doctor->id)
            ->whereDate('scheduled_on', $date->toDateString())
            ->whereIn('status', [Appointment::PENDING, Appointment::CONFIRMED, Appointment::COMPLETED])
            ->when($ignoreAppointmentId, fn ($q) => $q->where('id', '!=', $ignoreAppointmentId))
            ->pluck('starts_at')
            ->map(fn ($t) => substr($t, 0, 5))
            ->all();

        $partialClosures = DoctorTimeOff::query()
            ->where(fn ($q) => $q->where('doctor_id', $doctor->id)->orWhereNull('doctor_id'))
            ->whereDate('starts_on', '<=', $date->toDateString())
            ->whereDate('ends_on', '>=', $date->toDateString())
            ->whereNotNull('starts_at')
            ->get();

        $cutoff = now()->addMinutes(config('clinic.min_notice_minutes'));
        $slots = collect();

        foreach ($schedules as $schedule) {
            $cursor = $date->copy()->setTimeFromTimeString($schedule->starts_at);
            $end = $date->copy()->setTimeFromTimeString($schedule->ends_at);
            $length = $schedule->slot_minutes ?: config('clinic.slot_minutes');

            while ($cursor->copy()->addMinutes($length)->lessThanOrEqualTo($end)) {
                $slotStart = $cursor->copy();
                $slotEnd = $cursor->copy()->addMinutes($length);
                $label = $slotStart->format('H:i');

                $blocked = in_array($label, $taken, true)
                    || $slotStart->lessThan($cutoff)
                    || $this->overlapsBreak($schedule, $slotStart, $slotEnd)
                    || $this->overlapsClosure($partialClosures, $date, $slotStart, $slotEnd);

                if (! $blocked) {
                    $slots->push([
                        'value' => $label,
                        'label' => $slotStart->format('g:i A'),
                        'ends_at' => $slotEnd->format('H:i'),
                        'period' => $slotStart->hour < 12 ? 'Morning' : ($slotStart->hour < 17 ? 'Afternoon' : 'Evening'),
                    ]);
                }

                $cursor->addMinutes($length);
            }
        }

        return $slots->unique('value')->values();
    }

    /** The next N days that still have at least one open slot. */
    public function upcomingOpenDays(Doctor $doctor, int $days = 14): Collection
    {
        $out = collect();

        for ($i = 0; $i < $days; $i++) {
            $date = today()->addDays($i);
            $count = $this->availableFor($doctor, $date)->count();

            $out->push([
                'date' => $date->toDateString(),
                'weekday' => $date->format('D'),
                'day' => $date->format('j'),
                'month' => $date->format('M'),
                'open' => $count,
                'is_today' => $date->isToday(),
            ]);
        }

        return $out;
    }

    public function nextAvailable(Doctor $doctor, int $searchDays = 21): ?array
    {
        for ($i = 0; $i < $searchDays; $i++) {
            $date = today()->addDays($i);
            $slot = $this->availableFor($doctor, $date)->first();

            if ($slot) {
                return ['date' => $date, 'slot' => $slot];
            }
        }

        return null;
    }

    public function isSlotFree(Doctor $doctor, Carbon $date, string $time, ?int $ignoreId = null): bool
    {
        return $this->availableFor($doctor, $date, $ignoreId)
            ->contains(fn ($slot) => $slot['value'] === substr($time, 0, 5));
    }

    protected function isClosed(Doctor $doctor, Carbon $date): bool
    {
        if ($date->lessThan(today()) || $date->greaterThan(today()->addDays(config('clinic.max_advance_days')))) {
            return true;
        }

        return DoctorTimeOff::query()
            ->where(fn ($q) => $q->where('doctor_id', $doctor->id)->orWhereNull('doctor_id'))
            ->whereDate('starts_on', '<=', $date->toDateString())
            ->whereDate('ends_on', '>=', $date->toDateString())
            ->whereNull('starts_at')
            ->exists();
    }

    protected function overlapsBreak($schedule, Carbon $start, Carbon $end): bool
    {
        if (! $schedule->break_starts_at || ! $schedule->break_ends_at) {
            return false;
        }

        $breakStart = $start->copy()->setTimeFromTimeString($schedule->break_starts_at);
        $breakEnd = $start->copy()->setTimeFromTimeString($schedule->break_ends_at);

        return $start->lessThan($breakEnd) && $end->greaterThan($breakStart);
    }

    protected function overlapsClosure(Collection $closures, Carbon $date, Carbon $start, Carbon $end): bool
    {
        foreach ($closures as $closure) {
            $from = $date->copy()->setTimeFromTimeString($closure->starts_at);
            $to = $date->copy()->setTimeFromTimeString($closure->ends_at ?? '23:59');

            if ($start->lessThan($to) && $end->greaterThan($from)) {
                return true;
            }
        }

        return false;
    }
}
