@extends('layouts.console')
@section('heading', 'Enquiries')
@section('subheading', $unread.' unread')

@section('content')
<div class="space-y-5">
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('admin.inbox.index') }}" class="btn btn-sm {{ empty($filters['status']) ? 'btn-primary' : 'btn-ghost' }}">All</a>
        @foreach(['new' => 'Unread', 'read' => 'Read', 'replied' => 'Replied', 'archived' => 'Archived'] as $status => $label)
            <a href="{{ route('admin.inbox.index', ['status' => $status]) }}"
               class="btn btn-sm {{ ($filters['status'] ?? '') === $status ? 'btn-primary' : 'btn-ghost' }}">{{ $label }}</a>
        @endforeach
    </div>

    <x-ui.panel :padded="false">
        @forelse($messages as $message)
            <a href="{{ route('admin.inbox.show', $message) }}"
               class="flex items-start gap-4 px-5 py-4 border-b border-pine/6 last:border-0 hover:bg-mint/12 transition {{ $message->status === 'new' ? 'bg-mint/10' : '' }}">
                <span class="w-9 h-9 rounded-xl bg-pine text-mint grid place-items-center shrink-0 font-mono text-xs">
                    {{ Str::upper(Str::substr($message->name, 0, 2)) }}
                </span>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2.5">
                        <p class="font-medium truncate">{{ $message->subject ?: 'No subject' }}</p>
                        @if($message->status === 'new')<span class="badge badge-jade">New</span>@endif
                    </div>
                    <p class="text-xs text-pine/55 mt-0.5">{{ $message->name }} · {{ $message->email }}</p>
                    <p class="text-sm text-pine/75 mt-1.5 truncate">{{ $message->message }}</p>
                </div>
                <span class="text-xs text-pine/45 font-mono whitespace-nowrap">{{ $message->created_at->diffForHumans() }}</span>
            </a>
        @empty
            <x-ui.empty title="Inbox is empty" message="Messages from the website contact form land here." />
        @endforelse
    </x-ui.panel>

    {{ $messages->links() }}
</div>
@endsection
