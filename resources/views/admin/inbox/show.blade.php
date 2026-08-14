@extends('layouts.console')
@section('heading', $message->subject ?: 'Enquiry')
@section('subheading', 'From '.$message->name.' · '.$message->created_at->format('j F Y, g:i A'))

@section('content')
<div class="grid lg:grid-cols-[1fr_300px] gap-6 items-start max-w-5xl">
    <div class="space-y-5">
        <x-ui.panel title="Message">
            <p class="text-sm text-pine/85 leading-relaxed whitespace-pre-line">{{ $message->message }}</p>
        </x-ui.panel>

        @if($message->reply)
            <x-ui.panel title="Your reply" :subtitle="'Sent '.$message->replied_at?->format('j M Y, g:i A')">
                <p class="text-sm text-pine/85 leading-relaxed whitespace-pre-line">{{ $message->reply }}</p>
            </x-ui.panel>
        @endif

        @permission('messages.reply')
            <x-ui.panel :title="$message->reply ? 'Reply again' : 'Reply'">
                <form action="{{ route('admin.inbox.reply', $message) }}" method="POST" class="space-y-4">
                    @csrf
                    <x-ui.field name="reply" type="textarea" required
                                hint="This is emailed to {{ $message->email }} from the clinic address." />
                    <button class="btn btn-primary">Send reply</button>
                </form>
            </x-ui.panel>
        @endpermission
    </div>

    <aside class="space-y-5">
        <x-ui.panel title="Sender">
            <dl class="space-y-3 text-sm">
                @foreach(['Name' => $message->name, 'Email' => $message->email, 'Phone' => $message->phone ?? '—', 'Status' => ucfirst($message->status)] as $label => $value)
                    <div>
                        <dt class="eyebrow text-pine/45 mb-1">{{ $label }}</dt>
                        <dd class="truncate">{{ $value }}</dd>
                    </div>
                @endforeach
            </dl>

            <div class="flex flex-col gap-2 mt-5 pt-4 border-t border-pine/8">
                <a href="mailto:{{ $message->email }}" class="btn btn-ghost btn-sm">Open in mail client</a>
                @if($message->phone)
                    <a href="tel:{{ $message->phone }}" class="btn btn-ghost btn-sm">Call them</a>
                @endif
                @permission('messages.reply')
                    <form action="{{ route('admin.inbox.archive', $message) }}" method="POST">
                        @csrf
                        <button class="btn btn-ghost btn-sm w-full">Archive</button>
                    </form>
                @endpermission
            </div>
        </x-ui.panel>
    </aside>
</div>
@endsection
