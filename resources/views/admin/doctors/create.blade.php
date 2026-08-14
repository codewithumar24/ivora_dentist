@extends('layouts.console')
@section('heading', 'Add a practitioner')
@section('subheading', 'A Mon–Sat 9–5 schedule is created automatically')

@section('content')
<form action="{{ route('admin.doctors.store') }}" method="POST" enctype="multipart/form-data" class="max-w-5xl">
    @csrf
    @include('admin.doctors._form', ['doctor' => $doctor ?? new \App\Models\Doctor])

    <div class="flex gap-3 mt-5">
        <button class="btn btn-primary">Add practitioner</button>
        <a href="{{ route('admin.doctors.index') }}" class="btn btn-ghost">Cancel</a>
    </div>
</form>
@endsection
