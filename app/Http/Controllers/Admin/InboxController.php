<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendNewsletterEmail;
use App\Mail\ContactReply;
use App\Models\ContactMessage;
use App\Models\Subscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class InboxController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('messages.view');

        return view('admin.inbox.index', [
            'messages' => ContactMessage::when($request->status, fn ($q, $s) => $q->where('status', $s))
                ->latest()->paginate(15)->withQueryString(),
            'unread' => ContactMessage::unread()->count(),
            'filters' => $request->only('status'),
        ]);
    }

    public function show(ContactMessage $message)
    {
        $this->authorize('messages.view');

        if ($message->status === 'new') {
            $message->update(['status' => 'read']);
        }

        return view('admin.inbox.show', compact('message'));
    }

    public function reply(Request $request, ContactMessage $message)
    {
        $this->authorize('messages.reply');

        $data = $request->validate(['reply' => ['required', 'string', 'min:5', 'max:5000']]);

        $message->update($data + [
            'status' => 'replied',
            'replied_at' => now(),
            'replied_by' => auth()->id(),
        ]);

        Mail::to($message->email)->queue(new ContactReply($message));

        return back()->with('status', 'Reply sent.');
    }

    public function archive(ContactMessage $message)
    {
        $this->authorize('messages.reply');
        $message->update(['status' => 'archived']);

        return redirect()->route('admin.inbox.index')->with('status', 'Message archived.');
    }

    public function subscribers(Request $request)
    {
        $this->authorize('subscribers.manage');

        return view('admin.inbox.subscribers', [
            'subscribers' => Subscriber::when($request->q, fn ($q, $t) => $q->where('email', 'like', "%{$t}%"))
                ->latest()->paginate(25)->withQueryString(),
            'active' => Subscriber::where('is_active', true)->count(),
        ]);
    }

    public function broadcast(Request $request)
    {
        $this->authorize('subscribers.manage');

        $data = $request->validate([
            'heading' => ['required', 'string', 'max:150'],
            'body' => ['required', 'string', 'min:20'],
        ]);

        $subscribers = Subscriber::where('is_active', true)->get();

        foreach ($subscribers as $subscriber) {
            SendNewsletterEmail::dispatch($subscriber, $data['heading'], $data['body']);
        }

        return back()->with('status', "Queued for {$subscribers->count()} subscriber(s). The queue worker sends them out.");
    }

    public function destroySubscriber(Subscriber $subscriber)
    {
        $this->authorize('subscribers.manage');
        $subscriber->delete();

        return back()->with('status', 'Subscriber removed.');
    }
}
