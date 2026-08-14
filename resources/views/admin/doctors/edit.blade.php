@extends('layouts.console')
@section('heading', 'Edit '.$doctor->full_name)
@section('subheading', $doctor->designation)

@section('content')
<form action="{{ route('admin.doctors.update', $doctor) }}" method="POST" enctype="multipart/form-data" class="max-w-5xl">
    @csrf @method('PUT')
    @include('admin.doctors._form')

    <div class="flex flex-wrap gap-3 mt-5">
        <button class="btn btn-primary">Save changes</button>
        <a href="{{ route('admin.doctors.show', $doctor) }}" class="btn btn-ghost">Cancel</a>
    </div>
</form>

<form action="{{ route('admin.doctors.destroy', $doctor) }}" method="POST" class="max-w-5xl mt-4"
      data-confirm="Deactivate this practitioner? Their past appointments are kept.">
    @csrf @method('DELETE')
    <button class="btn btn-sm border border-rose-200 text-rose-700 hover:bg-rose-50">Deactivate</button>
</form>
@endsection
