<?php $page = "member-blog" ?>
@extends('layout.mainlayout')
@section('content')
<div class="page-wrapper">
  <div class="content">

    <div class="page-header">
      <div class="page-title">
        <h4 class="fw-bold"><i class="ti ti-pencil me-2 text-warning"></i>{{ isset($blog) ? 'Edit Your Post' : 'Write New Article' }}</h4>
        <h6 class="text-muted">{{ isset($blog) ? 'Update and resubmit for review' : 'Share your mess management tips with the community' }}</h6>
      </div>
    </div>

    <div class="alert alert-info alert-dismissible fade show d-flex align-items-start gap-2 mb-4" style="border-radius:12px">
      <i class="ti ti-info-circle fs-5 mt-0"></i>
      <div>
        <strong>Submission Notice:</strong> Your post will be reviewed by our admin team before being published. This usually takes 24–48 hours.
      </div>
      <button class="btn-close" data-bs-dismiss="alert"></button>
    </div>

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show mb-3">
      <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
      <button class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <form action="{{ isset($blog) ? route('member.blog.update', $blog) : route('member.blog.store') }}" method="POST" enctype="multipart/form-data">
      @csrf
      @if(isset($blog)) @method('PUT') @endif

      <div class="row g-4">
        <div class="col-lg-8">
          <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white fw-bold border-bottom" style="padding:16px 20px">Post Content</div>
            <div class="card-body" style="padding:20px">
              <div class="mb-3">
                <label class="form-label fw-semibold">Title *</label>
                <input type="text" name="title" class="form-control form-control-lg @error('title') is-invalid @enderror" value="{{ old('title', $blog->title ?? '') }}" placeholder="Enter a clear, descriptive title">
                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold">Excerpt <span class="text-muted fw-normal">(short summary shown in listing)</span></label>
                <textarea name="excerpt" class="form-control @error('excerpt') is-invalid @enderror" rows="2" placeholder="A brief summary of your post (max 500 characters)...">{{ old('excerpt', $blog->excerpt ?? '') }}</textarea>
                @error('excerpt')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold">Content * <span class="text-muted fw-normal">(min 50 characters)</span></label>
                <textarea name="content" id="blog-content" class="@error('content') is-invalid @enderror">{{ old('content', $blog->content ?? '') }}</textarea>
                @error('content')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
              </div>
            </div>
          </div>

          <div class="card shadow-sm border-0">
            <div class="card-header bg-white fw-bold border-bottom" style="padding:16px 20px">Additional Images</div>
            <div class="card-body" style="padding:20px">
              <input type="file" name="images[]" class="form-control" multiple accept="image/*">
              <div class="form-text mt-1">Optional. Upload multiple images to illustrate your post. Max 2MB each.</div>
              @if(isset($blog) && $blog->images->count())
              <div class="row g-2 mt-3">
                @foreach($blog->images as $img)
                <div class="col-3 col-md-2">
                  <img src="{{ asset('storage/'.$img->image) }}" style="width:100%;height:60px;object-fit:cover;border-radius:8px;border:1px solid #e5e7eb">
                </div>
                @endforeach
              </div>
              @endif
            </div>
          </div>
        </div>

        <div class="col-lg-4">
          {{-- Submit --}}
          <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white fw-bold border-bottom" style="padding:16px 20px">Submit</div>
            <div class="card-body" style="padding:20px">
              @if(isset($blog) && $blog->status === 'rejected')
              <div class="alert alert-danger py-2 mb-3" style="font-size:.875rem;border-radius:10px">
                <div class="fw-bold mb-1"><i class="ti ti-alert-circle me-1"></i>Rejection Reason:</div>
                {{ $blog->rejection_reason }}
              </div>
              @endif
              <div class="d-grid gap-2">
                <button type="submit" class="btn btn-warning fw-bold">
                  <i class="ti ti-send me-1"></i>{{ isset($blog) ? 'Resubmit for Review' : 'Submit for Review' }}
                </button>
                <a href="{{ route('member.blog.index') }}" class="btn btn-outline-secondary">Cancel</a>
              </div>
              <div class="mt-3 p-2 rounded" style="background:#fefce8;border:1px solid #fef08a;font-size:.8rem;color:#92400e">
                <i class="ti ti-clock me-1"></i> Review usually takes 24–48 hours.
              </div>
            </div>
          </div>

          {{-- Thumbnail --}}
          <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white fw-bold border-bottom" style="padding:16px 20px">Cover Image</div>
            <div class="card-body" style="padding:20px">
              @if(isset($blog) && $blog->thumbnail)
              <img src="{{ asset('storage/'.$blog->thumbnail) }}" class="img-fluid rounded mb-2" style="max-height:140px;object-fit:cover;width:100%">
              @endif
              <input type="file" name="thumbnail" class="form-control" accept="image/*">
              <div class="form-text">Recommended: 1200×630px, max 2MB.</div>
            </div>
          </div>

          {{-- Category & Tags --}}
          <div class="card shadow-sm border-0">
            <div class="card-header bg-white fw-bold border-bottom" style="padding:16px 20px">Category & Tags</div>
            <div class="card-body" style="padding:20px">
              <div class="mb-3">
                <label class="form-label fw-semibold">Category</label>
                <select name="category_id" class="form-select">
                  <option value="">— No Category —</option>
                  @foreach($categories as $cat)
                  <option value="{{ $cat->id }}" {{ old('category_id', $blog->category_id ?? '') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                  @endforeach
                </select>
              </div>
              <div>
                <label class="form-label fw-semibold">Tags</label>
                <input type="text" name="tags" class="form-control" value="{{ old('tags', $blogTags ?? '') }}" placeholder="tag1, tag2, tag3">
                <div class="form-text">Comma-separated. Helps readers find your post.</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </form>

  </div>
</div>
@push('scripts')
<script src="https://cdn.tiny.cloud/1/jykn8vph8lu4spie12bsc6u34jyhvezfyne3q2u7u64d1yzk/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
<script>
tinymce.init({
  selector: '#blog-content',
  height: 520,
  menubar: 'file edit view insert format tools table',
  plugins: [
    'advlist','autolink','lists','link','image','charmap','preview','anchor','searchreplace',
    'visualblocks','code','fullscreen','insertdatetime','media','table','help','wordcount',
    'emoticons','codesample'
  ],
  toolbar:
    'undo redo | styles | fontfamily fontsize | bold italic underline strikethrough | ' +
    'forecolor backcolor | alignleft aligncenter alignright alignjustify | ' +
    'bullist numlist outdent indent | link image media table | ' +
    'removeformat code fullscreen',
  toolbar_mode: 'wrap',
  font_size_formats: '10px 11px 12px 13px 14px 16px 18px 20px 22px 24px 28px 32px 36px 48px 60px',
  content_style: `
    body { font-family: 'Segoe UI', system-ui, sans-serif; font-size: 15px; line-height: 1.7; color: #212B36; max-width: 100%; padding: 12px 16px; }
    img { max-width: 100%; height: auto; border-radius: 8px; }
    table { border-collapse: collapse; width: 100%; margin: 12px 0; }
    td, th { border: 1px solid #dee2e6; padding: 8px 12px; }
    th { background: #f3f4f6; font-weight: 700; }
    pre { background: #1e1e2e; color: #cdd6f4; padding: 14px 18px; border-radius: 8px; overflow-x: auto; }
  `,
  image_advtab: true,
  image_uploadtab: true,
  images_upload_url: '{{ route("member.blog.upload-image") }}',
  images_upload_handler: uploadImage,
  automatic_uploads: true,
  file_picker_types: 'image',
  image_class_list: [
    { title: 'Responsive', value: 'img-fluid' },
    { title: 'Rounded', value: 'img-fluid rounded' },
    { title: 'Float Left', value: 'img-fluid float-start me-3 mb-2' },
    { title: 'Float Right', value: 'img-fluid float-end ms-3 mb-2' },
    { title: 'Center Block', value: 'img-fluid d-block mx-auto' },
  ],
  table_default_styles: { 'width': '100%', 'border-collapse': 'collapse' },
  setup: function(editor) {
    editor.on('change', function() { editor.save(); });
  }
});

function uploadImage(blobInfo, progress) {
  return new Promise((resolve, reject) => {
    const xhr = new XMLHttpRequest();
    xhr.open('POST', '{{ route("member.blog.upload-image") }}');
    xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
    xhr.upload.onprogress = e => { if (e.lengthComputable) progress(e.loaded / e.total * 100); };
    xhr.onload = () => {
      if (xhr.status !== 200) { reject({ message: 'Upload failed: ' + xhr.status, remove: true }); return; }
      const json = JSON.parse(xhr.responseText);
      if (!json || !json.location) { reject({ message: 'Invalid server response', remove: true }); return; }
      resolve(json.location);
    };
    xhr.onerror = () => reject({ message: 'Network error', remove: true });
    const fd = new FormData();
    fd.append('file', blobInfo.blob(), blobInfo.filename());
    xhr.send(fd);
  });
}
</script>
@endpush

@endsection
