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

    public function show(Mess $mess, MessMember $member)
    {
        if (!Auth::user()->getMembershipIn($mess->id)) abort(403);
        if ($member->mess_id !== $mess->id) abort(403);

        $isManager = Auth::user()->isManagerOf($mess->id);
        return view('mess.member-profile', compact('mess', 'member', 'isManager'));
    }

    public function store(Request $request, Mess $mess)
    {
        $this->authorizeManager($mess);

        $request->validate([
            'name'                        => 'required|string|max:255',
            'email'                       => 'required|email|unique:users,email',
            'password'                    => 'required|string|min:6',
            'phone'                       => 'nullable|string|max:20',
            'role'                        => 'required|in:member,author',
            'avatar'                      => 'nullable|image|max:3072',
            'nid_document'                => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:3072',
            'blood_group'                 => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'gender'                      => 'nullable|in:male,female,other',
            'date_of_birth'               => 'nullable|date|before:today',
            'address'                     => 'nullable|string|max:500',
            'occupation_type'             => 'nullable|in:student,employed,business,freelance,other',
            'organization'                => 'nullable|string|max:255',
            'emergency_contact_name'      => 'nullable|string|max:100',
            'emergency_contact_phone'     => 'nullable|string|max:20',
            'emergency_contact_relation'  => 'nullable|string|max:50',
            'joined_at'                   => 'nullable|date',
            'house_rent'                  => 'nullable|numeric|min:0',
            'advance_amount'              => 'nullable|numeric|min:0',
            'advance_date'                => 'nullable|date',
            'notes'                       => 'nullable|string|max:1000',
        ]);

        $maxMembers = $mess->getEffectiveMaxMembers();
        if ($mess->getMemberCount() >= $maxMembers) {
            return back()->with('error', "Member limit reached ({$maxMembers}). Upgrade to add more.");
        }

        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('member-avatars', 'public');
        }

        $nidPath = null;
        if ($request->hasFile('nid_document')) {
            $nidPath = $request->file('nid_document')->store('nid_documents', 'public');
        }

        $user = User::create([
            'name'                       => $request->name,
            'email'                      => $request->email,
            'password'                   => Hash::make($request->password),
            'phone'                      => $request->phone,
            'avatar'                     => $avatarPath,
            'nid_document'               => $nidPath,
            'is_active'                  => true,
            'blood_group'                => $request->blood_group,
            'gender'                     => $request->gender,
            'date_of_birth'              => $request->date_of_birth,
            'address'                    => $request->address,
            'occupation_type'            => $request->occupation_type,
            'organization'               => $request->organization,
            'emergency_contact_name'     => $request->emergency_contact_name,
            'emergency_contact_phone'    => $request->emergency_contact_phone,
            'emergency_contact_relation' => $request->emergency_contact_relation,
        ]);

        MessMember::create([
            'mess_id'        => $mess->id,
            'user_id'        => $user->id,
            'role'           => $request->role,
            'is_active'      => true,
            'joined_at'      => $request->joined_at ?? now(),
            'house_rent'     => $request->house_rent ?? 0,
            'advance_amount' => $request->advance_amount ?? 0,
            'advance_date'   => $request->advance_date,
            'notes'          => $request->notes,
        ]);

        return back()->with('success', "{$user->name} added successfully.");
    }

    public function update(Request $request, Mess $mess, MessMember $member)
    {
        $this->authorizeOwner($mess);

        $user = $member->user;

        $request->validate([
            'name'                        => 'required|string|max:255',
            'email'                       => 'required|email|unique:users,email,' . $user->id,
            'password'                    => 'nullable|string|min:6',
            'phone'                       => 'nullable|string|max:20',
            'role'                        => 'required|in:member,author,manager',
            'avatar'                      => 'nullable|image|max:3072',
            'nid_document'                => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:3072',
            'blood_group'                 => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'gender'                      => 'nullable|in:male,female,other',
            'date_of_birth'               => 'nullable|date|before:today',
            'address'                     => 'nullable|string|max:500',
            'occupation_type'             => 'nullable|in:student,employed,business,freelance,other',
            'organization'                => 'nullable|string|max:255',
            'emergency_contact_name'      => 'nullable|string|max:100',
            'emergency_contact_phone'     => 'nullable|string|max:20',
            'emergency_contact_relation'  => 'nullable|string|max:50',
            'joined_at'                   => 'nullable|date',
            'house_rent'                  => 'nullable|numeric|min:0',
            'advance_amount'              => 'nullable|numeric|min:0',
            'advance_date'                => 'nullable|date',
            'notes'                       => 'nullable|string|max:1000',
        ]);

        $userData = [
            'name'                       => $request->name,
            'email'                      => $request->email,
            'phone'                      => $request->phone,
            'blood_group'                => $request->blood_group,
            'gender'                     => $request->gender,
            'date_of_birth'              => $request->date_of_birth ?: null,
            'address'                    => $request->address,
            'occupation_type'            => $request->occupation_type,
            'organization'               => $request->organization,
            'emergency_contact_name'     => $request->emergency_contact_name,
            'emergency_contact_phone'    => $request->emergency_contact_phone,
            'emergency_contact_relation' => $request->emergency_contact_relation,
        ];

        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('avatar')) {
            if ($user->avatar) Storage::disk('public')->delete($user->avatar);
            $userData['avatar'] = $request->file('avatar')->store('member-avatars', 'public');
        }

        if ($request->hasFile('nid_document')) {
            if ($user->nid_document) Storage::disk('public')->delete($user->nid_document);
            $userData['nid_document'] = $request->file('nid_document')->store('nid_documents', 'public');
        }

        $user->update($userData);

        if ($request->role === 'manager') {
            MessMember::where('mess_id', $mess->id)
                ->where('role', 'manager')
                ->update(['role' => 'member']);
        }

        $member->update([
            'role'           => $request->role,
            'joined_at'      => $request->joined_at ?: $member->joined_at,
            'house_rent'     => $request->house_rent ?? 0,
            'advance_amount' => $request->advance_amount ?? 0,
            'advance_date'   => $request->advance_date ?: null,
            'notes'          => $request->notes,
        ]);

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
            abort(403, 'Only managers and owners can access this.');
        }
    }

    private function authorizeOwner(Mess $mess): void
    {
        if ($mess->owner_id !== Auth::id()) {
            abort(403, 'Owners only.');
        }
    }
}
