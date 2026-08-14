@extends('layouts.console')
@section('heading', 'SMS outbox')
@section('subheading', 'Driver: '.config('clinic.sms.driver'))

@section('content')
<div class="space-y-5">
    <div class="card-flat p-4 bg-mint/20 border-mint text-sm text-pine/80">
        Running on the <strong>{{ config('clinic.sms.driver') }}</strong> driver.
        @if(config('clinic.sms.driver') === 'log')
            Nothing is actually being sent — messages are written to <code class="font-mono">storage/logs/sms.log</code>
            and listed here. Set <code class="font-mono">SMS_DRIVER=twilio</code> in your .env to send for real.
        @endif
    </div>

    <form method="GET" class="card-flat p-4 flex flex-wrap gap-3 items-end">
        <div>
            <label class="field-label">Purpose</label>
            <select name="purpose" class="field !py-2 !text-sm">
                <option value="">Any</option>
                @foreach(['confirmation', 'reminder', 'reschedule', 'status', 'digest'] as $purpose)
                    <option value="{{ $purpose }}" @selected(($filters['purpose'] ?? '') === $purpose)>{{ ucfirst($purpose) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="field-label">Status</label>
            <select name="status" class="field !py-2 !text-sm">
                <option value="">Any</option>
                @foreach(['queued', 'sent', 'failed'] as $status)
                    <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
        </div>
        <button class="btn btn-primary btn-sm">Filter</button>
    </form>

    <x-ui.panel :padded="false">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead><tr><th>Sent</th><th>To</th><th>Purpose</th><th>Message</th><th>Status</th></tr></thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td class="whitespace-nowrap text-xs">{{ $log->created_at->format('j M, H:i') }}</td>
                            <td class="font-mono text-xs">{{ $log->to }}</td>
                            <td><span class="badge badge-mint">{{ ucfirst($log->purpose) }}</span></td>
                            <td class="truncate max-w-[380px] text-xs text-pine/70">{{ $log->body }}</td>
                            <td>
                                <span class="badge badge-{{ match($log->status) { 'sent' => 'jade', 'failed' => 'rose', default => 'amber' } }}">
                                    {{ ucfirst($log->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5"><x-ui.empty title="No messages sent yet" /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.panel>

    {{ $logs->links() }}
</div>
@endsection
