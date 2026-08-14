@extends('layouts.portal')
@section('heading', 'Notifications')
@section('subheading', 'Updates about your appointments')

@section('content')
<div class="max-w-3xl space-y-5">
    <div class="flex justify-end">
        <form action="{{ route('patient.notifications.readAll') }}" method="POST">
            @csrf
            <button class="btn btn-ghost btn-sm">Mark all as read</button>
        </form>
    </div>

    <x-ui.panel :padded="false">
        @forelse($notifications as $notification)
            <div class="flex items-start gap-4 px-5 py-4 border-b border-pine/6 last:border-0 {{ $notification->read_at ? '' : 'bg-mint/12' }}">
                <span class="w-9 h-9 rounded-xl bg-pine text-mint grid place-items-center shrink-0">
                    <svg class="w-4 h-4" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M4 5h12v12H4zM7 3v4M13 3v4M4 9h12"/></svg>
                </span>

                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium">{{ $notification->data['headline'] ?? 'Update' }}</p>
                    <p class="text-xs text-pine/60 mt-1">
                        {{ $notification->data['when'] ?? '' }}
                        @if(!empty($notification->data['doctor'])) · {{ $notification->data['doctor'] }} @endif
                    </p>
                    <p class="text-xs text-pine/40 mt-1 font-mono">{{ $notification->created_at->diffForHumans() }}</p>
                </div>

                @if(!empty($notification->data['url']))
                    <a href="{{ $notification->data['url'] }}" class="btn btn-ghost btn-sm">Open</a>
                @endif

                @unless($notification->read_at)
                    <form action="{{ route('patient.notifications.read', $notification->id) }}" method="POST">
                        @csrf
                        <button class="text-xs text-pine/50 hover:text-jade transition">Mark read</button>
                    </form>
                @endunless
            </div>
        @empty
            <x-ui.empty title="Nothing to report" message="Appointment updates land here." icon="M10 3a4 4 0 0 0-4 4v3l-1.5 3h11L14 10V7a4 4 0 0 0-4-4z" />
        @endforelse
    </x-ui.panel>

    {{ $notifications->links() }}
</div>
@endsection
