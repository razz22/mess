<?php

namespace App\Http\Controllers\Mess;

use App\Http\Controllers\Controller;
use App\Models\Mess;
use App\Models\MessMember;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class MemberController extends Controller
{
    public function index(Mess $mess)
    {
        $this->authorizeManager($mess);
        $members = $mess->members()->with('user')->where('is_active', true)->orderBy('role')->get();

        return view('mess.members', compact('mess', 'members'));
    }

    public function store(Request $request, Mess $mess)
    {
        $this->authorizeManager($mess);

        $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|unique:users,email',
            'password'     => 'required|string|min:6',
            'phone'        => 'nullable|string|max:20',
            'nid_document' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'role'         => 'required|in:member,author',
        ]);

        $maxMembers = $mess->getEffectiveMaxMembers();
        if ($mess->getMemberCount() >= $maxMembers) {
            return back()->with('error', "Member limit reached ({$maxMembers}). Upgrade subscription to add more.");
        }

        $nidPath = null;
        if ($request->hasFile('nid_document')) {
            $nidPath = $request->file('nid_document')->store('nid_documents', 'public');
        }

        $user = User::create([
            'name'         => $request->name,
            'email'        => $request->email,
            'password'     => Hash::make($request->password),
            'phone'        => $request->phone,
            'nid_document' => $nidPath,
            'is_active'    => true,
        ]);

        MessMember::create([
            'mess_id'   => $mess->id,
            'user_id'   => $user->id,
            'role'      => $request->role,
            'is_active' => true,
            'joined_at' => now(),
        ]);

        return back()->with('success', "Member {$user->name} added. They can now log in with {$user->email}.");
    }

    public function update(Request $request, Mess $mess, MessMember $member)
    {
        $this->authorizeOwner($mess);

        $user = $member->user;

        $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|unique:users,email,' . $user->id,
            'password'     => 'nullable|string|min:6',
            'phone'        => 'nullable|string|max:20',
            'nid_document' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'role'         => 'required|in:member,author,manager',
        ]);

        $data = [
            'name'  => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('nid_document')) {
            if ($user->nid_document) {
                Storage::disk('public')->delete($user->nid_document);
            }
            $data['nid_document'] = $request->file('nid_document')->store('nid_documents', 'public');
        }

        $user->update($data);

        if ($request->role === 'manager') {
            MessMember::where('mess_id', $mess->id)
                ->where('role', 'manager')
                ->update(['role' => 'member']);
        }
        $member->update(['role' => $request->role]);

        return back()->with('success', $user->name . ' updated successfully.');
    }

    public function updateRole(Request $request, Mess $mess, MessMember $member)
    {
        $this->authorizeOwner($mess);
        $request->validate(['role' => 'required|in:member,author,manager']);

        if ($request->role === 'manager') {
            MessMember::where('mess_id', $mess->id)
                ->where('role', 'manager')
                ->update(['role' => 'member']);
        }

        $member->update(['role' => $request->role]);
        return back()->with('success', 'Role updated.');
    }

    public function remove(Mess $mess, MessMember $member)
    {
        $this->authorizeOwner($mess);

        if ($member->role === 'owner') {
            return back()->with('error', 'Cannot remove the owner.');
        }

        $member->update(['is_active' => false]);
        return back()->with('success', 'Member removed.');
    }

    private function authorizeManager(Mess $mess): void
    {
        if (!Auth::user()->isManagerOf($mess->id)) {
            abort(403, 'Only managers, authors, and owners can access this.');
        }
    }

    private function authorizeOwner(Mess $mess): void
    {
        if ($mess->owner_id !== Auth::id()) {
            abort(403, 'Owners only.');
        }
    }
}
