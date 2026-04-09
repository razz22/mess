<!DOCTYPE html>
<html lang="bn">
<head>
<meta charset="UTF-8">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body {
    font-family: 'DejaVu Sans', sans-serif;
    font-size: 10pt;
    color: #000;
    background: #fff;
    padding: 20px 30px;
}
.page { width: 100%; }

/* ─── Header ─── */
.header-table { width: 100%; border-collapse: collapse; margin-bottom: 6px; }
.logo-cell { width: 70px; text-align: center; vertical-align: middle; }
.logo-circle {
    width: 55px; height: 55px; border-radius: 50%;
    background: #1a3a6b; color: #ffd700;
    display: inline-block; text-align: center; line-height: 55px;
    font-size: 24pt; font-weight: bold;
}
.title-cell { text-align: center; vertical-align: middle; }
.police-title { font-size: 18pt; font-weight: bold; color: #1a3a6b; }
.form-title {
    font-size: 14pt; font-weight: bold;
    border-bottom: 3px double #000;
    display: inline-block; padding-bottom: 2px;
    margin-top: 4px;
}
.top-right-cell {
    width: 180px; vertical-align: top;
    border: 1px solid #000; padding: 4px 6px;
    font-size: 8.5pt;
}
.top-right-table { width: 100%; border-collapse: collapse; }
.top-right-table td { padding: 2px 0; }
.top-info-row { margin-bottom: 3px; font-size: 9pt; }
.photo-box {
    width: 80px; height: 100px;
    border: 1px solid #000;
    float: left; margin-right: 10px;
    text-align: center; font-size: 8pt;
    color: #666; padding-top: 30px;
}

/* ─── Sub-header band ─── */
.sub-header {
    border: 1px solid #000;
    padding: 3px 10px;
    margin-bottom: 6px;
    display: flex; justify-content: space-between;
}
.sub-header-table { width: 100%; border-collapse: collapse; margin-bottom: 6px; }
.sub-header-table td { padding: 3px 6px; font-size: 9pt; }

/* ─── Field line ─── */
.field-line { margin-bottom: 5px; }
.field-line .label { font-weight: normal; }
.field-line .value {
    display: inline-block;
    border-bottom: 1px solid #000;
    min-width: 200px; padding-bottom: 1px;
}
.field-block { margin-bottom: 6px; }
.dotted-line {
    border-bottom: 1px dotted #555;
    display: block; min-height: 18px;
    padding-bottom: 2px; margin-top: 2px;
}

/* ─── Section numbers ─── */
.section-row { display: flex; gap: 6px; margin-bottom: 5px; align-items: baseline; }
.sec-num { font-weight: bold; white-space: nowrap; }

/* ─── Two-column layout ─── */
.two-col { width: 100%; border-collapse: collapse; margin-bottom: 5px; }
.two-col td { padding: 2px 4px; vertical-align: top; }

/* ─── Table ─── */
.data-table { width: 100%; border-collapse: collapse; margin: 4px 0; }
.data-table th, .data-table td {
    border: 1px solid #000; padding: 4px 6px;
    text-align: left; font-size: 9pt;
}
.data-table th { background: #f0f0f0; font-weight: bold; text-align: center; }
.data-table td { min-height: 20px; }
.data-table .num-col { text-align: center; width: 35px; }

/* ─── Signature block ─── */
.sig-block { margin-top: 20px; }
.sig-table { width: 100%; border-collapse: collapse; }
.sig-table td { padding: 6px 10px; vertical-align: bottom; }
.sig-line { border-top: 1px solid #000; display: block; width: 180px; text-align: center; padding-top: 4px; font-size: 9pt; }

/* ─── Footer note ─── */
.footer-note { margin-top: 10px; font-size: 9pt; font-weight: bold; text-align: center; border-top: 1px solid #000; padding-top: 6px; }

/* ─── Inline dotted ─── */
.inline-dot { border-bottom: 1px dotted #555; display: inline-block; min-width: 120px; }
</style>
</head>
<body>
<div class="page">

    {{-- ═══ PAGE HEADER ═══ --}}
    <table class="header-table">
        <tr>
            <td class="logo-cell" rowspan="2">
                {{-- Photo box on left --}}
                <div style="border:1px solid #000; width:80px; height:100px; text-align:center; padding-top:5px; font-size:8pt; color:#555">
                    @if($form->passport_photo)
                    <img src="{{ public_path('storage/'.$form->passport_photo) }}" style="width:78px;height:98px;object-fit:cover">
                    @else
                    ভাড়াটিয়ার এক কপি<br>পাসপোর্ট সাইজ ছবি
                    @endif
                </div>
            </td>
            <td class="title-cell">
                <div style="font-size:7pt; margin-bottom:3px">
                    <span style="margin-right:20px">বিভাগ ঃ <span class="inline-dot">{{ $form->division }}</span></span>
                    <span>থানা ঃ <span class="inline-dot">{{ $form->police_station }}</span></span>
                </div>
                <div class="police-title">ঢাকা মেট্রোপলিটন পুলিশ</div>
                <div style="margin: 4px 0; font-size:13pt; font-weight:bold; text-decoration:underline; text-underline-offset:3px">
                    ভাড়াটিয়া নিবন্ধন ফরম
                </div>
            </td>
            <td class="top-right-cell" rowspan="2">
                <table class="top-right-table">
                    <tr><td>ফ্ল্যাট/তলা ঃ</td><td><span class="inline-dot" style="min-width:60px">{{ $form->flat_floor }}</span></td></tr>
                    <tr><td style="padding-top:4px">বাড়ি/হোল্ডিং ঃ</td><td><span class="inline-dot" style="min-width:60px">{{ $form->house_holding }}</span></td></tr>
                    <tr><td style="padding-top:4px">রাস্তা &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ঃ</td><td><span class="inline-dot" style="min-width:60px">{{ $form->road }}</span></td></tr>
                    <tr><td style="padding-top:4px">এলাকা &nbsp;&nbsp;&nbsp;&nbsp; ঃ</td><td><span class="inline-dot" style="min-width:60px">{{ $form->area }}</span></td></tr>
                    <tr><td style="padding-top:4px">পোস্ট কোড ঃ</td><td><span class="inline-dot" style="min-width:60px">{{ $form->post_code }}</span></td></tr>
                </table>
            </td>
        </tr>
    </table>

    <div style="height:8px"></div>

    {{-- ─── Row 1: Name ─── --}}
    <div class="field-block">
        <span class="sec-num">১. </span>ভাড়াটিয়া/বাড়ীওয়ালার নাম ঃ
        <span class="inline-dot" style="min-width:400px">{{ $form->tenant_name }}</span>
    </div>

    {{-- ─── Row 2: Father ─── --}}
    <div class="field-block">
        <span class="sec-num">২. </span>পিতার নাম ঃ
        <span class="inline-dot" style="min-width:420px">{{ $form->father_name }}</span>
    </div>

    {{-- ─── Row 3: DOB + Marital ─── --}}
    <table class="two-col">
        <tr>
            <td width="50%">
                <span class="sec-num">৩. </span>জন্ম তারিখ ঃ
                <span class="inline-dot" style="min-width:160px">{{ $form->date_of_birth?->format('d/m/Y') }}</span>
            </td>
            <td>
                বৈবাহিক অবস্থা ঃ
                <span class="inline-dot" style="min-width:160px">{{ $form->marital_status }}</span>
            </td>
        </tr>
    </table>

    {{-- ─── Row 4: Permanent address ─── --}}
    <div class="field-block">
        <span class="sec-num">৪. </span>স্থায়ী ঠিকানা ঃ
        <span class="inline-dot" style="min-width:430px">{{ $form->permanent_address }}</span>
    </div>
    <div style="margin-bottom:5px; padding-left:20px">
        <span class="inline-dot" style="min-width:490px">{{ strlen($form->permanent_address ?? '') > 80 ? '' : '&nbsp;' }}</span>
    </div>

    {{-- ─── Row 5: Profession ─── --}}
    <div class="field-block">
        <span class="sec-num">৫. </span>পেশা ও প্রতিষ্ঠান/কর্মস্থলের ঠিকানা ঃ
        <span class="inline-dot" style="min-width:300px">{{ $form->profession_workplace }}</span>
    </div>

    {{-- ─── Row 6: Religion + Education ─── --}}
    <table class="two-col">
        <tr>
            <td width="40%">
                <span class="sec-num">৬. </span>ধর্ম ঃ
                <span class="inline-dot" style="min-width:130px">{{ $form->religion }}</span>
            </td>
            <td>
                শিক্ষাগত যোগ্যতা ঃ
                <span class="inline-dot" style="min-width:180px">{{ $form->education }}</span>
            </td>
        </tr>
    </table>

    {{-- ─── Row 7: Mobile + Email ─── --}}
    <table class="two-col">
        <tr>
            <td width="50%">
                <span class="sec-num">৭. </span>মোবাইল নম্বর
                <span class="inline-dot" style="min-width:160px">{{ $form->mobile }}</span>
            </td>
            <td>
                ই-মেইল আইডি ঃ
                <span class="inline-dot" style="min-width:160px">{{ $form->email }}</span>
            </td>
        </tr>
    </table>

    {{-- ─── Row 8: NID ─── --}}
    <div class="field-block">
        <span class="sec-num">৮. </span>জাতীয় পরিচয়পত্র নম্বর ঃ
        <span class="inline-dot" style="min-width:380px">{{ $form->nid_number }}</span>
    </div>

    {{-- ─── Row 9: Passport ─── --}}
    <div class="field-block">
        <span class="sec-num">৯. </span>পাসপোর্ট নম্বর (যদি থাকে) ঃ
        <span class="inline-dot" style="min-width:380px">{{ $form->passport_number }}</span>
    </div>

    {{-- ─── Row 10: Emergency ─── --}}
    <div style="margin-bottom:4px"><span class="sec-num">১০. </span>জরুরী যোগাযোগ ঃ</div>
    <table class="two-col" style="padding-left:20px">
        <tr>
            <td width="50%">
                (ক) নাম ঃ <span class="inline-dot" style="min-width:160px">{{ $form->emergency_name }}</span>
            </td>
            <td>
                (খ) সম্পর্ক ঃ <span class="inline-dot" style="min-width:160px">{{ $form->emergency_relation }}</span>
            </td>
        </tr>
        <tr>
            <td style="padding-top:5px">
                (গ) ঠিকানা ঃ <span class="inline-dot" style="min-width:160px">{{ $form->emergency_address }}</span>
            </td>
            <td style="padding-top:5px">
                (ঘ) মোবাইল নম্বর ঃ <span class="inline-dot" style="min-width:130px">{{ $form->emergency_mobile }}</span>
            </td>
        </tr>
    </table>

    <div style="height:6px"></div>

    {{-- ─── Row 11: Family Table ─── --}}
    <div style="margin-bottom:3px"><span class="sec-num">১১. </span>পরিবার / মেসের সঙ্গীয় সদস্যদের বিবরণ ঃ</div>
    <table class="data-table">
        <thead>
            <tr>
                <th class="num-col">ক্রঃ নং</th>
                <th>নাম</th>
                <th width="60">বয়স</th>
                <th width="150">পেশা</th>
                <th width="120">মোবাইল নম্বর</th>
            </tr>
        </thead>
        <tbody>
            @php
                $family = $form->family_members ?? [];
                while(count($family) < 3) $family[] = ['name'=>'','age'=>'','profession'=>'','mobile'=>''];
            @endphp
            @foreach($family as $i => $fm)
            <tr>
                <td class="num-col">{{ $i + 1 }}</td>
                <td style="min-height:18px">{{ $fm['name'] ?? '' }}</td>
                <td>{{ $fm['age'] ?? '' }}</td>
                <td>{{ $fm['profession'] ?? '' }}</td>
                <td>{{ $fm['mobile'] ?? '' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="height:5px"></div>

    {{-- ─── Row 12: Housekeeper ─── --}}
    <table class="two-col">
        <tr>
            <td width="55%">
                <span class="sec-num">১২. </span>গৃহকর্মীর নাম ঃ
                <span class="inline-dot" style="min-width:150px">{{ $form->housekeeper_name }}</span>
            </td>
            <td>
                জাতীয় পরিচয়পত্র নং ঃ
                <span class="inline-dot" style="min-width:120px">{{ $form->housekeeper_nid }}</span>
            </td>
        </tr>
        <tr>
            <td style="padding-top:4px">
                মোবাইল নম্বর ঃ
                <span class="inline-dot" style="min-width:170px">{{ $form->housekeeper_mobile }}</span>
            </td>
            <td style="padding-top:4px">
                স্থায়ী ঠিকানা ঃ
                <span class="inline-dot" style="min-width:140px">{{ $form->housekeeper_address }}</span>
            </td>
        </tr>
    </table>

    <div style="height:4px"></div>

    {{-- ─── Row 13: Driver ─── --}}
    <table class="two-col">
        <tr>
            <td width="55%">
                <span class="sec-num">১৩. </span>ড্রাইভারের নাম ঃ
                <span class="inline-dot" style="min-width:150px">{{ $form->driver_name }}</span>
            </td>
            <td>
                জাতীয় পরিচয়পত্র নং ঃ
                <span class="inline-dot" style="min-width:120px">{{ $form->driver_nid }}</span>
            </td>
        </tr>
        <tr>
            <td style="padding-top:4px">
                মোবাইল নম্বর ঃ
                <span class="inline-dot" style="min-width:170px">{{ $form->driver_mobile }}</span>
            </td>
            <td style="padding-top:4px">
                স্থায়ী ঠিকানা ঃ
                <span class="inline-dot" style="min-width:140px">{{ $form->driver_address }}</span>
            </td>
        </tr>
    </table>

    <div style="height:4px"></div>

    {{-- ─── Row 14: Previous landlord ─── --}}
    <table class="two-col">
        <tr>
            <td width="55%">
                <span class="sec-num">১৪. </span>পূর্ববর্তী বাড়িওয়ালার নাম ঃ
                <span class="inline-dot" style="min-width:140px">{{ $form->prev_landlord_name }}</span>
            </td>
            <td>
                মোবাইল নম্বর ঃ
                <span class="inline-dot" style="min-width:140px">{{ $form->prev_landlord_mobile }}</span>
            </td>
        </tr>
    </table>
    <div class="field-block" style="padding-left:20px">
        ঠিকানা ঃ <span class="inline-dot" style="min-width:420px">{{ $form->prev_landlord_address }}</span>
    </div>

    {{-- ─── Row 15: Reason ─── --}}
    <div class="field-block">
        <span class="sec-num">১৫. </span>পূর্ববর্তী বাসা ছাড়ার কারণ ঃ
        <span class="inline-dot" style="min-width:380px">{{ $form->reason_leaving }}</span>
    </div>

    {{-- ─── Row 16: Current landlord ─── --}}
    <table class="two-col">
        <tr>
            <td width="55%">
                <span class="sec-num">১৬. </span>বর্তমান বাড়িওয়ালার নাম ঃ
                <span class="inline-dot" style="min-width:150px">{{ $form->curr_landlord_name }}</span>
            </td>
            <td>
                মোবাইল নম্বর ঃ
                <span class="inline-dot" style="min-width:140px">{{ $form->curr_landlord_mobile }}</span>
            </td>
        </tr>
    </table>

    {{-- ─── Row 17: Living since ─── --}}
    <div class="field-block">
        <span class="sec-num">১৭. </span>বর্তমান বাড়িতে কোন তারিখ থেকে বসবাস ঃ
        <span class="inline-dot" style="min-width:320px">{{ $form->living_since?->format('d/m/Y') }}</span>
    </div>

    {{-- ─── Signature Block ─── --}}
    <div style="margin-top:24px">
        <table width="100%" style="border-collapse:collapse">
            <tr>
                <td width="50%" style="padding:0 10px; vertical-align:bottom">
                    <div style="border-top:1px solid #000; display:inline-block; min-width:160px; text-align:center; padding-top:4px; font-size:9pt">
                        তারিখ
                    </div>
                </td>
                <td width="50%" style="padding:0 10px; vertical-align:bottom; text-align:right">
                    <div style="border-top:1px solid #000; display:inline-block; min-width:200px; text-align:center; padding-top:4px; font-size:9pt">
                        ভাড়াটিয়ার স্বাক্ষর
                    </div>
                </td>
            </tr>
        </table>
    </div>

    {{-- ─── Footer Note ─── --}}
    <div class="footer-note">
        বিঃ দ্রঃ এই ফরমের একটি কপি বাড়ির মালিক অবশ্যই সংরক্ষণ করবেন ।
    </div>

</div>
</body>
</html>
