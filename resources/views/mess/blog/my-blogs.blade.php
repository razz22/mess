<?php $page = "member-blog" ?>
@extends('layout.mainlayout')
@section('content')
<div class="page-wrapper">
  <div class="content">

    <div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-2">
      <div class="page-title">
        <h4 class="fw-bold"><i class="ti ti-article me-2 text-warning"></i>My Blog Posts</h4>
        <h6 class="text-muted">Manage your submitted articles</h6>
      </div>
      <a href="{{ route('member.blog.create') }}" class="btn btn-warning fw-bold">
        <i class="ti ti-plus me-1"></i> Write New Article
      </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show py-2">{{ session('success') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    @if($blogs->isEmpty())
    <div class="card shadow-sm border-0">
      <div class="card-body text-center py-5">
        <i class="ti ti-article-off" style="font-size:3rem;color:#d1d5db;display:block;margin-bottom:16px"></i>
        <h5 class="text-muted">No Posts Yet</h5>
        <p class="text-muted mb-4">You haven't written any blog posts yet. Share your mess management experiences!</p>
        <a href="{{ route('member.blog.create') }}" class="btn btn-warning fw-bold">
          <i class="ti ti-pencil me-1"></i> Write Your First Article
        </a>
      </div>
    </div>
    @else
    <div class="card shadow-sm border-0">
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead style="background:#f9fafb">
              <tr>
                <th style="padding:14px 16px;font-size:.8rem;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">Post</th>
                <th style="padding:14px 16px;font-size:.8rem;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">Status</th>
                <th style="padding:14px 16px;font-size:.8rem;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">Category</th>
                <th style="padding:14px 16px;font-size:.8rem;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">Submitted</th>
                <th style="padding:14px 16px;font-size:.8rem;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">Views</th>
                <th style="padding:14px 16px;font-size:.8rem;text-transform:uppercase;letter-spacing:.5px;color:#6b7280;text-align:right">Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($blogs as $blog)
              <tr>
                <td style="padding:14px 16px">
                  <div class="fw-semibold" style="max-width:280px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $blog->title }}</div>
                  @if($blog->status === 'rejected' && $blog->rejection_reason)
                  <div style="background:#fee2e2;border:1px solid #fca5a5;border-radius:8px;padding:8px 12px;margin-top:8px;font-size:.8rem;color:#991b1b;max-width:300px">
                    <div class="fw-bold mb-1"><i class="ti ti-alert-circle me-1"></i>Rejection Reason:</div>
                    {{ $blog->rejection_reason }}
                  </div>
                  @endif
                </td>
                <td style="padding:14px 16px">
                  @php $sc = ['published'=>['success','Published'], 'pending'=>['warning','Pending Review'], 'draft'=>['secondary','Draft'], 'rejected'=>['danger','Rejected']]; $s = $sc[$blog->status] ?? ['secondary', ucfirst($blog->status)]; @endphp
                  <span class="badge bg-{{ $s[0] }}" style="font-size:.8rem">{{ $s[1] }}</span>
                </td>
                <td style="padding:14px 16px">
                  @if($blog->category)
                    <span class="badge bg-light text-dark border" style="font-size:.8rem">{{ $blog->category->name }}</span>
                  @else
                    <span class="text-muted" style="font-size:.8rem">—</span>
                  @endif
                </td>
                <td style="padding:14px 16px;font-size:.8rem;color:#6b7280">{{ $blog->created_at->format('M j, Y') }}</td>
                <td style="padding:14px 16px">
                  @if($blog->status === 'published')
                    <span class="fw-semibold">{{ number_format($blog->views) }}</span>
                  @else
                    <span class="text-muted">—</span>
                  @endif
                </td>
                <td style="padding:14px 16px;text-align:right">
                  <div class="d-flex gap-1 justify-content-end">
                    @if($blog->status === 'published')
                    <a href="{{ route('blog.show', $blog->slug) }}" class="btn btn-sm btn-outline-primary" target="_blank" title="View">
                      <i class="ti ti-external-link"></i>
                    </a>
                    @endif
                    @if(in_array($blog->status, ['pending', 'rejected', 'draft']))
                    <a href="{{ route('member.blog.edit', $blog) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                      <i class="ti ti-edit"></i>
                    </a>
                    @endif
                  </div>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
      @if($blogs->hasPages())
      <div class="card-footer bg-transparent">
        {{ $blogs->links() }}
      </div>
      @endif
    </div>

    {{-- Stats Summary --}}
    <div class="row g-3 mt-1">
      @php
      $total = $blogs->total();
      $published = auth()->user()->blogs()->where('status','published')->count();
      $pending = auth()->user()->blogs()->where('status','pending')->count();
      $rejected = auth()->user()->blogs()->where('status','rejected')->count();
      @endphp
      @foreach([
        ['label'=>'Total Posts','val'=>$total,'color'=>'#141432','icon'=>'ti-article'],
        ['label'=>'Published','val'=>$published,'color'=>'#22c55e','icon'=>'ti-check-circle'],
        ['label'=>'Pending Review','val'=>$pending,'color'=>'#f59e0b','icon'=>'ti-clock'],
        ['label'=>'Rejected','val'=>$rejected,'color'=>'#ef4444','icon'=>'ti-x-circle'],
      ] as $stat)
      <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body d-flex align-items-center gap-3">
            <div style="width:40px;height:40px;border-radius:10px;background:{{ $stat['color'] }}15;display:flex;align-items:center;justify-content:center;flex-shrink:0">
              <i class="ti {{ $stat['icon'] }}" style="color:{{ $stat['color'] }};font-size:1.2rem"></i>
            </div>
            <div>
              <div class="fw-bold fs-5">{{ $stat['val'] }}</div>
              <div class="text-muted" style="font-size:.8rem">{{ $stat['label'] }}</div>
            </div>
          </div>
        </div>
      </div>
      @endforeach
    </div>
    @endif

  </div>
</div>
@endsection
