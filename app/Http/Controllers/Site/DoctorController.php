<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\Speciality;
use App\Services\SlotService;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    public function index(Request $request)
    {
        return view('site.doctors.index', [
            'specialities' => Speciality::active()->get(),
            'doctors' => Doctor::with('user', 'speciality')->active()
                ->when($request->speciality, fn ($q, $slug) => $q->whereRelation('speciality', 'slug', $slug))
                ->when($request->q, fn ($q, $term) => $q->whereRelation('user', 'name', 'like', "%{$term}%"))
                ->orderByDesc('is_featured')->orderBy('sort_order')
                ->paginate(9)->withQueryString(),
            'active' => $request->speciality,
        ]);
    }

    public function show(Doctor $doctor, SlotService $slots)
    {
        $doctor->load('user', 'speciality', 'services', 'schedules', 'reviews.patient');

        return view('site.doctors.show', [
            'doctor' => $doctor,
            'days' => $slots->upcomingOpenDays($doctor, 10),
            'nextAvailable' => $slots->nextAvailable($doctor),
            'reviews' => $doctor->reviews()->approved()->with('patient')->latest()->take(6)->get(),
        ]);
    }
}
