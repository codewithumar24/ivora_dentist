<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\Service;
use App\Models\Speciality;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        return view('site.services.index', [
            'specialities' => Speciality::active()->get(),
            'services' => Service::active()->with('speciality')
                ->when($request->speciality, fn ($q, $slug) => $q->whereRelation('speciality', 'slug', $slug))
                ->when($request->q, fn ($q, $term) => $q->where('name', 'like', "%{$term}%"))
                ->orderBy('sort_order')->paginate(9)->withQueryString(),
            'active' => $request->speciality,
        ]);
    }

    public function show(Service $service)
    {
        $service->load('speciality', 'doctors.user');

        return view('site.services.show', [
            'service' => $service,
            'related' => Service::active()->where('id', '!=', $service->id)
                ->where('speciality_id', $service->speciality_id)->take(3)->get(),
            'faqs' => Faq::active()->take(5)->get(),
        ]);
    }
}
