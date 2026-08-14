<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\WelcomeToIvora;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\DoctorTimeOff;
use App\Models\Role;
use App\Models\Service;
use App\Models\Speciality;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class DoctorController extends Controller
{
    public function __construct()
    {
        $this->middleware('can.do:doctors.view')->only(['index', 'show']);
        $this->middleware('can.do:doctors.manage')->only(['create', 'store', 'edit', 'update', 'destroy']);
        $this->middleware('can.do:schedules.manage')->only(['schedule', 'saveSchedule', 'storeTimeOff', 'destroyTimeOff']);
    }

    public function index(Request $request)
    {
        return view('admin.doctors.index', [
            'doctors' => Doctor::with('user', 'speciality')->withCount('appointments')
                ->when($request->q, fn ($q, $term) => $q->whereRelation('user', 'name', 'like', "%{$term}%"))
                ->orderBy('sort_order')->paginate(12)->withQueryString(),
        ]);
    }

    public function create()
    {
        return view('admin.doctors.create', [
            'specialities' => Speciality::active()->get(),
            'services' => Service::active()->get(),
            'roles' => Role::whereIn('slug', [Role::DENTIST, Role::OWNER])->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:32', 'unique:users,phone'],
            'role_id' => ['required', 'exists:roles,id'],
            'speciality_id' => ['nullable', 'exists:specialities,id'],
            'title' => ['required', 'string', 'max:20'],
            'designation' => ['nullable', 'string', 'max:150'],
            'qualifications' => ['nullable', 'string', 'max:255'],
            'license_no' => ['nullable', 'string', 'max:80'],
            'experience_years' => ['nullable', 'integer', 'min:0', 'max:70'],
            'consultation_fee' => ['nullable', 'numeric', 'min:0'],
            'short_bio' => ['nullable', 'string', 'max:500'],
            'biography' => ['nullable', 'string'],
            'languages' => ['nullable', 'array'],
            'services' => ['nullable', 'array'],
            'avatar' => ['nullable', 'image', 'max:2048'],
            'is_featured' => ['nullable', 'boolean'],
        ]);

        $password = Str::password(12);

        $doctor = DB::transaction(function () use ($request, $data, $password) {
            $user = User::create([
                'role_id' => $data['role_id'],
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'password' => $password,
                'status' => 'active',
                'email_verified_at' => now(),
                'avatar' => $request->hasFile('avatar')
                    ? $request->file('avatar')->store('avatars', 'public') : null,
            ]);

            $doctor = Doctor::create([
                'user_id' => $user->id,
                'speciality_id' => $data['speciality_id'] ?? null,
                'title' => $data['title'],
                'slug' => Str::slug($data['title'].'-'.$data['name'].'-'.Str::random(4)),
                'designation' => $data['designation'] ?? null,
                'qualifications' => $data['qualifications'] ?? null,
                'license_no' => $data['license_no'] ?? null,
                'experience_years' => $data['experience_years'] ?? 0,
                'consultation_fee' => $data['consultation_fee'] ?? 0,
                'short_bio' => $data['short_bio'] ?? null,
                'biography' => $data['biography'] ?? null,
                'languages' => $data['languages'] ?? ['English', 'Urdu'],
                'is_featured' => $request->boolean('is_featured'),
            ]);

            $doctor->services()->sync($data['services'] ?? []);

            // A sensible Mon–Sat default so the doctor is bookable immediately.
            foreach ([1, 2, 3, 4, 5, 6] as $day) {
                DoctorSchedule::create([
                    'doctor_id' => $doctor->id,
                    'day_of_week' => $day,
                    'starts_at' => '09:00',
                    'ends_at' => '17:00',
                    'break_starts_at' => '13:00',
                    'break_ends_at' => '14:00',
                    'slot_minutes' => config('clinic.slot_minutes'),
                ]);
            }

            return $doctor;
        });

        Mail::to($data['email'])->queue(new WelcomeToIvora($doctor->user, $password));

        return redirect()->route('admin.doctors.index')
            ->with('status', 'Practitioner added with a Mon–Sat 9–5 schedule you can adjust.');
    }

    public function show(Doctor $doctor)
    {
        $doctor->load('user', 'speciality', 'services', 'schedules', 'timeOffs');

        return view('admin.doctors.show', [
            'doctor' => $doctor,
            'appointments' => $doctor->appointments()->with('patient')->latest('scheduled_on')->take(10)->get(),
        ]);
    }

    public function edit(Doctor $doctor)
    {
        return view('admin.doctors.edit', [
            'doctor' => $doctor->load('user', 'services'),
            'specialities' => Speciality::active()->get(),
            'services' => Service::active()->get(),
        ]);
    }

    public function update(Request $request, Doctor $doctor)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'unique:users,email,'.$doctor->user_id],
            'phone' => ['required', 'string', 'max:32', 'unique:users,phone,'.$doctor->user_id],
            'speciality_id' => ['nullable', 'exists:specialities,id'],
            'title' => ['required', 'string', 'max:20'],
            'designation' => ['nullable', 'string', 'max:150'],
            'qualifications' => ['nullable', 'string', 'max:255'],
            'license_no' => ['nullable', 'string', 'max:80'],
            'experience_years' => ['nullable', 'integer', 'min:0'],
            'consultation_fee' => ['nullable', 'numeric', 'min:0'],
            'short_bio' => ['nullable', 'string', 'max:500'],
            'biography' => ['nullable', 'string'],
            'services' => ['nullable', 'array'],
            'avatar' => ['nullable', 'image', 'max:2048'],
        ]);

        $doctor->user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'avatar' => $request->hasFile('avatar')
                ? $request->file('avatar')->store('avatars', 'public')
                : $doctor->user->avatar,
        ]);

        $doctor->update(collect($data)->except(['name', 'email', 'phone', 'services', 'avatar'])->all() + [
            'is_featured' => $request->boolean('is_featured'),
            'is_active' => $request->boolean('is_active'),
            'accepts_online_booking' => $request->boolean('accepts_online_booking'),
        ]);

        $doctor->services()->sync($data['services'] ?? []);

        return back()->with('status', 'Practitioner profile saved.');
    }

    public function destroy(Doctor $doctor)
    {
        $doctor->delete();

        return redirect()->route('admin.doctors.index')->with('status', 'Practitioner deactivated.');
    }

    public function schedule(Doctor $doctor)
    {
        return view('admin.doctors.schedule', [
            'doctor' => $doctor->load('user', 'schedules', 'timeOffs'),
            'days' => DoctorSchedule::DAYS,
        ]);
    }

    public function saveSchedule(Request $request, Doctor $doctor)
    {
        $request->validate([
            'days' => ['required', 'array'],
            'days.*.starts_at' => ['nullable', 'date_format:H:i'],
            'days.*.ends_at' => ['nullable', 'date_format:H:i', 'after:days.*.starts_at'],
        ]);

        foreach ($request->input('days') as $day => $row) {
            if (empty($row['active']) || empty($row['starts_at']) || empty($row['ends_at'])) {
                $doctor->schedules()->where('day_of_week', $day)->delete();

                continue;
            }

            $doctor->schedules()->updateOrCreate(['day_of_week' => $day], [
                'starts_at' => $row['starts_at'],
                'ends_at' => $row['ends_at'],
                'break_starts_at' => $row['break_starts_at'] ?? null,
                'break_ends_at' => $row['break_ends_at'] ?? null,
                'slot_minutes' => $row['slot_minutes'] ?? config('clinic.slot_minutes'),
                'is_active' => true,
            ]);
        }

        return back()->with('status', 'Working hours saved. New slots are live on the website.');
    }

    public function storeTimeOff(Request $request, Doctor $doctor)
    {
        $data = $request->validate([
            'starts_on' => ['required', 'date'],
            'ends_on' => ['required', 'date', 'after_or_equal:starts_on'],
            'starts_at' => ['nullable', 'date_format:H:i'],
            'ends_at' => ['nullable', 'date_format:H:i'],
            'reason' => ['nullable', 'string', 'max:180'],
        ]);

        $doctor->timeOffs()->create($data);

        return back()->with('status', 'Time off blocked out. Those slots are now hidden.');
    }

    public function destroyTimeOff(Doctor $doctor, DoctorTimeOff $timeOff)
    {
        $timeOff->delete();

        return back()->with('status', 'Time off removed.');
    }
}
