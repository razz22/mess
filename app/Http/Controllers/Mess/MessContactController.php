<?php

namespace App\Http\Controllers\Mess;

use App\Http\Controllers\Controller;
use App\Models\Mess;
use App\Models\MessContact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessContactController extends Controller
{
    public function store(Request $request, Mess $mess)
    {
        $this->authorizeManager($mess);

        $request->validate([
            'name'  => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'label' => 'required|string|max:50',
            'notes' => 'nullable|string|max:255',
        ]);

        MessContact::create([
            'mess_id' => $mess->id,
            'name'    => $request->name,
            'phone'   => $request->phone,
            'label'   => $request->label,
            'notes'   => $request->notes,
        ]);

        return back()->with('contact_success', 'Contact added.');
    }

    public function update(Request $request, Mess $mess, MessContact $contact)
    {
        $this->authorizeManager($mess);
        if ($contact->mess_id !== $mess->id) abort(403);

        $request->validate([
            'name'  => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'label' => 'required|string|max:50',
            'notes' => 'nullable|string|max:255',
        ]);

        $contact->update($request->only('name', 'phone', 'label', 'notes'));
        return back()->with('contact_success', 'Contact updated.');
    }

    public function destroy(Mess $mess, MessContact $contact)
    {
        $this->authorizeManager($mess);
        if ($contact->mess_id !== $mess->id) abort(403);

        $contact->delete();
        return back()->with('contact_success', 'Contact deleted.');
    }

    private function authorizeManager(Mess $mess): void
    {
        if (!Auth::user()->isManagerOf($mess->id)) {
            abort(403, 'Only managers and owners can manage contacts.');
        }
    }
}
