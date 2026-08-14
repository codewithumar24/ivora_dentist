<?php

namespace App\Jobs;

use App\Mail\NewsletterBroadcast;
use App\Models\Subscriber;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendNewsletterEmail implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Subscriber $subscriber,
        public string $heading,
        public string $body
    ) {}

    public function handle(): void
    {
        if (! $this->subscriber->is_active) {
            return;
        }

        Mail::to($this->subscriber->email)
            ->send(new NewsletterBroadcast($this->subscriber, $this->heading, $this->body));
    }
}
