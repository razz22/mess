<?php $page = "mess-members" ?>
@extends('layout.mainlayout')
@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4 class="fw-bold">ভাড়াটিয়া নিবন্ধন ফরম — {{ $form->tenant_name }}</h4>
            </div>
            <div class="page-btn d-flex gap-2">
                <a href="{{ route('mess.tenant-forms.download', [$mess->id, $form->id]) }}" class="btn btn-success btn-sm">
                    <i class="ti ti-download me-1"></i>Download PDF
                </a>
                <button class="btn btn-outline-secondary btn-sm" onclick="window.print()">
                    <i class="ti ti-printer me-1"></i>Print
                </button>
                <a href="{{ route('mess.tenant-forms.index', $mess->id) }}" class="btn btn-outline-secondary btn-sm">
                    <i class="ti ti-arrow-left me-1"></i>Back
                </a>
            </div>
        </div>

        <!-- Official Form Preview -->
        <div class="card shadow" id="formPreview" style="max-width:800px;margin:0 auto">
            <div class="card-body p-4" style="font-family:'Segoe UI',sans-serif">

                {{-- Header --}}
                <div class="d-flex align-items-start gap-3 mb-3">
                    {{-- Photo box --}}
                    <div style="border:1px solid #000;width:80px;height:100px;flex-shrink:0;overflow:hidden;text-align:center;font-size:10px;color:#666;display:flex;align-items:center;justify-content:center">
                        @if($form->passport_photo)
                        <img src="{{ asset('storage/'.$form->passport_photo) }}" style="width:80px;height:100px;object-fit:cover">
                        @else
                        <span>ভাড়াটিয়ার ছবি</span>
                        @endif
                    </div>
                    {{-- Title --}}
                    <div class="flex-grow-1 text-center">
                        <div class="small text-muted mb-1">
                            বিভাগ ঃ <strong>{{ $form->division }}</strong> &nbsp;&nbsp;
                            থানা ঃ <strong>{{ $form->police_station }}</strong>
                        </div>
                        <h4 class="fw-bold text-primary mb-1">ঢাকা মেট্রোপলিটন পুলিশ</h4>
                        <h5 class="fw-bold" style="text-decoration:underline;text-underline-offset:4px">ভাড়াটিয়া নিবন্ধন ফরম</h5>
                    </div>
                    {{-- Location box --}}
                    <div style="border:1px solid #000;padding:6px 10px;min-width:160px;font-size:12px">
                        <div>ফ্ল্যাট/তলা ঃ <strong>{{ $form->flat_floor }}</strong></div>
                        <div>বাড়ি/হোল্ডিং ঃ <strong>{{ $form->house_holding }}</strong></div>
                        <div>রাস্তা ঃ <strong>{{ $form->road }}</strong></div>
                        <div>এলাকা ঃ <strong>{{ $form->area }}</strong></div>
                        <div>পোস্ট কোড ঃ <strong>{{ $form->post_code }}</strong></div>
                    </div>
                </div>

                @php
                    function fline($label, $value, $fullWidth = false) {
                        $val = $value ?: str_repeat(' ', 40);
                        echo '<div style="margin-bottom:5px;font-size:13px">' . e($label) . ' <span style="border-bottom:1px solid #000;display:inline-block;min-width:' . ($fullWidth ? '400px' : '200px') . ';padding-bottom:1px">' . e($val) . '</span></div>';
                    }
                @endphp

                {{-- Fields --}}
                <div style="font-size:13px;line-height:1.9">
                    <div><strong>১.</strong> ভাড়াটিয়া/বাড়ীওয়ালার নাম ঃ <span style="border-bottom:1px solid #000;display:inline-block;min-width:380px">{{ $form->tenant_name }}</span></div>
                    <div><strong>২.</strong> পিতার নাম ঃ <span style="border-bottom:1px solid #000;display:inline-block;min-width:400px">{{ $form->father_name }}</span></div>
                    <div class="d-flex gap-4">
                        <div><strong>৩.</strong> জন্ম তারিখ ঃ <span style="border-bottom:1px solid #000;display:inline-block;min-width:150px">{{ $form->date_of_birth?->format('d/m/Y') }}</span></div>
                        <div>বৈবাহিক অবস্থা ঃ <span style="border-bottom:1px solid #000;display:inline-block;min-width:150px">{{ $form->marital_status }}</span></div>
                    </div>
                    <div><strong>৪.</strong> স্থায়ী ঠিকানা ঃ <span style="border-bottom:1px solid #000;display:inline-block;min-width:380px">{{ $form->permanent_address }}</span></div>
                    <div style="border-bottom:1px solid #000;min-height:18px;margin-bottom:5px">&nbsp;</div>
                    <div><strong>৫.</strong> পেশা ও প্রতিষ্ঠান/কর্মস্থলের ঠিকানা ঃ <span style="border-bottom:1px solid #000;display:inline-block;min-width:300px">{{ $form->profession_workplace }}</span></div>
                    <div class="d-flex gap-4">
                        <div><strong>৬.</strong> ধর্ম ঃ <span style="border-bottom:1px solid #000;display:inline-block;min-width:120px">{{ $form->religion }}</span></div>
                        <div>শিক্ষাগত যোগ্যতা ঃ <span style="border-bottom:1px solid #000;display:inline-block;min-width:160px">{{ $form->education }}</span></div>
                    </div>
                    <div class="d-flex gap-4">
                        <div><strong>৭.</strong> মোবাইল নম্বর <span style="border-bottom:1px solid #000;display:inline-block;min-width:140px">{{ $form->mobile }}</span></div>
                        <div>ই-মেইল আইডি ঃ <span style="border-bottom:1px solid #000;display:inline-block;min-width:180px">{{ $form->email }}</span></div>
                    </div>
                    <div><strong>৮.</strong> জাতীয় পরিচয়পত্র নম্বর ঃ <span style="border-bottom:1px solid #000;display:inline-block;min-width:350px">{{ $form->nid_number }}</span></div>
                    <div><strong>৯.</strong> পাসপোর্ট নম্বর (যদি থাকে) ঃ <span style="border-bottom:1px solid #000;display:inline-block;min-width:340px">{{ $form->passport_number }}</span></div>

                    {{-- Emergency --}}
                    <div><strong>১০.</strong> জরুরী যোগাযোগ ঃ</div>
                    <div class="d-flex gap-4 ps-3">
                        <div>(ক) নাম ঃ <span style="border-bottom:1px solid #000;display:inline-block;min-width:150px">{{ $form->emergency_name }}</span></div>
                        <div>(খ) সম্পর্ক ঃ <span style="border-bottom:1px solid #000;display:inline-block;min-width:150px">{{ $form->emergency_relation }}</span></div>
                    </div>
                    <div class="d-flex gap-4 ps-3">
                        <div>(গ) ঠিকানা ঃ <span style="border-bottom:1px solid #000;display:inline-block;min-width:180px">{{ $form->emergency_address }}</span></div>
                        <div>(ঘ) মোবাইল নম্বর ঃ <span style="border-bottom:1px solid #000;display:inline-block;min-width:140px">{{ $form->emergency_mobile }}</span></div>
                    </div>
                </div>

                {{-- Family Table --}}
                <div style="margin-top:8px;margin-bottom:5px;font-size:13px"><strong>১১.</strong> পরিবার / মেসের সঙ্গীয় সদস্যদের বিবরণ ঃ</div>
                <table style="width:100%;border-collapse:collapse;font-size:12px">
                    <thead>
                        <tr style="background:#f0f0f0">
                            <th style="border:1px solid #000;padding:4px;text-align:center;width:40px">ক্রঃ নং</th>
                            <th style="border:1px solid #000;padding:4px;text-align:center">নাম</th>
                            <th style="border:1px solid #000;padding:4px;text-align:center;width:60px">বয়স</th>
                            <th style="border:1px solid #000;padding:4px;text-align:center;width:160px">পেশা</th>
                            <th style="border:1px solid #000;padding:4px;text-align:center;width:130px">মোবাইল নম্বর</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $fam = $form->family_members ?? []; while(count($fam)<3) $fam[]=['name'=>'','age'=>'','profession'=>'','mobile'=>'']; @endphp
                        @foreach($fam as $i => $fm)
                        <tr>
                            <td style="border:1px solid #000;padding:4px 6px;text-align:center">{{ $i+1 }}</td>
                            <td style="border:1px solid #000;padding:4px 6px;min-height:20px">{{ $fm['name'] ?? '' }}</td>
                            <td style="border:1px solid #000;padding:4px 6px">{{ $fm['age'] ?? '' }}</td>
                            <td style="border:1px solid #000;padding:4px 6px">{{ $fm['profession'] ?? '' }}</td>
                            <td style="border:1px solid #000;padding:4px 6px">{{ $fm['mobile'] ?? '' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- Housekeeper --}}
                <div style="font-size:13px;line-height:2;margin-top:8px">
                    <div class="d-flex gap-4">
                        <div><strong>১২.</strong> গৃহকর্মীর নাম ঃ <span style="border-bottom:1px solid #000;display:inline-block;min-width:160px">{{ $form->housekeeper_name }}</span></div>
                        <div>জাতীয় পরিচয়পত্র নং ঃ <span style="border-bottom:1px solid #000;display:inline-block;min-width:130px">{{ $form->housekeeper_nid }}</span></div>
                    </div>
                    <div class="d-flex gap-4">
                        <div>মোবাইল নম্বর ঃ <span style="border-bottom:1px solid #000;display:inline-block;min-width:180px">{{ $form->housekeeper_mobile }}</span></div>
                        <div>স্থায়ী ঠিকানা ঃ <span style="border-bottom:1px solid #000;display:inline-block;min-width:170px">{{ $form->housekeeper_address }}</span></div>
                    </div>
                    <div class="d-flex gap-4">
                        <div><strong>১৩.</strong> ড্রাইভারের নাম ঃ <span style="border-bottom:1px solid #000;display:inline-block;min-width:160px">{{ $form->driver_name }}</span></div>
                        <div>জাতীয় পরিচয়পত্র নং ঃ <span style="border-bottom:1px solid #000;display:inline-block;min-width:130px">{{ $form->driver_nid }}</span></div>
                    </div>
                    <div class="d-flex gap-4">
                        <div>মোবাইল নম্বর ঃ <span style="border-bottom:1px solid #000;display:inline-block;min-width:180px">{{ $form->driver_mobile }}</span></div>
                        <div>স্থায়ী ঠিকানা ঃ <span style="border-bottom:1px solid #000;display:inline-block;min-width:170px">{{ $form->driver_address }}</span></div>
                    </div>
                    <div class="d-flex gap-4">
                        <div><strong>১৪.</strong> পূর্ববর্তী বাড়িওয়ালার নাম ঃ <span style="border-bottom:1px solid #000;display:inline-block;min-width:130px">{{ $form->prev_landlord_name }}</span></div>
                        <div>মোবাইল নম্বর ঃ <span style="border-bottom:1px solid #000;display:inline-block;min-width:130px">{{ $form->prev_landlord_mobile }}</span></div>
                    </div>
                    <div>ঠিকানা ঃ <span style="border-bottom:1px solid #000;display:inline-block;min-width:420px">{{ $form->prev_landlord_address }}</span></div>
                    <div><strong>১৫.</strong> পূর্ববর্তী বাসা ছাড়ার কারণ ঃ <span style="border-bottom:1px solid #000;display:inline-block;min-width:350px">{{ $form->reason_leaving }}</span></div>
                    <div class="d-flex gap-4">
                        <div><strong>১৬.</strong> বর্তমান বাড়িওয়ালার নাম ঃ <span style="border-bottom:1px solid #000;display:inline-block;min-width:150px">{{ $form->curr_landlord_name }}</span></div>
                        <div>মোবাইল নম্বর ঃ <span style="border-bottom:1px solid #000;display:inline-block;min-width:130px">{{ $form->curr_landlord_mobile }}</span></div>
                    </div>
                    <div><strong>১৭.</strong> বর্তমান বাড়িতে কোন তারিখ থেকে বসবাস ঃ <span style="border-bottom:1px solid #000;display:inline-block;min-width:280px">{{ $form->living_since?->format('d/m/Y') }}</span></div>
                </div>

                {{-- Signature --}}
                <div class="d-flex justify-content-between mt-5 pt-2">
                    <div style="border-top:1px solid #000;min-width:160px;text-align:center;padding-top:4px;font-size:12px">তারিখ</div>
                    <div style="border-top:1px solid #000;min-width:200px;text-align:center;padding-top:4px;font-size:12px">ভাড়াটিয়ার স্বাক্ষর</div>
                </div>

                {{-- Footer --}}
                <div style="border-top:1px solid #000;margin-top:14px;padding-top:6px;text-align:center;font-weight:bold;font-size:12px">
                    বিঃ দ্রঃ এই ফরমের একটি কপি বাড়ির মালিক অবশ্যই সংরক্ষণ করবেন ।
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .page-header, .sidebar, .header, nav, .page-btn { display: none !important; }
    .page-wrapper { padding: 0 !important; }
    #formPreview { box-shadow: none !important; max-width: 100% !important; }
}
</style>
@endsection
