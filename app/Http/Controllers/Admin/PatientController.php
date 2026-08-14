<?php

namespace App\Http\Controllers\Admin;

use App\Exports\PatientsExport;
use App\Http\Controllers\Controller;
use App\Mail\WelcomeToIvora;
use App\Models\ActivityLog;
use App\Models\PatientProfile;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class PatientController extends Controller
{
    public function __construct()
    {
        $this->middleware('can.do:patients.view')->only(['index', 'show']);
        $this->middleware('can.do:patients.create')->only(['create', 'store']);
        $this->middleware('can.do:patients.update')->only(['edit', 'update', 'toggle']);
        $this->middleware('can.do:patients.delete')->only('destroy');
        $this->middleware('can.do:patients.export')->only('export');
    }

    public function index(Request $request)
    {
        return view('admin.patients.index', [
            'patients' => User::patients()->with('patientProfile')
                ->search($request->q)
                ->when($request->status, fn ($q, $s) => $q->where('status', $s))
                ->withCount('appointments')
                ->latest()->paginate(15)->withQueryString(),
            'filters' => $request->only('q', 'status'),
        ]);
    }

    public function create()
    {
        return view('admin.patients.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:32', 'unique:users,phone'],
            'gender' => ['nullable', 'in:male,female,other'],
            'date_of_birth' => ['nullable', 'date'],
            'city' => ['nullable', 'string', 'max:120'],
            'address' => ['nullable', 'string', 'max:500'],
            'blood_group' => ['nullable', 'string', 'max:8'],
            'allergies' => ['nullable', 'string', 'max:1000'],
        ]);

        $password = Str::password(10);

        $user = DB::transaction(function () use ($data, $password) {
            $user = User::create(collect($data)->only([
                'name', 'email', 'phone', 'gender', 'date_of_birth', 'city', 'address',
            ])->all() + [
                'role_id' => Role::where('slug', Role::PATIENT)->value('id'),
                'password' => $password,
                'status' => 'active',
            ]);

            PatientProfile::create([
                'user_id' => $user->id,
                'mrn' => PatientProfile::nextMrn(),
                'blood_group' => $data['blood_group'] ?? null,
                'allergies' => $data['allergies'] ?? null,
            ]);

            return $user;
        });

        Mail::to($user->email)->queue(new WelcomeToIvora($user, $password));
        ActivityLog::record('created', "Registered patient {$user->name}", $user);

        return redirect()->route('admin.patients.show', $user)
            ->with('status', 'Patient added and sent their portal login.');
    }

    public function show(User $patient)
    {
        abort_unless($patient->hasRole(Role::PATIENT), 404);

        $patient->load('patientProfile');

        return view('admin.patients.show', [
            'patient' => $patient,
            'appointments' => $patient->appointments()->with('doctor.user', 'service')->latest('scheduled_on')->take(10)->get(),
            'records' => $patient->medicalRecords()->with('doctor.user', 'prescription.items')->latest('visited_on')->get(),
            'invoices' => $patient->invoices()->latest('issued_on')->get(),
        ]);
    }

    public function edit(User $patient)
    {
        return view('admin.patients.edit', ['patient' => $patient->load('patientProfile')]);
    }

    public function update(Request $request, User $patient)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'unique:users,email,'.$patient->id],
            'phone' => ['required', 'string', 'max:32', 'unique:users,phone,'.$patient->id],
            'gender' => ['nullable', 'in:male,female,other'],
            'date_of_birth' => ['nullable', 'date'],
            'city' => ['nullable', 'string', 'max:120'],
            'address' => ['nullable', 'string', 'max:500'],
            'status' => ['required', 'in:active,suspended,pending'],
            'profile' => ['array'],
        ]);

        $patient->update(collect($data)->except('profile')->all());

        if ($request->filled('profile')) {
            PatientProfile::updateOrCreate(
                ['user_id' => $patient->id],
                $request->input('profile') + ['mrn' => $patient->patientProfile?->mrn ?? PatientProfile::nextMrn()]
            );
        }

        ActivityLog::record('updated', "Updated patient {$patient->name}", $patient);

        return back()->with('status', 'Patient record saved.');
    }

    public function destroy(User $patient)
    {
        $patient->delete();
        ActivityLog::record('deleted', "Archived patient {$patient->name}", $patient);

        return redirect()->route('admin.patients.index')->with('status', 'Patient archived.');
    }

    public function export(Request $request)
    {
        return Excel::download(new PatientsExport($request->q), 'ivora-patients-'.now()->format('Y-m-d').'.xlsx');
    }
}
