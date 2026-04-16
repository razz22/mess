<?php

namespace App\Http\Controllers\Mess;

use App\Http\Controllers\Controller;
use App\Models\Mess;
use App\Models\MessMember;
use App\Models\MessShowCause;
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
        $authUser = Auth::user();
        if (! $authUser->getMembershipIn($mess->id) && ! $authUser->is_super_admin) abort(403);
        if ($member->mess_id !== $mess->id) abort(403);

        $isManager    = $authUser->isManagerOf($mess->id);
        $isOwner      = $mess->owner_id === $authUser->id;
        $isSelf       = $member->user_id === $authUser->id;
        $isSuperAdmin = $authUser->is_super_admin;

        $showCauses = MessShowCause::where('mess_id', $mess->id)
            ->where('member_id', $member->id)
            ->with('issuedBy')
            ->latest('issued_at')
            ->get();

        $leaveRequests = \App\Models\MessLeaveRequest::where('member_id', $member->id)
            ->with('reviewedBy')
            ->orderByDesc('applied_at')
            ->get();

        $activeLeavePending = $leaveRequests->whereIn('status', ['pending', 'approved'])->first();

        return view('mess.member-profile', compact(
            'mess', 'member', 'isManager', 'isOwner', 'isSelf', 'isSuperAdmin',
            'showCauses', 'leaveRequests', 'activeLeavePending'
        ));
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
            'service_charge'              => 'nullable|numeric|min:0',
            'service_charge_date'         => 'nullable|date',
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
            'house_rent'          => $request->house_rent ?? 0,
            'service_charge'      => $request->service_charge ?? 0,
            'service_charge_date' => $request->service_charge_date,
            'advance_amount'      => $request->advance_amount ?? 0,
            'advance_date'        => $request->advance_date,
            'notes'               => $request->notes,
        ]);

        return back()->with('success', "{$user->name} added successfully.");
    }

    public function update(Request $request, Mess $mess, MessMember $member)
    {
        $authUser     = Auth::user();
        $isSelf       = $member->user_id === $authUser->id;
        $isManager    = $authUser->isManagerOf($mess->id);
        $isOwner      = $mess->owner_id === $authUser->id;
        $isSuperAdmin = $authUser->is_super_admin;

        if (! $isSuperAdmin && ! $isManager && ! $isSelf) {
            abort(403, 'You are not allowed to edit this profile.');
        }

        $profileUser = $member->user;

        $request->validate([
            'name'                        => 'required|string|max:255',
            'email'                       => 'required|email|unique:users,email,' . $profileUser->id,
            'password'                    => 'nullable|string|min:6',
            'phone'                       => 'nullable|string|max:20',
            'role'                        => 'nullable|in:member,author,manager',
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
            'service_charge'              => 'nullable|numeric|min:0',
            'service_charge_date'         => 'nullable|date',
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
            if ($profileUser->avatar) Storage::disk('public')->delete($profileUser->avatar);
            $userData['avatar'] = $request->file('avatar')->store('member-avatars', 'public');
        }

        if ($request->hasFile('nid_document')) {
            if ($profileUser->nid_document) Storage::disk('public')->delete($profileUser->nid_document);
            $userData['nid_document'] = $request->file('nid_document')->store('nid_documents', 'public');
        }

        $profileUser->update($userData);

        // Only owner/super admin can change role and financial fields
        if ($isSuperAdmin || $isOwner) {
            $newRole = $request->role ?? $member->role;
            if ($newRole === 'manager' && $member->role !== 'manager') {
                MessMember::where('mess_id', $mess->id)
                    ->where('role', 'manager')
                    ->update(['role' => 'member']);
            }
            $member->update([
                'role'           => $newRole,
                'joined_at'      => $request->joined_at ?: $member->joined_at,
                'house_rent'          => $request->house_rent ?? $member->house_rent,
                'service_charge'      => $request->service_charge ?? $member->service_charge,
                'service_charge_date' => $request->service_charge_date ?: $member->service_charge_date,
                'advance_amount'      => $request->advance_amount ?? $member->advance_amount,
                'advance_date'        => $request->advance_date ?: $member->advance_date,
                'notes'               => $request->notes ?? $member->notes,
            ]);
        } elseif ($isManager) {
            // Manager can change role (except owner) and mess-specific fields
            $newRole = $request->role ?? $member->role;
            if ($member->role !== 'owner') {
                if ($newRole === 'manager' && $member->role !== 'manager') {
                    MessMember::where('mess_id', $mess->id)
                        ->where('role', 'manager')
                        ->update(['role' => 'member']);
                }
                $member->update([
                    'role'      => $newRole,
                    'joined_at' => $request->joined_at ?: $member->joined_at,
                    'notes'     => $request->notes ?? $member->notes,
                ]);
            }
        }
        // Self-edit: no mess-member fields change (role/rent/etc.)

        return back()->with('success', $profileUser->name . ' updated successfully.');
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
