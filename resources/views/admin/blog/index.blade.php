<?php $page = "admin-blog" ?>
@extends('layout.mainlayout')
@section('content')
<div class="page-wrapper">
  <div class="content">

    <div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-2">
      <div class="page-title">
        <h4 class="fw-bold"><i class="ti ti-article me-2 text-warning"></i>Blog Management</h4>
        <h6 class="text-muted">Manage all blog posts, approve or reject submissions</h6>
      </div>
      <a href="{{ route('admin.blog.create') }}" class="btn btn-warning fw-bold">
        <i class="ti ti-plus me-1"></i> New Post
      </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show py-2"><i class="ti ti-check me-2"></i>{{ session('success') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show py-2">{{ session('error') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    {{-- Status Filter --}}
    <div class="d-flex gap-2 flex-wrap mb-3">
      @foreach([''=>'All Posts','pending'=>'Pending','published'=>'Published','draft'=>'Draft','rejected'=>'Rejected'] as $val=>$lbl)
      <a href="{{ route('admin.blog.index', $val ? ['status'=>$val] : []) }}"
         class="badge {{ request('status')===$val ? 'bg-warning text-dark' : 'bg-light text-muted border' }}"
         style="font-size:.82rem;padding:6px 14px;border-radius:50px;text-decoration:none">{{ $lbl }}</a>
      @endforeach
    </div>

    <div class="card shadow-sm border-0">
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead style="background:#f9fafb">
              <tr>
                <th style="padding:14px 16px;font-size:.8rem;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">Post</th>
                <th style="padding:14px 16px;font-size:.8rem;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">Author</th>
                <th style="padding:14px 16px;font-size:.8rem;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">Category</th>
                <th style="padding:14px 16px;font-size:.8rem;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">Status</th>
                <th style="padding:14px 16px;font-size:.8rem;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">Date</th>
                <th style="padding:14px 16px;font-size:.8rem;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">Views</th>
                <th style="padding:14px 16px;font-size:.8rem;text-transform:uppercase;letter-spacing:.5px;color:#6b7280;text-align:right">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($blogs as $blog)
              <tr>
                <td style="padding:14px 16px;max-width:280px">
                  <div class="fw-semibold text-truncate" style="max-width:260px">{{ $blog->title }}</div>
                  @if($blog->featured)<span class="badge bg-success" style="font-size:.7rem">Featured</span>@endif
                </td>
                <td style="padding:14px 16px">
                  <div class="d-flex align-items-center gap-2">
                    <div style="width:28px;height:28px;border-radius:50%;background:#FE9F43;color:white;font-weight:700;font-size:.75rem;display:flex;align-items:center;justify-content:center;flex-shrink:0">{{ strtoupper(substr($blog->user->name,0,1)) }}</div>
                    <span style="font-size:.875rem">{{ $blog->user->name }}</span>
                  </div>
                </td>
                <td style="padding:14px 16px">
                  @if($blog->category)
                    <span class="badge bg-light text-dark border" style="font-size:.8rem">{{ $blog->category->name }}</span>
                  @else
                    <span class="text-muted" style="font-size:.8rem">—</span>
                  @endif
                </td>
                <td style="padding:14px 16px">
                  @php
                  $sc=['published'=>'success','pending'=>'warning','draft'=>'secondary','rejected'=>'danger'];
                  @endphp
                  <span class="badge bg-{{ $sc[$blog->status] ?? 'secondary' }}" style="font-size:.8rem">{{ ucfirst($blog->status) }}</span>
                </td>
                <td style="padding:14px 16px;font-size:.8rem;color:#6b7280">{{ $blog->created_at->format('M j, Y') }}</td>
                <td style="padding:14px 16px;font-size:.875rem">{{ number_format($blog->views) }}</td>
                <td style="padding:14px 16px;text-align:right">
                  <div class="d-flex gap-1 justify-content-end">
                    @if($blog->status === 'pending')
                    <form action="{{ route('admin.blog.approve', $blog) }}" method="POST" class="d-inline">
                      @csrf
                      <button type="submit" class="btn btn-sm btn-success" title="Approve"><i class="ti ti-check"></i></button>
                    </form>
                    <button class="btn btn-sm btn-danger" title="Reject" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $blog->id }}"><i class="ti ti-x"></i></button>
                    @endif
                    <a href="{{ route('admin.blog.edit', $blog) }}" class="btn btn-sm btn-outline-secondary"><i class="ti ti-edit"></i></a>
                    @if($blog->status === 'published')
                    <a href="{{ route('blog.show', $blog->slug) }}" class="btn btn-sm btn-outline-primary" target="_blank"><i class="ti ti-external-link"></i></a>
                    @endif
                    <button type="button" class="btn btn-sm btn-outline-danger"
                      data-bs-toggle="modal" data-bs-target="#deleteModal{{ $blog->id }}">
                      <i class="ti ti-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>

              {{-- Reject Modal --}}
              @if($blog->status === 'pending')
              <div class="modal fade" id="rejectModal{{ $blog->id }}" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title fw-bold">Reject Post</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('admin.blog.reject', $blog) }}" method="POST">
                      @csrf
                      <div class="modal-body">
                        <div class="fw-semibold mb-2 text-truncate">{{ $blog->title }}</div>
                        <label class="form-label fw-semibold">Rejection Reason *</label>
                        <textarea name="reason" class="form-control" rows="3" placeholder="Explain why this post is being rejected..." required></textarea>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger btn-sm"><i class="ti ti-x me-1"></i>Reject</button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
              @endif

              {{-- Delete Modal --}}
              <div class="modal fade" id="deleteModal{{ $blog->id }}" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered modal-sm">
                  <div class="modal-content">
                    <div class="modal-body text-center px-4 pt-4 pb-2">
                      <div style="width:56px;height:56px;border-radius:50%;background:#fee2e2;display:flex;align-items:center;justify-content:center;margin:0 auto 16px">
                        <i class="ti ti-trash text-danger" style="font-size:1.5rem"></i>
                      </div>
                      <h5 class="fw-bold mb-2">Delete Post?</h5>
                      <p class="text-muted mb-0" style="font-size:.875rem">
                        "<strong>{{ Str::limit($blog->title, 50) }}</strong>" will be permanently deleted. This cannot be undone.
                      </p>
                    </div>
                    <div class="modal-footer border-0 justify-content-center gap-2 pb-4">
                      <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="ti ti-x me-1"></i> Cancel
                      </button>
                      <form action="{{ route('admin.blog.destroy', $blog) }}" method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger fw-bold">
                          <i class="ti ti-trash me-1"></i> Yes, Delete
                        </button>
                      </form>
                    </div>
                  </div>
                </div>
              </div>

              @empty
              <tr>
                <td colspan="7" class="text-center py-5 text-muted">
                  <i class="ti ti-article-off" style="font-size:2.5rem;opacity:.3;display:block;margin-bottom:10px"></i>
                  No blog posts found.
                </td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
      @if($blogs->hasPages())
      <div class="card-footer bg-transparent d-flex justify-content-between align-items-center">
        <div class="text-muted" style="font-size:.875rem">Showing {{ $blogs->firstItem() }}–{{ $blogs->lastItem() }} of {{ $blogs->total() }}</div>
        {{ $blogs->links() }}
      </div>
      @endif
    </div>

    {{-- Quick links --}}
    <div class="d-flex gap-2 mt-3 flex-wrap">
      <a href="{{ route('admin.blog.categories') }}" class="btn btn-sm btn-outline-secondary"><i class="ti ti-folder me-1"></i>Manage Categories</a>
      <a href="{{ route('admin.blog.tags') }}" class="btn btn-sm btn-outline-secondary"><i class="ti ti-tag me-1"></i>Manage Tags</a>
    </div>

  </div>
</div>
@endsection
