@extends('layouts.site')
@section('title', 'Contact Zynera Dental Solution')

@section('content')
<x-site.page-hero
    eyebrow="Contact"
    title="Talk to a person."
    lede="Reception is staffed during opening hours. Out of hours, the emergency line goes to the on-call clinician."
/>

<section class="py-16">
    <div class="max-w-shell mx-auto px-5 grid lg:grid-cols-[1fr_.8fr] gap-14">

        <div class="card-flat p-7 lg:p-9 reveal">
            <h2 class="font-display text-2xl font-semibold mb-1">Send a message</h2>
            <p class="text-sm text-pine/70 mb-8">We reply within one working day. For anything urgent, please call.</p>

            <form action="{{ route('contact.send') }}" method="POST" class="space-y-5">
                @csrf
                <div class="grid sm:grid-cols-2 gap-5">
                    <x-ui.field label="Your name" name="name" required />
                    <x-ui.field label="Email" name="email" type="email" required />
                </div>
                <div class="grid sm:grid-cols-2 gap-5">
                    <x-ui.field label="Phone" name="phone" type="tel" hint="Optional, but faster for us to reply." />
                    <x-ui.field label="Subject" name="subject" />
                </div>
                <x-ui.field label="Message" name="message" type="textarea" required />

                <button class="btn btn-primary w-full sm:w-auto">Send message</button>
            </form>
        </div>

        <aside class="space-y-5">
            <div class="reveal card-flat p-7">
                <h3 class="font-display text-lg font-semibold mb-5">Reach us</h3>
                <dl class="space-y-4 text-sm">
                    <div>
                        <dt class="eyebrow text-pine/45 mb-1.5">Reception</dt>
                        <dd><a href="tel:{{ $settings['phone'] ?? '' }}" class="text-jade font-mono underline-grow">{{ $settings['phone'] ?? '' }}</a></dd>
                    </div>
                    <div>
                        <dt class="eyebrow text-pine/45 mb-1.5">Out of hours</dt>
                        <dd><a href="tel:{{ $settings['emergency_phone'] ?? '' }}" class="text-jade font-mono underline-grow">{{ $settings['emergency_phone'] ?? '' }}</a></dd>
                    </div>
                    <div>
                        <dt class="eyebrow text-pine/45 mb-1.5">Email</dt>
                        <dd><a href="mailto:{{ $settings['email'] ?? '' }}" class="text-jade underline-grow">{{ $settings['email'] ?? '' }}</a></dd>
                    </div>
                    <div>
                        <dt class="eyebrow text-pine/45 mb-1.5">Address</dt>
                        <dd class="text-pine/80 leading-relaxed">{{ $settings['address'] ?? '' }}</dd>
                    </div>
                </dl>
            </div>

            <div class="reveal card-flat p-7">
                <h3 class="font-display text-lg font-semibold mb-4">Opening hours</h3>
                <dl class="text-sm space-y-2.5">
                    <div class="flex justify-between"><dt>Monday – Friday</dt><dd class="font-mono text-pine/70">{{ $settings['hours_weekday'] ?? '' }}</dd></div>
                    <div class="flex justify-between"><dt>Saturday</dt><dd class="font-mono text-pine/70">{{ $settings['hours_saturday'] ?? '' }}</dd></div>
                    <div class="flex justify-between"><dt>Sunday</dt><dd class="font-mono text-pine/70">{{ $settings['hours_sunday'] ?? '' }}</dd></div>
                </dl>
            </div>

            <div class="reveal card-flat overflow-hidden">
                <iframe src="{{ $settings['map_embed'] ?? '' }}" class="w-full aspect-square border-0" loading="lazy" title="Map"></iframe>
            </div>
        </aside>
    </div>
</section>
@endsection
