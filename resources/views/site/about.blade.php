@extends('layouts.site')
@section('title', 'About Zynera Dental Solution')

@section('content')
<x-site.page-hero
    eyebrow="About"
    title="A dental studio built<br>around the ten minutes<br>before treatment."
    lede="Most of what makes people dread the dentist happens before anything touches a tooth: not knowing what is wrong, not knowing what it will cost, not being told what happens next."
/>

<section class="py-20">
    <div class="max-w-shell mx-auto px-5 grid lg:grid-cols-2 gap-16 items-center">
        <div class="arch-frame aspect-[4/5] max-w-[460px] reveal bg-pine">
            <img src="https://images.unsplash.com/photo-1629909613654-28e377c37b09?w=900&q=80" alt="Ivora reception" class="w-full h-full object-cover">
        </div>

        <div class="prose-ivora reveal">
            <h2 class="font-display text-3xl font-semibold text-ink mb-5">Why we opened</h2>
            <p>Dr Ayesha Rahim spent ten years in restorative practice in London before coming home to Karachi in 2012. What she noticed on returning was not a shortage of skill — there is plenty of that — but a shortage of explanation.</p>
            <p>Patients were arriving at Ivora with treatment plans they could not read, quotes that changed after the work started, and a general sense that the appointment was something being done to them rather than with them.</p>
            <p>So the studio was built around a simple rule: <strong>nobody gets treated until they understand what is happening and what it costs.</strong> Everything else — the six chairs, the digital imaging, the Saturday surgical list — followed from that.</p>
            <h2 class="font-display text-3xl font-semibold text-ink mt-10 mb-5">What that means in practice</h2>
            <ul>
                <li>Your images go on the screen in front of you, every visit.</li>
                <li>Written, itemised plans before booking — not after.</li>
                <li>Slot lengths that match the work, so the diary does not run late.</li>
                <li>One named clinician per course of treatment.</li>
            </ul>
        </div>
    </div>
</section>

<x-site.arch-divider from="#F4F7F5" to="#10403B" />

<section class="on-dark relative overflow-hidden">
    <div class="grain absolute inset-0"></div>
    <div class="relative max-w-shell mx-auto px-5 py-20">
        <x-site.section-head eyebrow="Specialities" tone="light" title="Six disciplines under<br>one roof." lede="Referrals cost time. Almost everything you might need is handled in this building." />

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5 mt-14">
    @foreach($specialities as $speciality)
        <div class="glass !bg-white/90 !border-white/40 rounded-2xl p-6 reveal">
            <h3 class="font-display text-lg font-semibold text-ink mb-2.5">{{ $speciality->name }}</h3>
            <p class="text-sm text-ink leading-relaxed">{{ $speciality->description }}</p>
        </div>
    @endforeach
</div>
    </div>
</section>

<x-site.arch-divider from="#10403B" to="#F4F7F5" flip />

<section class="py-20">
    <div class="max-w-shell mx-auto px-5">
        <x-site.section-head eyebrow="The team" title="Ten clinicians, each with<br>a declared speciality." />
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-8 mt-14">
            @foreach($doctors as $doctor)
                <x-site.doctor-card :doctor="$doctor" />
            @endforeach
        </div>
    </div>
</section>
@endsection
