<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomSubscription;
use App\Models\Mess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomSubscriptionController extends Controller
{
    public function index()
    {
        abort_unless(Auth::user()->is_super_admin, 403);

        $subscriptions = CustomSubscription::with('creator')->latest()->paginate(20)->withQueryString();
        $messes        = Mess::orderBy('name')->get(['id', 'name']);

        return view('admin.custom-subscriptions.index', compact('subscriptions', 'messes'));
    }

    public function store(Request $request)
    {
        abort_unless(Auth::user()->is_super_admin, 403);

        $data = $request->validate([
            'label'       => 'required|string|max:255',
            'max_members' => 'required|integer|min:1|max:9999',
            'is_free'     => 'nullable|boolean',
            'price'       => 'required_unless:is_free,1|nullable|numeric|min:0',
            'mess_ids'    => 'required|array|min:1',
            'mess_ids.*'  => 'exists:messes,id',
            'starts_at'   => 'nullable|date',
            'expires_at'  => 'nullable|date|after_or_equal:starts_at',
            'notes'       => 'nullable|string|max:1000',
        ]);

        $data['created_by'] = Auth::id();
        $data['is_free']    = $request->boolean('is_free');
        if ($data['is_free']) {
            $data['price'] = 0;
        }
        if (empty($data['starts_at'])) {
            $data['starts_at'] = now();
        }

        CustomSubscription::create($data);

        return back()->with('success', __('Custom subscription assigned successfully.'));
    }

    public function update(Request $request, CustomSubscription $customSubscription)
    {
        abort_unless(Auth::user()->is_super_admin, 403);

        $data = $request->validate([
            'label'       => 'required|string|max:255',
            'max_members' => 'required|integer|min:1|max:9999',
            'is_free'     => 'nullable|boolean',
            'price'       => 'required_unless:is_free,1|nullable|numeric|min:0',
            'mess_ids'    => 'required|array|min:1',
            'mess_ids.*'  => 'exists:messes,id',
            'starts_at'   => 'nullable|date',
            'expires_at'  => 'nullable|date',
            'status'      => 'required|in:active,inactive',
            'notes'       => 'nullable|string|max:1000',
        ]);

        $data['is_free'] = $request->boolean('is_free');
        if ($data['is_free']) {
            $data['price'] = 0;
        }

        $customSubscription->update($data);

        return back()->with('success', __('Custom subscription updated successfully.'));
    }

    public function destroy(CustomSubscription $customSubscription)
    {
        abort_unless(Auth::user()->is_super_admin, 403);
        $customSubscription->delete();
        return back()->with('success', __('Custom subscription deleted.'));
    }
}
