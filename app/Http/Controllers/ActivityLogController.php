<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $items = ActivityLog::with('user')
            ->search($request->search)
            ->latest()
            ->paginate(30)
            ->withQueryString();

        return view('logs.index', compact('items'));
    }
}