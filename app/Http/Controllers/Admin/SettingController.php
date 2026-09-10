<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('can.do:settings.manage');
    }

    public function edit()
    {
        return view('admin.settings', [
            'groups' => Setting::query()->orderBy('group')->get()->groupBy('group'),
        ]);
    }

    public function update(Request $request)
    {
        foreach ($request->input('settings', []) as $key => $value) {
            Setting::put($key, $value);
        }

        // Images and the hero video come through as files[key]. Validate by the
        // setting's own declared type so a stray upload cannot put a 40 MB clip
        // where a logo belongs.
        $files = $request->allFiles()['files'] ?? [];

        if ($files) {
            $types = Setting::query()
                ->whereIn('key', array_keys($files))
                ->pluck('type', 'key');

            $rules = [];

            foreach ($files as $key => $file) {
                $rules["files.$key"] = match ($types[$key] ?? 'image') {
                    'video' => ['file', 'mimetypes:video/mp4,video/webm', 'max:20480'],
                    default => ['image', 'mimes:jpg,jpeg,png,webp,svg', 'max:4096'],
                };
            }

            $request->validate($rules, [], [
                'files.logo'        => 'logo',
                'files.hero_image'  => 'hero photograph',
                'files.hero_video'  => 'hero video',
            ]);

            foreach ($files as $key => $file) {
                $previous = Setting::get($key);

                Setting::put($key, $file->store('brand', 'public'));

                // Replaced files are dead weight — remove them rather than
                // letting storage/app/public/brand grow forever.
                if ($previous && ! str_starts_with($previous, 'http') && Storage::disk('public')->exists($previous)) {
                    Storage::disk('public')->delete($previous);
                }
            }
        }

        // Clearing a media setting has to be possible too, or a wrong upload is
        // permanent.
        foreach ($request->input('clear', []) as $key => $_) {
            if ($path = Setting::get($key)) {
                if (! str_starts_with($path, 'http') && Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }

            Setting::put($key, '');
        }

        ActivityLog::record('updated', 'Updated clinic settings');

        return back()->with('status', 'Settings saved.');
    }
}
