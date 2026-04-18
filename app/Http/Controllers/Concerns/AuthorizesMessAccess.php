<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Mess;
use App\Models\MessMember;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

trait AuthorizesMessAccess
{
    protected function requireMember(Mess $mess): ?MessMember
    {
        $user = Auth::user();
        if (!$user) return null;
        $member = $user->getMembershipIn($mess->id);
        if (!$member) {
            Log::warning('Mess 403: user is not an active member', [
                'user_id' => $user->id,
                'mess_id' => $mess->id,
                'url'     => request()->fullUrl(),
            ]);
            abort(redirect()->route('mess.index')->with('error', 'You do not have access to this mess.'));
        }
        return $member;
    }

    protected function requireManager(Mess $mess): ?MessMember
    {
        $user = Auth::user();
        if (!$user) return null;
        if ($user->is_super_admin) return $user->getMembershipIn($mess->id);
        $member = $user->getMembershipIn($mess->id);
        if (!$member || !in_array($member->role, ['owner', 'manager', 'author'])) {
            Log::warning('Mess 403: user is not a manager', [
                'user_id' => $user->id,
                'mess_id' => $mess->id,
                'role'    => $member?->role ?? 'no_membership',
                'url'     => request()->fullUrl(),
            ]);
            abort(redirect()->route('mess.index')->with('error', 'You need manager or owner access for this action.'));
        }
        return $member;
    }

    protected function requireOwner(Mess $mess): void
    {
        $user   = Auth::user();
        $member = $user?->getMembershipIn($mess->id);
        $isOwner = ($mess->owner_id === $user?->id)
            || ($member && $member->role === 'owner')
            || $user?->is_super_admin;
        if (!$isOwner) {
            Log::warning('Mess 403: user is not the owner', [
                'user_id' => $user?->id,
                'mess_id' => $mess->id,
                'url'     => request()->fullUrl(),
            ]);
            abort(redirect()->route('mess.index')->with('error', 'Only the mess owner can perform this action.'));
        }
    }

    protected function requireSameMess(Mess $mess, $model, string $label = 'resource'): void
    {
        if ((int) $model->mess_id !== (int) $mess->id) {
            Log::warning("Mess 403: {$label} mess_id mismatch", [
                'user_id'       => Auth::id(),
                'mess_id'       => $mess->id,
                'model_mess_id' => $model->mess_id,
                'url'           => request()->fullUrl(),
            ]);
            abort(redirect()->route('mess.index')->with('error', "This {$label} does not belong to the selected mess."));
        }
    }
}
