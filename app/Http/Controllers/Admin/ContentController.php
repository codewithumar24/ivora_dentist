<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\Gallery;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\Review;
use App\Models\Service;
use App\Models\Speciality;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Everything a marketing person touches: treatments, articles, gallery,
 * testimonials, FAQs and review moderation.
 */
class ContentController extends Controller
{
    // ---- Treatments -------------------------------------------------------

    public function services(Request $request)
    {
        $this->authorize('services.manage');

        return view('admin.content.services', [
            'services' => Service::with('speciality')->when($request->q, fn ($q, $t) => $q->where('name', 'like', "%{$t}%"))
                ->orderBy('sort_order')->paginate(15)->withQueryString(),
            'specialities' => Speciality::orderBy('name')->get(),
        ]);
    }

    public function storeService(Request $request)
    {
        $this->authorize('services.manage');

        $data = $this->serviceRules($request);
        $data['slug'] = Str::slug($data['name']).'-'.Str::random(4);
        $data['image'] = $this->upload($request, 'image', 'services') ?? $request->input('image_url');

        Service::create($data);

        return back()->with('status', 'Treatment added.');
    }

    public function updateService(Request $request, Service $service)
    {
        $this->authorize('services.manage');

        $data = $this->serviceRules($request);

        if ($path = $this->upload($request, 'image', 'services')) {
            $data['image'] = $path;
        }

        $service->update($data);

        return back()->with('status', 'Treatment updated.');
    }

    public function destroyService(Service $service)
    {
        $this->authorize('services.manage');
        $service->delete();

        return back()->with('status', 'Treatment removed from the site.');
    }

    protected function serviceRules(Request $request): array
    {
        return $request->validate([
            'speciality_id' => ['nullable', 'exists:specialities,id'],
            'name' => ['required', 'string', 'max:150'],
            'icon' => ['nullable', 'string', 'max:60'],
            'summary' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price_from' => ['required', 'numeric', 'min:0'],
            'price_to' => ['nullable', 'numeric', 'min:0'],
            'duration_minutes' => ['required', 'integer', 'min:10', 'max:480'],
            'is_featured' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer'],
        ]) + [
            'is_featured' => $request->boolean('is_featured'),
            'is_active' => $request->boolean('is_active', true),
        ];
    }

    // ---- Specialities -----------------------------------------------------

    public function specialities()
    {
        $this->authorize('services.manage');

        return view('admin.content.specialities', [
            'specialities' => Speciality::withCount('services', 'doctors')->orderBy('sort_order')->get(),
        ]);
    }

    public function storeSpeciality(Request $request)
    {
        $this->authorize('services.manage');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'icon' => ['nullable', 'string', 'max:60'],
            'description' => ['nullable', 'string', 'max:500'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        Speciality::create($data + ['slug' => Str::slug($data['name'])]);

        return back()->with('status', 'Speciality added.');
    }

    public function updateSpeciality(Request $request, Speciality $speciality)
    {
        $this->authorize('services.manage');

        $speciality->update($request->validate([
                'name' => ['required', 'string', 'max:120'],
                'icon' => ['nullable', 'string', 'max:60'],
                'description' => ['nullable', 'string', 'max:500'],
                'sort_order' => ['nullable', 'integer'],
            ]) + ['is_active' => $request->boolean('is_active', true)]);

        return back()->with('status', 'Speciality updated.');
    }

    public function destroySpeciality(Speciality $speciality)
    {
        $this->authorize('services.manage');
        $speciality->delete();

        return back()->with('status', 'Speciality removed.');
    }

    // ---- Blog -------------------------------------------------------------

    public function posts(Request $request)
    {
        $this->authorize('blog.manage');

        return view('admin.content.posts', [
            'posts' => Post::with('category', 'author')->when($request->q, fn ($q, $t) => $q->where('title', 'like', "%{$t}%"))
                ->latest()->paginate(12)->withQueryString(),
            'categories' => PostCategory::orderBy('name')->get(),
        ]);
    }

    public function createPost()
    {
        $this->authorize('blog.manage');

        return view('admin.content.post-form', [
            'post' => new Post(['status' => 'published', 'read_minutes' => 4]),
            'categories' => PostCategory::orderBy('name')->get(),
        ]);
    }

    public function editPost(Post $post)
    {
        $this->authorize('blog.manage');

        return view('admin.content.post-form', [
            'post' => $post,
            'categories' => PostCategory::orderBy('name')->get(),
        ]);
    }

    public function storePost(Request $request)
    {
        $this->authorize('blog.manage');

        $data = $this->postRules($request);
        $data['slug'] = Str::slug($data['title']).'-'.Str::random(4);
        $data['author_id'] = auth()->id();
        $data['cover_image'] = $this->upload($request, 'cover_image', 'posts') ?? $request->input('cover_url');

        Post::create($data);

        return redirect()->route('admin.content.posts')->with('status', 'Article published.');
    }

    public function updatePost(Request $request, Post $post)
    {
        $this->authorize('blog.manage');

        $data = $this->postRules($request);

        if ($path = $this->upload($request, 'cover_image', 'posts')) {
            $data['cover_image'] = $path;
        }

        $post->update($data);

        return back()->with('status', 'Article updated.');
    }

    public function destroyPost(Post $post)
    {
        $this->authorize('blog.manage');
        $post->delete();

        return back()->with('status', 'Article deleted.');
    }

    protected function postRules(Request $request): array
    {
        return $request->validate([
            'post_category_id' => ['nullable', 'exists:post_categories,id'],
            'title' => ['required', 'string', 'max:190'],
            'excerpt' => ['nullable', 'string', 'max:280'],
            'body' => ['required', 'string'],
            'read_minutes' => ['nullable', 'integer', 'min:1', 'max:60'],
            'status' => ['required', 'in:draft,published'],
            'published_at' => ['nullable', 'date'],
            'meta_title' => ['nullable', 'string', 'max:190'],
            'meta_description' => ['nullable', 'string', 'max:300'],
        ]) + ['published_at' => $request->published_at ?? now()];
    }

    public function storeCategory(Request $request)
    {
        $this->authorize('blog.manage');

        $data = $request->validate(['name' => ['required', 'string', 'max:120'], 'colour' => ['nullable', 'string', 'max:20']]);
        PostCategory::create($data + ['slug' => Str::slug($data['name'])]);

        return back()->with('status', 'Category added.');
    }

    // ---- Gallery / testimonials / FAQs ------------------------------------

    public function gallery()
    {
        $this->authorize('gallery.manage');

        return view('admin.content.gallery', ['items' => Gallery::orderBy('sort_order')->paginate(24)]);
    }

    public function storeGallery(Request $request)
    {
        $this->authorize('gallery.manage');

        $data = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'category' => ['required', 'string', 'max:80'],
            'caption' => ['nullable', 'string', 'max:500'],
            'image' => ['nullable', 'image', 'max:4096'],
            'image_url' => ['nullable', 'url'],
            'before_image' => ['nullable', 'image', 'max:4096'],
        ]);

        Gallery::create([
            'title' => $data['title'],
            'category' => $data['category'],
            'caption' => $data['caption'] ?? null,
            'image' => $this->upload($request, 'image', 'gallery') ?? $data['image_url'] ?? '',
            'before_image' => $this->upload($request, 'before_image', 'gallery'),
        ]);

        return back()->with('status', 'Image added to the gallery.');
    }

    public function destroyGallery(Gallery $gallery)
    {
        $this->authorize('gallery.manage');
        $gallery->delete();

        return back()->with('status', 'Image removed.');
    }

    public function testimonials()
    {
        $this->authorize('gallery.manage');

        return view('admin.content.testimonials', ['testimonials' => Testimonial::orderBy('sort_order')->paginate(15)]);
    }

    public function storeTestimonial(Request $request)
    {
        $this->authorize('gallery.manage');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'role' => ['nullable', 'string', 'max:120'],
            'treatment' => ['nullable', 'string', 'max:120'],
            'quote' => ['required', 'string', 'max:1000'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'avatar' => ['nullable', 'image', 'max:2048'],
        ]);

        Testimonial::create($data + ['avatar' => $this->upload($request, 'avatar', 'testimonials')]);

        return back()->with('status', 'Testimonial added.');
    }

    public function updateTestimonial(Request $request, Testimonial $testimonial)
    {
        $this->authorize('gallery.manage');

        $testimonial->update($request->only('name', 'role', 'treatment', 'quote', 'rating', 'sort_order')
            + ['is_active' => $request->boolean('is_active')]);

        return back()->with('status', 'Testimonial updated.');
    }

    public function destroyTestimonial(Testimonial $testimonial)
    {
        $this->authorize('gallery.manage');
        $testimonial->delete();

        return back()->with('status', 'Testimonial removed.');
    }

    public function faqs()
    {
        $this->authorize('faqs.manage');

        return view('admin.content.faqs', ['faqs' => Faq::orderBy('category')->orderBy('sort_order')->get()->groupBy('category')]);
    }

    public function storeFaq(Request $request)
    {
        $this->authorize('faqs.manage');

        Faq::create($request->validate([
            'question' => ['required', 'string', 'max:255'],
            'answer' => ['required', 'string', 'max:3000'],
            'category' => ['required', 'string', 'max:80'],
            'sort_order' => ['nullable', 'integer'],
        ]));

        return back()->with('status', 'FAQ added.');
    }

    public function updateFaq(Request $request, Faq $faq)
    {
        $this->authorize('faqs.manage');

        $faq->update($request->only('question', 'answer', 'category', 'sort_order')
            + ['is_active' => $request->boolean('is_active')]);

        return back()->with('status', 'FAQ updated.');
    }

    public function destroyFaq(Faq $faq)
    {
        $this->authorize('faqs.manage');
        $faq->delete();

        return back()->with('status', 'FAQ removed.');
    }

    // ---- Review moderation ------------------------------------------------

    public function reviews(Request $request)
    {
        $this->authorize('gallery.manage');

        return view('admin.content.reviews', [
            'reviews' => Review::with('patient', 'doctor.user')
                ->when($request->pending, fn ($q) => $q->where('is_approved', false))
                ->latest()->paginate(15)->withQueryString(),
        ]);
    }

    public function moderateReview(Request $request, Review $review)
    {
        $this->authorize('gallery.manage');

        $review->update([
            'is_approved' => $request->boolean('is_approved'),
            'is_featured' => $request->boolean('is_featured'),
        ]);

        $review->doctor?->refreshRating();

        return back()->with('status', $review->is_approved ? 'Review published.' : 'Review hidden.');
    }

    protected function upload(Request $request, string $field, string $folder): ?string
    {
        return $request->hasFile($field) ? $request->file($field)->store($folder, 'public') : null;
    }
}
