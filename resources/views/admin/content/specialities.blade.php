@extends('layouts.console')
@section('heading', 'Specialities')
@section('subheading', 'The categories treatments and practitioners sit under')

@section('content')
<div class="grid lg:grid-cols-[1fr_340px] gap-6 items-start">
    <x-ui.panel :padded="false">
        <table class="data-table">
            <thead><tr><th>Speciality</th><th>Treatments</th><th>Practitioners</th><th></th></tr></thead>
            <tbody>
                @foreach($specialities as $speciality)
                    <tr>
                        <td>
                            <p class="font-medium">{{ $speciality->name }}</p>
                            <p class="text-xs text-pine/50 truncate max-w-[320px]">{{ $speciality->description }}</p>
                        </td>
                        <td class="font-mono text-xs">{{ $speciality->services_count }}</td>
                        <td class="font-mono text-xs">{{ $speciality->doctors_count }}</td>
                        <td class="text-right">
                            <form action="{{ route('admin.content.specialities.destroy', $speciality) }}" method="POST" data-confirm="Delete this speciality?">
                                @csrf @method('DELETE')
                                <button class="btn btn-ghost btn-sm text-rose-600">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-ui.panel>

    <x-ui.panel title="Add a speciality">
        <form action="{{ route('admin.content.specialities.store') }}" method="POST" class="space-y-4">
            @csrf
            <x-ui.field label="Name" name="name" required />
            <x-ui.field label="Description" name="description" type="textarea" />
            <x-ui.field label="Sort order" name="sort_order" type="number" value="0" />
            <button class="btn btn-primary w-full">Add speciality</button>
        </form>
    </x-ui.panel>
</div>
@endsection
