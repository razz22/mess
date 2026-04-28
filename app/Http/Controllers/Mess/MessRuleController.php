<?php

namespace App\Http\Controllers\Mess;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\AuthorizesMessAccess;
use App\Models\Mess;
use App\Models\MessRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessRuleController extends Controller
{
    use AuthorizesMessAccess;

    public function index(Mess $mess)
    {
        $this->requireMember($mess);
        $member   = Auth::user()->getMembershipIn($mess->id);
        $canManage = Auth::user()->is_super_admin || ($member && $member->role === 'owner');
        $rules    = MessRule::where('mess_id', $mess->id)->orderBy('sort_order')->orderBy('id')->get();

        return view('mess.rules', compact('mess', 'member', 'canManage', 'rules'));
    }

    public function store(Request $request, Mess $mess)
    {
        $this->requireOwner($mess);

        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string|max:65000',
        ]);

        $maxOrder = MessRule::where('mess_id', $mess->id)->max('sort_order') ?? 0;

        MessRule::create([
            'mess_id'     => $mess->id,
            'title'       => $request->title,
            'description' => $request->description,
            'sort_order'  => $maxOrder + 1,
            'is_active'   => true,
            'created_by'  => Auth::id(),
        ]);

        return back()->with('success', 'Rule added successfully.');
    }

    public function update(Request $request, Mess $mess, MessRule $rule)
    {
        $this->requireOwner($mess);
        if ($rule->mess_id !== $mess->id) abort(403);

        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string|max:65000',
        ]);

        $rule->update([
            'title'       => $request->title,
            'description' => $request->description,
        ]);

        return back()->with('success', 'Rule updated.');
    }

    public function destroy(Mess $mess, MessRule $rule)
    {
        $this->requireOwner($mess);
        if ($rule->mess_id !== $mess->id) abort(403);

        $rule->delete();

        return back()->with('success', 'Rule deleted.');
    }

    public function reorder(Request $request, Mess $mess)
    {
        $this->requireOwner($mess);

        $request->validate(['order' => 'required|array', 'order.*' => 'integer']);

        foreach ($request->order as $position => $id) {
            MessRule::where('id', $id)->where('mess_id', $mess->id)
                ->update(['sort_order' => $position + 1]);
        }

        return response()->json(['success' => true]);
    }
}
