<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Setting;
use Illuminate\Http\Request;

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

        foreach ($request->allFiles()['files'] ?? [] as $key => $file) {
            Setting::put($key, $file->store('brand', 'public'));
        }

        ActivityLog::record('updated', 'Updated clinic settings');

        return back()->with('status', 'Settings saved.');
    }
}
