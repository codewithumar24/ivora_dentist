<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\SmsLog;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function __construct()
    {
        $this->middleware('can.do:activity.view');
    }

    public function index(Request $request)
    {
        return view('admin.activity.index', [
            'logs' => ActivityLog::with('user')
                ->when($request->action, fn ($q, $a) => $q->where('action', $a))
                ->when($request->q, fn ($q, $t) => $q->where('description', 'like', "%{$t}%"))
                ->latest()->paginate(30)->withQueryString(),
            'actions' => ActivityLog::distinct()->pluck('action'),
            'filters' => $request->only('action', 'q'),
        ]);
    }

    /** The SMS outbox — useful when a patient says "I never got the reminder". */
    public function smsLog(Request $request)
    {
        return view('admin.activity.sms', [
            'logs' => SmsLog::with('user')
                ->when($request->purpose, fn ($q, $p) => $q->where('purpose', $p))
                ->when($request->status, fn ($q, $s) => $q->where('status', $s))
                ->latest()->paginate(30)->withQueryString(),
            'filters' => $request->only('purpose', 'status'),
        ]);
    }
}
