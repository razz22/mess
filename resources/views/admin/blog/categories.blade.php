<?php $page = "admin-blog" ?>
@extends('layout.mainlayout')
@section('content')
<div class="page-wrapper">
  <div class="content">

    <div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-2">
      <div class="page-title">
        <h4 class="fw-bold"><i class="ti ti-folder me-2 text-warning"></i>Blog Categories</h4>
        <h6 class="text-muted">Manage categories and subcategories</h6>
      </div>
      <a href="{{ route('admin.blog.index') }}" class="btn btn-sm btn-outline-secondary"><i class="ti ti-arrow-left me-1"></i>Back to Blog</a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show py-2">{{ session('success') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    <div class="row g-4">
      {{-- Create Form --}}
      <div class="col-lg-4">
        <div class="card shadow-sm border-0">
          <div class="card-header bg-white fw-bold border-bottom" style="padding:16px 20px">New Category</div>
          <div class="card-body" style="padding:20px">
            <form action="{{ route('admin.blog.categories.store') }}" method="POST">
              @csrf
              <div class="mb-3">
                <label class="form-label fw-semibold">Name *</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Category name" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold">Parent Category</label>
                <select name="parent_id" class="form-select">
                  <option value="">— Root Category —</option>
                  @foreach($parents as $p)
                  <option value="{{ $p->id }}" {{ old('parent_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold">Description</label>
                <textarea name="description" class="form-control" rows="2" placeholder="Optional description">{{ old('description') }}</textarea>
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold">Sort Order</label>
                <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', 0) }}">
              </div>
              <button type="submit" class="btn btn-warning fw-bold w-100"><i class="ti ti-plus me-1"></i>Create Category</button>
            </form>
          </div>
        </div>
      </div>

      {{-- List --}}
      <div class="col-lg-8">
        <div class="card shadow-sm border-0">
          <div class="card-header bg-white fw-bold border-bottom" style="padding:16px 20px">All Categories ({{ $categories->count() }})</div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table align-middle mb-0">
                <thead style="background:#f9fafb">
                  <tr>
                    <th style="padding:12px 16px;font-size:.8rem;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">Category</th>
                    <th style="padding:12px 16px;font-size:.8rem;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">Parent</th>
                    <th style="padding:12px 16px;font-size:.8rem;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">Posts</th>
                    <th style="padding:12px 16px;font-size:.8rem;text-transform:uppercase;letter-spacing:.5px;color:#6b7280;text-align:right">Action</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($categories as $cat)
                  <tr>
                    <td style="padding:12px 16px">
                      <div class="fw-semibold">{{ $cat->parent ? '↳ ' : '' }}{{ $cat->name }}</div>
                      @if($cat->description)<div class="text-muted" style="font-size:.8rem">{{ Str::limit($cat->description, 60) }}</div>@endif
                    </td>
                    <td style="padding:12px 16px">
                      @if($cat->parent)
                        <span class="badge bg-light text-dark border" style="font-size:.8rem">{{ $cat->parent->name }}</span>
                      @else
                        <span class="text-muted" style="font-size:.8rem">Root</span>
                      @endif
                    </td>
                    <td style="padding:12px 16px">
                      <span class="badge bg-warning text-dark">{{ $cat->blogs()->count() }}</span>
                    </td>
                    <td style="padding:12px 16px;text-align:right">
                      <form action="{{ route('admin.blog.categories.destroy', $cat) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete category? Posts will become uncategorized.')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger"><i class="ti ti-trash"></i></button>
                      </form>
                    </td>
                  </tr>
                  @empty
                  <tr>
                    <td colspan="4" class="text-center py-4 text-muted">No categories yet.</td>
                  </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>
@endsection
