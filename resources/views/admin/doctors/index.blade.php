@extends('layouts.console')
@section('heading', 'Practitioners')
@section('subheading', $doctors->total().' on the team')

@section('actions')
    @permission('doctors.manage')
        <a href="{{ route('admin.doctors.create') }}" class="btn btn-primary btn-sm">Add practitioner</a>
    @endpermission
@endsection

@section('content')
<div class="grid sm:grid-cols-2 xl:grid-cols-3 gap-5">
    @foreach($doctors as $doctor)
        <div class="card-flat p-5">
            <div class="flex items-start gap-4">
                <img src="{{ $doctor->user->avatar_url }}" alt="" class="w-14 h-14 rounded-2xl object-cover shrink-0">
                <div class="min-w-0 flex-1">
                    <p class="font-display font-semibold truncate">{{ $doctor->full_name }}</p>
                    <p class="text-xs text-pine/55 truncate">{{ $doctor->designation }}</p>
                    <div class="flex flex-wrap gap-1.5 mt-2">
                        <span class="badge badge-mint">{{ $doctor->speciality?->name ?? 'General' }}</span>
                        @unless($doctor->is_active)<span class="badge badge-slate">Inactive</span>@endunless
                    </div>
                </div>
            </div>

            <dl class="grid grid-cols-3 gap-2 mt-5 pt-4 border-t border-pine/8 text-center">
                @foreach(['Rating' => number_format((float) $doctor->rating, 1), 'Visits' => $doctor->appointments_count, 'Years' => $doctor->experience_years] as $label => $value)
                    <div>
                        <dt class="eyebrow text-pine/40 mb-1">{{ $label }}</dt>
                        <dd class="font-display font-semibold">{{ $value }}</dd>
                    </div>
                @endforeach
            </dl>

           <div class="flex gap-2 mt-4">
    <a href="{{ route('admin.doctors.show', $doctor) }}" class="btn btn-ghost btn-sm flex-1">Profile</a>
    @permission('schedules.manage')
        <a href="{{ route('admin.doctors.schedule', $doctor) }}" class="btn btn-ghost btn-sm flex-1">Hours</a>
    @endpermission
</div>

@permission('doctors.manage')
    <form action="{{ route('admin.doctors.destroy', $doctor) }}" method="POST" class="mt-2"
          data-confirm="Remove {{ $doctor->full_name }} from the team? Their profile and history will be hidden, not erased.">
        @csrf @method('DELETE')
        <button class="btn btn-sm w-full border border-rose-200 text-rose-700 hover:bg-rose-50">Remove practitioner</button>
    </form>
@endpermission
        </div>
    @endforeach
</div>

<div class="mt-6">{{ $doctors->links() }}</div>
@endsection
