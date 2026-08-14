<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\Faq;
use App\Models\Gallery;
use App\Models\Post;
use App\Models\Service;
use App\Models\Speciality;
use App\Models\Testimonial;
use App\Services\SlotService;

class HomeController extends Controller
{
    public function __invoke(SlotService $slots)
    {
        $doctors = Doctor::with('user', 'speciality')->active()
            ->orderByDesc('is_featured')->orderBy('sort_order')->take(4)->get();

        // The hero shows a real open slot, not a decorative one.
        $nextOpen = $doctors->map(fn ($doctor) => $slots->nextAvailable($doctor, 10))
            ->filter()->sortBy(fn ($item) => $item['date']->toDateString().$item['slot']['value'])->first();

        return view('site.home', [
            'services' => Service::active()->featured()->orderBy('sort_order')->take(6)->get(),
            'specialities' => Speciality::active()->take(6)->get(),
            'doctors' => $doctors,
            'testimonials' => Testimonial::active()->take(8)->get(),
            'faqs' => Faq::active()->take(6)->get(),
            'posts' => Post::published()->with('category')->latest('published_at')->take(3)->get(),
            'gallery' => Gallery::active()->take(6)->get(),
            'nextOpen' => $nextOpen,
            'stats' => [
                'patients' => 12400,
                'years' => 18,
                'chairs' => 6,
                'satisfaction' => 98,
            ],
        ]);
    }
}
