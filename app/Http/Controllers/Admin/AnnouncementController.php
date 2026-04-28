<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Mess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnnouncementController extends Controller
{
    public function index()
    {
        abort_unless(Auth::user()->is_super_admin, 403);

        $announcements = Announcement::with(['creator'])
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $messes = Mess::orderBy('name')->get(['id', 'name']);

        return view('admin.announcements.index', compact('announcements', 'messes'));
    }

    public function store(Request $request)
    {
        abort_unless(Auth::user()->is_super_admin, 403);

        $data = $request->validate([
            'title'      => 'required|string|max:255',
            'body'       => 'required|string|max:5000',
            'audience'   => 'required|in:all,individual',
            'mess_ids'   => 'required_if:audience,individual|nullable|array|min:1',
            'mess_ids.*' => 'exists:messes,id',
            'expires_at' => 'nullable|date|after:now',
        ]);

        $data['created_by'] = Auth::id();
        if ($data['audience'] === 'all') {
            $data['mess_ids'] = null;
        }

        Announcement::create($data);

        return back()->with('success', __('Announcement created successfully.'));
    }

    public function update(Request $request, Announcement $announcement)
    {
        abort_unless(Auth::user()->is_super_admin, 403);

        $data = $request->validate([
            'title'      => 'required|string|max:255',
            'body'       => 'required|string|max:5000',
            'audience'   => 'required|in:all,individual',
            'mess_ids'   => 'required_if:audience,individual|nullable|array|min:1',
            'mess_ids.*' => 'exists:messes,id',
            'expires_at' => 'nullable|date',
        ]);

        if ($data['audience'] === 'all') {
            $data['mess_ids'] = null;
        }

        $announcement->update($data);

        return back()->with('success', __('Announcement updated successfully.'));
    }

    public function destroy(Announcement $announcement)
    {
        abort_unless(Auth::user()->is_super_admin, 403);
        $announcement->delete();
        return back()->with('success', __('Announcement deleted.'));
    }
}
