<?php $page = "mess-members" ?>
@extends('layout.mainlayout')
@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4 class="fw-bold">ভাড়াটিয়া নিবন্ধন ফরম</h4>
                <h6>{{ $mess->name }} — {{ $member->user->name }}</h6>
            </div>
            <div class="page-btn d-flex gap-2">
                @if($form->exists && $form->isSubmitted())
                <a href="{{ route('mess.tenant-forms.download', [$mess->id, $form->id]) }}" class="btn btn-success btn-sm">
                    <i class="ti ti-download me-1"></i>Download PDF
                </a>
                @endif
                <a href="{{ route('mess.dashboard', $mess->id) }}" class="btn btn-outline-secondary btn-sm">
                    <i class="ti ti-arrow-left me-1"></i>Back
                </a>
            </div>
        </div>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif
        @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @if($form->exists && $form->isSubmitted())
        <div class="alert alert-success d-flex align-items-center gap-3">
            <i class="ti ti-circle-check fs-3"></i>
            <div>
                <strong>Form submitted on {{ $form->submitted_at->format('d M Y') }}</strong><br>
                <small>You can still edit and resubmit. Download PDF to get the printable version.</small>
            </div>
        </div>
        @endif

        <form action="{{ route('mess.tenant-form.save', $mess->id) }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- ─────────── HEADER / LOCATION ─────────── --}}
            <div class="card mb-3">
                <div class="card-header bg-primary text-white py-2">
                    <h6 class="mb-0 text-center fw-bold"><i class="ti ti-building me-2"></i>বাড়ির তথ্য (Property Details)</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">ফ্ল্যাট/তলা (Flat/Floor)</label>
                            <input type="text" name="flat_floor" class="form-control" value="{{ old('flat_floor', $form->flat_floor) }}" placeholder="e.g. 3rd Floor, Flat-A">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">বাড়ি/হোল্ডিং (House/Holding)</label>
                            <input type="text" name="house_holding" class="form-control" value="{{ old('house_holding', $form->house_holding) }}" placeholder="House No.">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">রাস্তা (Road)</label>
                            <input type="text" name="road" class="form-control" value="{{ old('road', $form->road) }}" placeholder="Road No/Name">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">এলাকা (Area)</label>
                            <input type="text" name="area" class="form-control" value="{{ old('area', $form->area) }}" placeholder="Area/Block">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">পোস্ট কোড (Post Code)</label>
                            <input type="text" name="post_code" class="form-control" value="{{ old('post_code', $form->post_code) }}" placeholder="1234">
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-semibold">বিভাগ (Division)</label>
                            <select name="division" class="form-select">
                                <option value="">— নির্বাচন করুন —</option>
                                @foreach(['ঢাকা','চট্টগ্রাম','রাজশাহী','খুলনা','বরিশাল','সিলেট','রংপুর','ময়মনসিংহ'] as $d)
                                <option value="{{ $d }}" {{ old('division', $form->division) === $d ? 'selected' : '' }}>{{ $d }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">থানা (Police Station)</label>
                            <input type="text" name="police_station" class="form-control" value="{{ old('police_station', $form->police_station) }}" placeholder="থানার নাম">
                        </div>
                    </div>
                </div>
            </div>

            {{-- ─────────── PERSONAL INFO ─────────── --}}
            <div class="card mb-3">
                <div class="card-header bg-success text-white py-2">
                    <h6 class="mb-0 fw-bold"><i class="ti ti-user me-2"></i>ব্যক্তিগত তথ্য (Personal Information)</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3 align-items-start">
                        <div class="col-md-9">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">১. ভাড়াটিয়ার নাম (Tenant Name) <span class="text-danger">*</span></label>
                                    <input type="text" name="tenant_name" class="form-control" value="{{ old('tenant_name', $form->tenant_name) }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">২. পিতার নাম (Father's Name)</label>
                                    <input type="text" name="father_name" class="form-control" value="{{ old('father_name', $form->father_name) }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">৩. জন্ম তারিখ (Date of Birth)</label>
                                    <input type="date" name="date_of_birth" class="form-control" value="{{ old('date_of_birth', $form->date_of_birth?->format('Y-m-d')) }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">বৈবাহিক অবস্থা (Marital Status)</label>
                                    <select name="marital_status" class="form-select">
                                        <option value="">— নির্বাচন করুন —</option>
                                        @foreach(['অবিবাহিত','বিবাহিত','তালাকপ্রাপ্ত','বিধবা/বিপত্নীক'] as $ms)
                                        <option value="{{ $ms }}" {{ old('marital_status', $form->marital_status) === $ms ? 'selected' : '' }}>{{ $ms }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <!-- Passport Photo -->
                        <div class="col-md-3 text-center">
                            <label class="form-label fw-semibold d-block">ভাড়াটিয়ার ছবি</label>
                            <div id="photoPreview" style="width:100px;height:130px;border:2px dashed #aaa;margin:0 auto 8px;display:flex;align-items:center;justify-content:center;overflow:hidden;background:#f8f9fa;border-radius:4px">
                                @if($form->passport_photo)
                                <img src="{{ asset('storage/'.$form->passport_photo) }}" style="width:100%;height:100%;object-fit:cover">
                                @else
                                <span class="text-muted small">পাসপোর্ট সাইজ ছবি</span>
                                @endif
                            </div>
                            <label class="btn btn-sm btn-outline-primary">
                                <i class="ti ti-camera me-1"></i>Upload
                                <input type="file" name="passport_photo" accept="image/*" class="d-none" onchange="previewPhoto(this)">
                            </label>
                            <div class="form-text">JPG/PNG max 2MB</div>
                        </div>
                    </div>

                    <div class="row g-3 mt-1">
                        <div class="col-12">
                            <label class="form-label fw-semibold">৪. স্থায়ী ঠিকানা (Permanent Address)</label>
                            <textarea name="permanent_address" class="form-control" rows="2">{{ old('permanent_address', $form->permanent_address) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ─────────── PROFESSIONAL ─────────── --}}
            <div class="card mb-3">
                <div class="card-header bg-info text-white py-2">
                    <h6 class="mb-0 fw-bold"><i class="ti ti-briefcase me-2"></i>পেশা ও যোগাযোগ (Profession & Contact)</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">৫. পেশা ও প্রতিষ্ঠান/কর্মস্থলের ঠিকানা</label>
                            <textarea name="profession_workplace" class="form-control" rows="2" placeholder="পেশা ও কর্মস্থলের পূর্ণ ঠিকানা লিখুন">{{ old('profession_workplace', $form->profession_workplace) }}</textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">৬. ধর্ম (Religion)</label>
                            <select name="religion" class="form-select">
                                <option value="">— নির্বাচন করুন —</option>
                                @foreach(['ইসলাম','হিন্দু','বৌদ্ধ','খ্রিষ্টান','অন্যান্য'] as $r)
                                <option value="{{ $r }}" {{ old('religion', $form->religion) === $r ? 'selected' : '' }}>{{ $r }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">শিক্ষাগত যোগ্যতা (Education)</label>
                            <input type="text" name="education" class="form-control" value="{{ old('education', $form->education) }}" placeholder="e.g. SSC, HSC, Graduate">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">৭. মোবাইল নম্বর</label>
                            <input type="text" name="mobile" class="form-control" value="{{ old('mobile', $form->mobile) }}" placeholder="01XXXXXXXXX">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">ই-মেইল আইডি (Email)</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $form->email) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">৮. জাতীয় পরিচয়পত্র নম্বর (NID)</label>
                            <input type="text" name="nid_number" class="form-control" value="{{ old('nid_number', $form->nid_number) }}" placeholder="NID number">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">৯. পাসপোর্ট নম্বর (যদি থাকে)</label>
                            <input type="text" name="passport_number" class="form-control" value="{{ old('passport_number', $form->passport_number) }}" placeholder="Optional">
                        </div>
                    </div>
                </div>
            </div>

            {{-- ─────────── EMERGENCY CONTACT ─────────── --}}
            <div class="card mb-3">
                <div class="card-header bg-warning py-2">
                    <h6 class="mb-0 fw-bold"><i class="ti ti-phone-call me-2"></i>১০. জরুরী যোগাযোগ (Emergency Contact)</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">(ক) নাম (Name)</label>
                            <input type="text" name="emergency_name" class="form-control" value="{{ old('emergency_name', $form->emergency_name) }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">(খ) সম্পর্ক (Relation)</label>
                            <input type="text" name="emergency_relation" class="form-control" value="{{ old('emergency_relation', $form->emergency_relation) }}" placeholder="পিতা, মাতা, স্বামী/স্ত্রী...">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">(গ) ঠিকানা (Address)</label>
                            <input type="text" name="emergency_address" class="form-control" value="{{ old('emergency_address', $form->emergency_address) }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">(ঘ) মোবাইল নম্বর</label>
                            <input type="text" name="emergency_mobile" class="form-control" value="{{ old('emergency_mobile', $form->emergency_mobile) }}" placeholder="01XXXXXXXXX">
                        </div>
                    </div>
                </div>
            </div>

            {{-- ─────────── FAMILY MEMBERS ─────────── --}}
            <div class="card mb-3">
                <div class="card-header bg-secondary text-white py-2 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold"><i class="ti ti-users me-2"></i>১১. পরিবার / মেসের সঙ্গীয় সদস্যদের বিবরণ</h6>
                    <button type="button" class="btn btn-sm btn-light" onclick="addFamilyRow()">
                        <i class="ti ti-plus me-1"></i>Add Row
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">ক্রঃ নং</th>
                                    <th>নাম (Name)</th>
                                    <th width="12%">বয়স (Age)</th>
                                    <th>পেশা (Profession)</th>
                                    <th>মোবাইল নম্বর</th>
                                    <th width="5%"></th>
                                </tr>
                            </thead>
                            <tbody id="familyBody">
                                @php
                                    $familyRows = old('family_members', $form->family_members ?? []);
                                    while(count($familyRows) < 3) $familyRows[] = ['name'=>'','age'=>'','profession'=>'','mobile'=>''];
                                @endphp
                                @foreach($familyRows as $i => $fm)
                                <tr>
                                    <td class="text-center fw-bold">{{ $i + 1 }}</td>
                                    <td><input type="text" name="family_members[{{ $i }}][name]" class="form-control form-control-sm" value="{{ $fm['name'] ?? '' }}"></td>
                                    <td><input type="text" name="family_members[{{ $i }}][age]" class="form-control form-control-sm" value="{{ $fm['age'] ?? '' }}"></td>
                                    <td><input type="text" name="family_members[{{ $i }}][profession]" class="form-control form-control-sm" value="{{ $fm['profession'] ?? '' }}"></td>
                                    <td><input type="text" name="family_members[{{ $i }}][mobile]" class="form-control form-control-sm" value="{{ $fm['mobile'] ?? '' }}"></td>
                                    <td class="text-center">
                                        @if($i >= 3)<button type="button" class="btn btn-xs btn-outline-danger" onclick="this.closest('tr').remove()"><i class="ti ti-x"></i></button>@endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- ─────────── HOUSEKEEPER ─────────── --}}
            <div class="card mb-3">
                <div class="card-header bg-light py-2">
                    <h6 class="mb-0 fw-bold"><i class="ti ti-home me-2 text-primary"></i>১২. গৃহকর্মীর তথ্য (Housekeeper Details)</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">নাম (Name)</label>
                            <input type="text" name="housekeeper_name" class="form-control" value="{{ old('housekeeper_name', $form->housekeeper_name) }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">জাতীয় পরিচয়পত্র নং</label>
                            <input type="text" name="housekeeper_nid" class="form-control" value="{{ old('housekeeper_nid', $form->housekeeper_nid) }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">মোবাইল নম্বর</label>
                            <input type="text" name="housekeeper_mobile" class="form-control" value="{{ old('housekeeper_mobile', $form->housekeeper_mobile) }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">স্থায়ী ঠিকানা</label>
                            <input type="text" name="housekeeper_address" class="form-control" value="{{ old('housekeeper_address', $form->housekeeper_address) }}">
                        </div>
                    </div>
                </div>
            </div>

            {{-- ─────────── DRIVER ─────────── --}}
            <div class="card mb-3">
                <div class="card-header bg-light py-2">
                    <h6 class="mb-0 fw-bold"><i class="ti ti-car me-2 text-info"></i>১৩. ড্রাইভারের তথ্য (Driver Details)</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">নাম (Name)</label>
                            <input type="text" name="driver_name" class="form-control" value="{{ old('driver_name', $form->driver_name) }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">জাতীয় পরিচয়পত্র নং</label>
                            <input type="text" name="driver_nid" class="form-control" value="{{ old('driver_nid', $form->driver_nid) }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">মোবাইল নম্বর</label>
                            <input type="text" name="driver_mobile" class="form-control" value="{{ old('driver_mobile', $form->driver_mobile) }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">স্থায়ী ঠিকানা</label>
                            <input type="text" name="driver_address" class="form-control" value="{{ old('driver_address', $form->driver_address) }}">
                        </div>
                    </div>
                </div>
            </div>

            {{-- ─────────── PREVIOUS / CURRENT LANDLORD ─────────── --}}
            <div class="card mb-3">
                <div class="card-header bg-light py-2">
                    <h6 class="mb-0 fw-bold"><i class="ti ti-building me-2 text-warning"></i>পূর্ববর্তী ও বর্তমান বাড়িওয়ালার তথ্য</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">১৪. পূর্ববর্তী বাড়িওয়ালার নাম</label>
                            <input type="text" name="prev_landlord_name" class="form-control" value="{{ old('prev_landlord_name', $form->prev_landlord_name) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">মোবাইল নম্বর</label>
                            <input type="text" name="prev_landlord_mobile" class="form-control" value="{{ old('prev_landlord_mobile', $form->prev_landlord_mobile) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">ঠিকানা (Address)</label>
                            <input type="text" name="prev_landlord_address" class="form-control" value="{{ old('prev_landlord_address', $form->prev_landlord_address) }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">১৫. পূর্ববর্তী বাসা ছাড়ার কারণ</label>
                            <textarea name="reason_leaving" class="form-control" rows="2">{{ old('reason_leaving', $form->reason_leaving) }}</textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">১৬. বর্তমান বাড়িওয়ালার নাম</label>
                            <input type="text" name="curr_landlord_name" class="form-control" value="{{ old('curr_landlord_name', $form->curr_landlord_name) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">মোবাইল নম্বর</label>
                            <input type="text" name="curr_landlord_mobile" class="form-control" value="{{ old('curr_landlord_mobile', $form->curr_landlord_mobile) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">১৭. বর্তমান বাড়িতে কোন তারিখ থেকে বসবাস</label>
                            <input type="date" name="living_since" class="form-control" value="{{ old('living_since', $form->living_since?->format('Y-m-d')) }}">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Submit Buttons --}}
            <div class="card">
                <div class="card-body d-flex gap-3 align-items-center flex-wrap">
                    <button type="submit" name="submit_form" value="0" class="btn btn-outline-primary">
                        <i class="ti ti-device-floppy me-1"></i>Save Draft
                    </button>
                    <button type="submit" name="submit_form" value="1" class="btn btn-success" onclick="return confirm('Submit the form? You can still edit it later.')">
                        <i class="ti ti-send me-1"></i>Submit Form
                    </button>
                    @if($form->exists && $form->isSubmitted())
                    <a href="{{ route('mess.tenant-forms.download', [$mess->id, $form->id]) }}" class="btn btn-primary">
                        <i class="ti ti-download me-1"></i>Download PDF
                    </a>
                    @endif
                    <div class="ms-auto text-muted small">
                        @if($form->exists)
                            @if($form->isSubmitted())
                            <span class="badge bg-success"><i class="ti ti-circle-check me-1"></i>Submitted {{ $form->submitted_at->format('d M Y') }}</span>
                            @else
                            <span class="badge bg-warning text-dark"><i class="ti ti-pencil me-1"></i>Draft</span>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
let familyRowCount = {{ count($familyRows) }};

function addFamilyRow() {
    const tbody = document.getElementById('familyBody');
    const i = familyRowCount;
    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td class="text-center fw-bold">${i + 1}</td>
        <td><input type="text" name="family_members[${i}][name]" class="form-control form-control-sm"></td>
        <td><input type="text" name="family_members[${i}][age]" class="form-control form-control-sm"></td>
        <td><input type="text" name="family_members[${i}][profession]" class="form-control form-control-sm"></td>
        <td><input type="text" name="family_members[${i}][mobile]" class="form-control form-control-sm"></td>
        <td class="text-center"><button type="button" class="btn btn-xs btn-outline-danger" onclick="this.closest('tr').remove()"><i class="ti ti-x"></i></button></td>
    `;
    tbody.appendChild(tr);
    familyRowCount++;
}

function previewPhoto(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            const preview = document.getElementById('photoPreview');
            preview.innerHTML = `<img src="${e.target.result}" style="width:100%;height:100%;object-fit:cover">`;
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection
