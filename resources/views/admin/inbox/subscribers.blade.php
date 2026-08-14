@extends('layouts.console')
@section('heading', 'Mailing list')
@section('subheading', $active.' active subscribers')

@section('content')
<div class="grid lg:grid-cols-[1fr_340px] gap-6 items-start">
    <div class="space-y-5">
        <form method="GET" class="card-flat p-4 flex gap-3 items-end">
            <div class="flex-1"><label class="field-label">Search</label><input type="search" name="q" value="{{ request('q') }}" class="field !py-2 !text-sm"></div>
            <button class="btn btn-primary btn-sm">Search</button>
        </form>

        <x-ui.panel :padded="false">
            <table class="data-table">
                <thead><tr><th>Email</th><th>Name</th><th>Joined</th><th>Status</th><th></th></tr></thead>
                <tbody>
                    @forelse($subscribers as $subscriber)
                        <tr>
                            <td class="truncate max-w-[220px]">{{ $subscriber->email }}</td>
                            <td>{{ $subscriber->name ?? '—' }}</td>
                            <td>{{ $subscriber->subscribed_at?->format('j M Y') }}</td>
                            <td><span class="badge badge-{{ $subscriber->is_active ? 'jade' : 'slate' }}">{{ $subscriber->is_active ? 'Active' : 'Unsubscribed' }}</span></td>
                            <td class="text-right">
                                <form action="{{ route('admin.inbox.subscribers.destroy', $subscriber) }}" method="POST" data-confirm="Remove this subscriber?">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-ghost btn-sm text-rose-600">Remove</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5"><x-ui.empty title="No subscribers yet" /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </x-ui.panel>

        {{ $subscribers->links() }}
    </div>

    <x-ui.panel title="Send a broadcast" subtitle="Queued and sent by the worker, one job per subscriber.">
        <form action="{{ route('admin.inbox.broadcast') }}" method="POST" class="space-y-4"
              data-confirm="Send this to every active subscriber?">
            @csrf
            <x-ui.field label="Subject" name="heading" required />
            <x-ui.field label="Message" name="body" type="textarea" required />
            <button class="btn btn-primary w-full">Queue broadcast</button>
            <p class="text-xs text-pine/50 leading-relaxed">
                Every email carries an unsubscribe link. Make sure <code class="font-mono">php artisan queue:work</code>
                is running or nothing leaves the building.
            </p>
        </form>
    </x-ui.panel>
</div>
@endsection
