@extends('layouts.console')
@section('heading', 'Treatments')
@section('subheading', 'What appears on the website, and at what price')

@section('actions')
    <button data-modal-open="addService" class="btn btn-primary btn-sm">Add treatment</button>
@endsection

@section('content')
@php $currency = $settings['currency_symbol'] ?? 'Rs'; @endphp

<div class="space-y-5">
    <form method="GET" class="card-flat p-4 flex gap-3 items-end">
        <div class="flex-1"><label class="field-label">Search</label><input type="search" name="q" value="{{ request('q') }}" class="field !py-2 !text-sm"></div>
        <button class="btn btn-primary btn-sm">Search</button>
    </form>

    <x-ui.panel :padded="false">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead><tr><th>Treatment</th><th>Speciality</th><th>Price</th><th>Duration</th><th>Visible</th><th></th></tr></thead>
                <tbody>
                    @foreach($services as $service)
                        <tr>
                            <td>
                                <p class="font-medium">{{ $service->name }}</p>
                                <p class="text-xs text-pine/50 truncate max-w-[280px]">{{ $service->summary }}</p>
                            </td>
                            <td>{{ $service->speciality?->name ?? '—' }}</td>
                            <td class="font-mono text-xs">{{ $service->price_label }}</td>
                            <td class="font-mono text-xs">{{ $service->duration_minutes }} min</td>
                            <td>
                                <span class="badge badge-{{ $service->is_active ? 'jade' : 'slate' }}">{{ $service->is_active ? 'Live' : 'Hidden' }}</span>
                                @if($service->is_featured)<span class="badge badge-iris">Featured</span>@endif
                            </td>
                            <td class="text-right whitespace-nowrap">
                                <button data-modal-open="editService{{ $service->id }}" class="btn btn-ghost btn-sm">Edit</button>
                                <form action="{{ route('admin.content.services.destroy', $service) }}" method="POST" class="inline" data-confirm="Remove this treatment from the site?">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-ghost btn-sm text-rose-600">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-ui.panel>

    {{ $services->links() }}
</div>

{{-- Add modal --}}
<div id="addService" data-modal class="hidden fixed inset-0 z-[60] bg-ink/50 backdrop-blur-sm overflow-y-auto p-5">
    <div class="card-flat max-w-2xl mx-auto my-10 p-7">
        <div class="flex items-center justify-between mb-6">
            <h2 class="font-display text-xl font-semibold">Add a treatment</h2>
            <button data-modal-close class="text-2xl leading-none text-pine/40 hover:text-pine">&times;</button>
        </div>

        <form action="{{ route('admin.content.services.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @include('admin.content._service-fields', ['service' => new \App\Models\Service(['is_active' => true, 'duration_minutes' => 30])])
            <div class="flex gap-3">
                <button class="btn btn-primary">Add treatment</button>
                <button type="button" data-modal-close class="btn btn-ghost">Cancel</button>
            </div>
        </form>
    </div>
</div>

{{-- Edit modals --}}
@foreach($services as $service)
    <div id="editService{{ $service->id }}" data-modal class="hidden fixed inset-0 z-[60] bg-ink/50 backdrop-blur-sm overflow-y-auto p-5">
        <div class="card-flat max-w-2xl mx-auto my-10 p-7">
            <div class="flex items-center justify-between mb-6">
                <h2 class="font-display text-xl font-semibold">{{ $service->name }}</h2>
                <button data-modal-close class="text-2xl leading-none text-pine/40 hover:text-pine">&times;</button>
            </div>

            <form action="{{ route('admin.content.services.update', $service) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                @csrf @method('PATCH')
                @include('admin.content._service-fields')
                <div class="flex gap-3">
                    <button class="btn btn-primary">Save changes</button>
                    <button type="button" data-modal-close class="btn btn-ghost">Cancel</button>
                </div>
            </form>
        </div>
    </div>
@endforeach
@endsection
