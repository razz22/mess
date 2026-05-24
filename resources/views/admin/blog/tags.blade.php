<?php $page = "admin-blog" ?>
@extends('layout.mainlayout')
@section('content')
<div class="page-wrapper">
  <div class="content">

    <div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-2">
      <div class="page-title">
        <h4 class="fw-bold"><i class="ti ti-tag me-2 text-warning"></i>Blog Tags</h4>
        <h6 class="text-muted">Manage blog post tags</h6>
      </div>
      <a href="{{ route('admin.blog.index') }}" class="btn btn-sm btn-outline-secondary"><i class="ti ti-arrow-left me-1"></i>Back to Blog</a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show py-2">{{ session('success') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show py-2">{{ session('error') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    <div class="row g-4">
      {{-- Create --}}
      <div class="col-lg-4">
        <div class="card shadow-sm border-0">
          <div class="card-header bg-white fw-bold border-bottom" style="padding:16px 20px">New Tag</div>
          <div class="card-body" style="padding:20px">
            <form action="{{ route('admin.blog.tags.store') }}" method="POST">
              @csrf
              <div class="mb-3">
                <label class="form-label fw-semibold">Tag Name *</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="e.g. meal-planning" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <button type="submit" class="btn btn-warning fw-bold w-100"><i class="ti ti-plus me-1"></i>Create Tag</button>
            </form>
          </div>
        </div>
      </div>

      {{-- List --}}
      <div class="col-lg-8">
        <div class="card shadow-sm border-0">
          <div class="card-header bg-white fw-bold border-bottom" style="padding:16px 20px">All Tags ({{ $tags->count() }})</div>
          <div class="card-body">
            @if($tags->isEmpty())
            <div class="text-center py-4 text-muted">No tags yet. Create one above.</div>
            @else
            <div class="d-flex gap-2 flex-wrap">
              @foreach($tags as $tag)
              <div class="d-flex align-items-center gap-2" style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:50px;padding:6px 10px 6px 14px">
                <span style="font-size:.875rem;font-weight:600;color:#111827">{{ $tag->name }}</span>
                <span class="badge bg-warning text-dark" style="font-size:.7rem">{{ $tag->blogs_count }}</span>
                <form action="{{ route('admin.blog.tags.destroy', $tag) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete tag?')">
                  @csrf @method('DELETE')
                  <button type="submit" style="background:none;border:none;width:20px;height:20px;border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;color:#9ca3af;font-size:.7rem;padding:0" onmouseover="this.style.background='#fee2e2';this.style.color='#dc2626'" onmouseout="this.style.background='none';this.style.color='#9ca3af'">
                    <i class="ti ti-x"></i>
                  </button>
                </form>
              </div>
              @endforeach
            </div>
            @endif
          </div>
        </div>
      </div>
    </div>

  </div>
</div>
@endsection
