<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\Doctor;
use App\Models\Faq;
use App\Models\Gallery;
use App\Models\Service;
use App\Models\Speciality;
use App\Models\Subscriber;
use App\Models\Testimonial;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function about()
    {
        return view('site.about', [
            'doctors' => Doctor::with('user', 'speciality')->active()->orderBy('sort_order')->take(6)->get(),
            'specialities' => Speciality::active()->get(),
        ]);
    }

    public function pricing()
    {
        return view('site.pricing', [
            'specialities' => Speciality::active()->with(['services' => fn ($q) => $q->active()->orderBy('sort_order')])->get(),
            'faqs' => Faq::active()->where('category', 'Payments')->get(),
        ]);
    }

    public function gallery(Request $request)
    {
        $categories = Gallery::active()->distinct()->pluck('category');

        return view('site.gallery', [
            'categories' => $categories,
            'items' => Gallery::active()
                ->when($request->category, fn ($q, $c) => $q->where('category', $c))
                ->paginate(12)->withQueryString(),
            'active' => $request->category,
        ]);
    }

    public function faq()
    {
        return view('site.faq', [
            'groups' => Faq::active()->get()->groupBy('category'),
        ]);
    }

    public function testimonials()
    {
        return view('site.testimonials', [
            'testimonials' => Testimonial::active()->paginate(12),
        ]);
    }

    public function contact()
    {
        return view('site.contact', [
            'services' => Service::active()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function sendContact(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190'],
            'phone' => ['nullable', 'string', 'max:32'],
            'subject' => ['nullable', 'string', 'max:180'],
            'message' => ['required', 'string', 'min:10', 'max:3000'],
        ]);

        ContactMessage::create($data + ['ip_address' => $request->ip()]);

        return back()->with('status', 'Message sent. The front desk replies within one working day.');
    }

    public function subscribe(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:190'],
            'name' => ['nullable', 'string', 'max:120'],
        ]);

        Subscriber::firstOrCreate(['email' => $data['email']], ['name' => $data['name'] ?? null]);

        return back()->with('status', "You're on the list. Expect one email a month, no more.");
    }

    public function unsubscribe(string $token)
    {
        Subscriber::where('unsubscribe_token', $token)->update(['is_active' => false]);

        return view('site.unsubscribed');
    }
}
