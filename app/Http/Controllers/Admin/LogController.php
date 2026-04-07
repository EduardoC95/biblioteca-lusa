<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LogController extends Controller
{
    public function index(Request $request): View
    {
        $logs = ActivityLog::query()
            ->with('user')
            ->when($request->filled('module'), function ($query) use ($request) {
                $query->where('module', $request->string('module'));
            })
            ->when($request->filled('user'), function ($query) use ($request) {
                $query->whereHas('user', function ($userQuery) use ($request) {
                    $userQuery->where('name', 'like', '%' . $request->string('user') . '%');
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $modules = ActivityLog::query()
            ->select('module')
            ->distinct()
            ->orderBy('module')
            ->pluck('module');

        return view('admin.logs.index', compact('logs', 'modules'));
    }
}
