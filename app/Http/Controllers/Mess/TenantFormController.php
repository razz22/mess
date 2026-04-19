<?php

namespace App\Http\Controllers\Mess;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\AuthorizesMessAccess;
use App\Models\Mess;
use App\Models\MessMember;
use App\Models\TenantRegistrationForm;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TenantFormController extends Controller
{
    use AuthorizesMessAccess;
    /** Member fills / edits their own form */
    public function edit(Mess $mess)
    {
        $member = Auth::user()->getMembershipIn($mess->id);
        if (!$member) abort(403);

        $form = TenantRegistrationForm::firstOrNew(
            ['mess_id' => $mess->id, 'member_id' => $member->id],
            $this->prefillFromProfile($member)
        );

        return view('mess.tenant-form', compact('mess', 'member', 'form'));
    }

    /** Save form data */
    public function save(Request $request, Mess $mess)
    {
        $member = Auth::user()->getMembershipIn($mess->id);
        if (!$member) abort(403);

        $data = $request->validate([
            'flat_floor'             => 'nullable|string|max:50',
            'house_holding'          => 'nullable|string|max:100',
            'road'                   => 'nullable|string|max:100',
            'area'                   => 'nullable|string|max:100',
            'post_code'              => 'nullable|string|max:20',
            'division'               => 'nullable|string|max:100',
            'police_station'         => 'nullable|string|max:100',
            'tenant_name'            => 'nullable|string|max:200',
            'father_name'            => 'nullable|string|max:200',
            'date_of_birth'          => 'nullable|date',
            'marital_status'         => 'nullable|in:অবিবাহিত,বিবাহিত,তালাকপ্রাপ্ত,বিধবা/বিপত্নীক',
            'permanent_address'      => 'nullable|string|max:500',
            'profession_workplace'   => 'nullable|string|max:500',
            'religion'               => 'nullable|string|max:50',
            'education'              => 'nullable|string|max:100',
            'mobile'                 => 'nullable|string|max:20',
            'email'                  => 'nullable|email|max:200',
            'nid_number'             => 'nullable|string|max:50',
            'passport_number'        => 'nullable|string|max:50',
            'emergency_name'         => 'nullable|string|max:200',
            'emergency_relation'     => 'nullable|string|max:100',
            'emergency_address'      => 'nullable|string|max:500',
            'emergency_mobile'       => 'nullable|string|max:20',
            'family_members'         => 'nullable|array|max:10',
            'family_members.*.name'       => 'nullable|string|max:200',
            'family_members.*.age'        => 'nullable|string|max:10',
            'family_members.*.profession' => 'nullable|string|max:100',
            'family_members.*.mobile'     => 'nullable|string|max:20',
            'housekeeper_name'       => 'nullable|string|max:200',
            'housekeeper_nid'        => 'nullable|string|max:50',
            'housekeeper_mobile'     => 'nullable|string|max:20',
            'housekeeper_address'    => 'nullable|string|max:500',
            'driver_name'            => 'nullable|string|max:200',
            'driver_nid'             => 'nullable|string|max:50',
            'driver_mobile'          => 'nullable|string|max:20',
            'driver_address'         => 'nullable|string|max:500',
            'prev_landlord_name'     => 'nullable|string|max:200',
            'prev_landlord_mobile'   => 'nullable|string|max:20',
            'prev_landlord_address'  => 'nullable|string|max:500',
            'reason_leaving'         => 'nullable|string|max:500',
            'curr_landlord_name'     => 'nullable|string|max:200',
            'curr_landlord_mobile'   => 'nullable|string|max:20',
            'living_since'           => 'nullable|date',
            'passport_photo'         => 'nullable|image|max:2048',
        ]);

        // Handle photo upload
        $form = TenantRegistrationForm::where([
            'mess_id'   => $mess->id,
            'member_id' => $member->id,
        ])->first();

        if ($request->hasFile('passport_photo')) {
            if ($form && $form->passport_photo) {
                Storage::disk('public')->delete($form->passport_photo);
            }
            $data['passport_photo'] = $request->file('passport_photo')->store('tenant-photos', 'public');
        }

        // Clean empty family members
        if (isset($data['family_members'])) {
            $data['family_members'] = array_values(array_filter(
                $data['family_members'],
                fn($fm) => !empty($fm['name'])
            ));
        }

        $isSubmit = $request->boolean('submit_form');
        if ($isSubmit) {
            $data['submitted_at'] = now();
        }

        TenantRegistrationForm::updateOrCreate(
            ['mess_id' => $mess->id, 'member_id' => $member->id],
            $data
        );

        return back()->with('success', $isSubmit ? 'Form submitted successfully!' : 'Form saved as draft.');
    }

    /** Manager: list all submitted forms */
    public function index(Mess $mess)
    {
        if (!Auth::user()->isManagerOf($mess->id)) abort(403);

        $forms = TenantRegistrationForm::where('mess_id', $mess->id)
            ->with('member.user')
            ->orderByDesc('submitted_at')
            ->get();

        return view('mess.tenant-forms-list', compact('mess', 'forms'));
    }

    /** Manager: view a single form (printable) */
    public function view(Mess $mess, TenantRegistrationForm $form)
    {
        if ((int) $form->mess_id !== (int) $mess->id) abort(403);

        $canView = Auth::user()->isManagerOf($mess->id)
            || Auth::user()->getMembershipIn($mess->id)?->id === $form->member_id;
        if (!$canView) abort(403);

        return view('mess.tenant-form-view', compact('mess', 'form'));
    }

    /** Download form as PDF */
    public function download(Mess $mess, TenantRegistrationForm $form)
    {
        if ((int) $form->mess_id !== (int) $mess->id) abort(403);

        $canView = Auth::user()->isManagerOf($mess->id)
            || Auth::user()->getMembershipIn($mess->id)?->id === $form->member_id;
        if (!$canView) abort(403);

        $form->load('member.user');

        $fontDir = storage_path('fonts');
        $this->ensureKalpurushRegistered($fontDir);

        $pdf = Pdf::loadView('mess.tenant-form-pdf', compact('mess', 'form'))
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'defaultFont'          => 'kalpurush',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => false,
                'isPhpEnabled'         => false,
                'fontDir'              => $fontDir,
                'fontCache'            => $fontDir,
                'dpi'                  => 150,
            ]);

        $name = 'tenant-form-' . str_replace(' ', '-', $form->member->user->name) . '.pdf';
        return $pdf->download($name);
    }

    private function ensureKalpurushRegistered(string $fontDir): void
    {
        $cacheFile = $fontDir . '/installed-fonts.json';
        $cached = is_file($cacheFile) ? json_decode(file_get_contents($cacheFile), true) : [];
        if (isset($cached['kalpurush'])) {
            return;
        }

        $options = new \Dompdf\Options([
            'fontDir'         => $fontDir,
            'fontCache'       => $fontDir,
            'isRemoteEnabled' => true,
            'chroot'          => [$fontDir],
        ]);
        $dompdf = new \Dompdf\Dompdf($options);
        $fontUrl = 'file://' . str_replace('\\', '/', $fontDir) . '/kalpurush.ttf';
        $dompdf->getFontMetrics()->registerFont(
            ['family' => 'kalpurush', 'weight' => 'normal', 'style' => 'normal'],
            $fontUrl
        );
        $dompdf->getFontMetrics()->saveFontFamilies();
    }

    private function prefillFromProfile(MessMember $member): array
    {
        $u = $member->user;
        return [
            'tenant_name'    => $u->name,
            'mobile'         => $u->phone,
            'email'          => $u->email,
            'date_of_birth'  => $u->date_of_birth,
            'permanent_address' => $u->address,
            'nid_number'     => null,
            'emergency_name'   => $u->emergency_contact_name,
            'emergency_mobile' => $u->emergency_contact_phone,
            'emergency_relation' => $u->emergency_contact_relation,
            'profession_workplace' => $u->organization,
        ];
    }
}
