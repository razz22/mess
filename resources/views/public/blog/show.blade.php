@extends('public.layout')
@section('title', $blog->title)
@section('meta-description', $blog->excerpt ?? Str::limit(strip_tags($blog->content), 160))

@section('extra-css')
<style>
.blog-content { line-height:1.9; color:#374151; font-size:1rem; }
.blog-content h1,.blog-content h2,.blog-content h3,.blog-content h4,.blog-content h5 { color:var(--navy); font-weight:700; margin:28px 0 12px; line-height:1.3; }
.blog-content h1 { font-size:1.8rem; } .blog-content h2 { font-size:1.5rem; } .blog-content h3 { font-size:1.25rem; }
.blog-content p { margin-bottom:16px; }
.blog-content ul,.blog-content ol { padding-left:26px; margin-bottom:16px; }
.blog-content li { margin-bottom:6px; }
.blog-content blockquote { border-left:4px solid var(--orange); padding:14px 20px; background:rgba(254,159,67,.06); margin:24px 0; border-radius:0 8px 8px 0; color:var(--muted); font-style:italic; }
.blog-content img { max-width:100%; height:auto; border-radius:10px; margin:12px 0; }
.blog-content img.float-start { margin:6px 20px 12px 0; }
.blog-content img.float-end  { margin:6px 0 12px 20px; }
.blog-content img.d-block.mx-auto { display:block; margin-left:auto; margin-right:auto; }
.blog-content table { width:100%; border-collapse:collapse; margin:20px 0; border-radius:8px; overflow:hidden; font-size:.9rem; }
.blog-content td,.blog-content th { border:1px solid #dee2e6; padding:10px 14px; vertical-align:top; }
.blog-content th { background:#f3f4f6; font-weight:700; color:var(--navy); }
.blog-content tr:nth-child(even) td { background:#fafafa; }
.blog-content pre { background:#1e1e2e; color:#cdd6f4; padding:16px 20px; border-radius:10px; overflow-x:auto; font-size:.875rem; margin:16px 0; }
.blog-content code { background:#f3f4f6; color:#e11d48; padding:2px 6px; border-radius:4px; font-size:.875em; }
.blog-content pre code { background:none; color:inherit; padding:0; }
.blog-content a { color:var(--orange); text-decoration:underline; }
.blog-content hr { border:none; border-top:2px solid #f3f4f6; margin:28px 0; }
.blog-content::after { content:''; display:table; clear:both; }
.like-btn { display:inline-flex; align-items:center; gap:8px; padding:10px 20px; border-radius:50px; border:2px solid var(--border); background:var(--white); color:var(--muted); font-weight:600; font-size:.9rem; cursor:pointer; transition:all .25s; }
.like-btn.liked { border-color:#ef4444; color:#ef4444; background:rgba(239,68,68,.05); }
.like-btn:hover { border-color:#ef4444; color:#ef4444; }
</style>
@endsection

@section('public-content')

{{-- Hero --}}
<div style="background:var(--navy);padding:100px 0 0;position:relative;overflow:hidden">
  <div style="position:absolute;inset:0;background:linear-gradient(135deg,var(--navy) 0%,var(--navy2) 100%)"></div>
  @if($blog->thumbnail)
  <div style="position:absolute;inset:0;background-image:url('{{ asset("storage/".$blog->thumbnail) }}');background-size:cover;background-position:center;opacity:.15"></div>
  @endif
  <div class="container" style="position:relative;z-index:2;padding-bottom:48px">
    <div class="pub-breadcrumb" data-aos="fade-right">
      <a href="{{ url('/') }}" style="color:rgba(255,255,255,.4)">{{ __('Home') }}</a>
      <i class="ti ti-chevron-right" style="color:rgba(255,255,255,.3)"></i>
      <a href="{{ route('blog.index') }}" style="color:rgba(255,255,255,.4)">{{ __('Blog') }}</a>
      <i class="ti ti-chevron-right" style="color:rgba(255,255,255,.3)"></i>
      <span style="color:rgba(255,255,255,.55)">{{ Str::limit($blog->title, 40) }}</span>
    </div>
    @if($blog->category)
    <a href="{{ route('blog.index', ['category' => $blog->category_id]) }}" style="display:inline-block;background:var(--orange);color:var(--white);font-size:.75rem;font-weight:700;padding:4px 14px;border-radius:50px;margin-bottom:16px;text-decoration:none">{{ $blog->category->name }}</a>
    @endif
    <h1 style="font-size:clamp(1.6rem,3.5vw,2.6rem);font-weight:800;color:var(--white);line-height:1.2;max-width:800px" data-aos="fade-up">{{ $blog->title }}</h1>
    @if($blog->excerpt)
    <p style="color:rgba(255,255,255,.6);font-size:1.05rem;margin-top:12px;max-width:680px" data-aos="fade-up" data-aos-delay="80">{{ $blog->excerpt }}</p>
    @endif
    <div class="d-flex align-items-center gap-4 mt-4 flex-wrap" style="color:rgba(255,255,255,.5);font-size:.85rem" data-aos="fade-up" data-aos-delay="120">
      <div class="d-flex align-items-center gap-2">
        <div style="width:36px;height:36px;border-radius:50%;background:var(--orange);color:var(--white);font-weight:700;font-size:.85rem;display:flex;align-items:center;justify-content:center">{{ strtoupper(substr($blog->user->name,0,1)) }}</div>
        <span>{{ $blog->user->name }}</span>
      </div>
      <span><i class="ti ti-calendar me-1"></i>{{ $blog->published_at?->format('M j, Y') }}</span>
      <span><i class="ti ti-eye me-1"></i>{{ number_format($blog->views) }} {{ __('views') }}</span>
      <span><i class="ti ti-clock me-1"></i>{{ $blog->read_time }} {{ __('min read') }}</span>
      <span><i class="ti ti-heart me-1"></i><span id="like-count">{{ $blog->likes->count() }}</span> {{ __('likes') }}</span>
    </div>
  </div>
</div>

<section style="padding:60px 0;background:var(--light)">
  <div class="container">
    <div class="row g-5">
      {{-- Main Content --}}
      <div class="col-lg-8">
        {{-- Thumbnail --}}
        @if($blog->thumbnail)
        <div style="border-radius:20px;overflow:hidden;margin-bottom:36px;box-shadow:0 8px 32px rgba(0,0,0,.12)" data-aos="fade-up">
          <img src="{{ asset("storage/".$blog->thumbnail) }}" alt="{{ $blog->title }}" style="width:100%;max-height:480px;object-fit:cover">
        </div>
        @endif

        {{-- Content --}}
        <div class="blog-content" style="background:var(--white);border-radius:20px;padding:36px;margin-bottom:28px;box-shadow:0 2px 12px rgba(0,0,0,.05)" data-aos="fade-up">
          {!! $blog->content !!}
        </div>

        {{-- Tags --}}
        @if($blog->tags->count())
        <div style="background:var(--white);border-radius:16px;padding:20px 24px;margin-bottom:24px;border:1px solid var(--border)" data-aos="fade-up">
          <div class="d-flex align-items-center gap-2 flex-wrap">
            <span style="font-size:.8rem;font-weight:700;color:var(--navy);text-transform:uppercase;letter-spacing:.5px;margin-right:4px"><i class="ti ti-tag me-1" style="color:var(--orange)"></i>{{ __('Tags:') }}</span>
            @foreach($blog->tags as $tag)
            <a href="{{ route('blog.index', ['tag' => $tag->slug]) }}" style="padding:4px 12px;background:var(--light);border:1px solid var(--border);border-radius:50px;font-size:.8rem;color:var(--navy);text-decoration:none">{{ $tag->name }}</a>
            @endforeach
          </div>
        </div>
        @endif

        {{-- Gallery --}}
        @if($blog->images->count())
        <div style="background:var(--white);border-radius:16px;padding:24px;margin-bottom:24px;border:1px solid var(--border)" data-aos="fade-up">
          <h6 style="font-weight:700;color:var(--navy);margin-bottom:16px">{{ __('Gallery') }}</h6>
          <div class="row g-2">
            @foreach($blog->images as $img)
            <div class="col-4 col-md-3">
              <a href="{{ asset("storage/".$img->image) }}" target="_blank">
                <div style="border-radius:10px;overflow:hidden;height:100px">
                  <img src="{{ asset("storage/".$img->image) }}" alt="{{ $img->caption }}" style="width:100%;height:100%;object-fit:cover;transition:transform .3s" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                </div>
              </a>
              @if($img->caption)
              <div style="font-size:.7rem;color:var(--muted);text-align:center;margin-top:4px">{{ $img->caption }}</div>
              @endif
            </div>
            @endforeach
          </div>
        </div>
        @endif

        {{-- Like & Share --}}
        <div style="background:var(--white);border-radius:16px;padding:24px;margin-bottom:24px;border:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;flex-wrap:gap 12px" data-aos="fade-up">
          <div>
            @auth
            <button id="likeBtn" class="like-btn {{ $isLiked ? 'liked' : '' }}" data-blog="{{ $blog->id }}">
              <i class="ti ti-heart{{ $isLiked ? '-filled' : '' }}" id="likeIcon"></i>
              <span id="likeText">{{ $isLiked ? __('Liked') : __('Like') }}</span>
              <span id="likeCount">{{ $blog->likes->count() }}</span>
            </button>
            @else
            <a href="{{ route('signin') }}" class="like-btn"><i class="ti ti-heart"></i> {{ __('Like') }} <span>{{ $blog->likes->count() }}</span></a>
            @endauth
          </div>
          <div>
            <span style="font-size:.85rem;color:var(--muted);margin-right:12px;font-weight:600">{{ __('Share:') }}</span>
            @foreach([
              ['icon'=>'ti-brand-facebook','color'=>'#1877f2','url'=>'https://facebook.com/sharer?u='.urlencode(request()->url())],
              ['icon'=>'ti-brand-twitter','color'=>'#1da1f2','url'=>'https://twitter.com/intent/tweet?url='.urlencode(request()->url()).'&text='.urlencode($blog->title)],
              ['icon'=>'ti-link','color'=>'var(--muted)','url'=>'#'],
            ] as $s)
            <a href="{{ $s['url'] }}" target="_blank" style="width:36px;height:36px;border-radius:8px;background:var(--light);border:1px solid var(--border);display:inline-flex;align-items:center;justify-content:center;color:{{ $s['color'] }};font-size:.9rem;margin-left:6px;transition:all .2s" onmouseover="this.style.background='{{ $s['color'] }}';this.style.color='white'" onmouseout="this.style.background='var(--light)';this.style.color='{{ $s['color'] }}'"><i class="ti {{ $s['icon'] }}"></i></a>
            @endforeach
          </div>
        </div>

        {{-- Author Card --}}
        <div style="background:var(--navy);border-radius:16px;padding:24px;margin-bottom:32px;display:flex;align-items:flex-start;gap:16px" data-aos="fade-up">
          <div style="width:60px;height:60px;border-radius:50%;background:var(--orange);color:var(--white);font-size:1.4rem;font-weight:800;display:flex;align-items:center;justify-content:center;flex-shrink:0">{{ strtoupper(substr($blog->user->name,0,1)) }}</div>
          <div>
            <div style="font-weight:700;color:var(--white);margin-bottom:4px">{{ $blog->user->name }}</div>
            <div style="font-size:.8rem;color:rgba(255,255,255,.4);margin-bottom:8px">{{ __('Author') }} &bull; {{ $blog->published_at?->format('M j, Y') }}</div>
            <div style="font-size:.875rem;color:rgba(255,255,255,.6)">{{ __('Member of the MessManager community sharing tips and experiences about shared living.') }}</div>
          </div>
        </div>

        {{-- Comments --}}
        @if($blog->allow_comments)
        <div data-aos="fade-up">
          <h4 style="font-weight:700;color:var(--navy);margin-bottom:20px"><i class="ti ti-messages me-2" style="color:var(--orange)"></i>{{ __('Comments') }} ({{ $blog->comments->count() }})</h4>

          @if(session('success'))
          <div class="alert alert-success rounded-3 mb-4">{{ session('success') }}</div>
          @endif
          @if(session('error'))
          <div class="alert alert-danger rounded-3 mb-4">{{ session('error') }}</div>
          @endif

          {{-- Comment Form --}}
          <div style="background:var(--white);border:1px solid var(--border);border-radius:16px;padding:24px;margin-bottom:28px">
            <h6 style="font-weight:700;color:var(--navy);margin-bottom:16px">{{ __('Leave a Comment') }}</h6>
            <form action="{{ route('blog.comment', $blog->id) }}" method="POST">
              @csrf
              <input type="hidden" name="parent_id" id="commentParentId" value="">
              <div id="replyNotice" style="display:none;background:rgba(254,159,67,.1);border:1px solid rgba(254,159,67,.2);border-radius:8px;padding:10px 14px;font-size:.85rem;color:var(--orange);margin-bottom:12px">
                {{ __('Replying to a comment.') }} <button type="button" onclick="cancelReply()" style="background:none;border:none;color:var(--orange);text-decoration:underline;cursor:pointer;padding:0">{{ __('Cancel') }}</button>
              </div>
              @if(!auth()->check())
              <div class="row g-3 mb-3">
                <div class="col-md-6">
                  <input type="text" name="guest_name" class="form-control @error('guest_name') is-invalid @enderror" placeholder="{{ __('Your Name') }} *" value="{{ old('guest_name') }}">
                  @error('guest_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                  <input type="email" name="guest_email" class="form-control @error('guest_email') is-invalid @enderror" placeholder="{{ __('Your Email') }} *" value="{{ old('guest_email') }}">
                  @error('guest_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
              </div>
              @endif
              <textarea name="content" rows="4" class="form-control @error('content') is-invalid @enderror mb-3" placeholder="{{ __('Write your comment...') }}">{{ old('content') }}</textarea>
              @error('content')<div class="invalid-feedback d-block mb-2">{{ $message }}</div>@enderror
              <button type="submit" class="btn-orange" style="font-size:.875rem;padding:10px 20px"><i class="ti ti-send"></i> {{ __('Post Comment') }}</button>
            </form>
          </div>

          {{-- Comments List --}}
          @forelse($blog->comments as $comment)
          <div style="background:var(--white);border:1px solid var(--border);border-radius:14px;padding:20px;margin-bottom:16px">
            <div class="d-flex align-items-start gap-3">
              <div style="width:38px;height:38px;border-radius:50%;background:var(--navy);color:var(--white);font-weight:700;font-size:.9rem;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                {{ strtoupper(substr($comment->user?->name ?? $comment->guest_name ?? '?', 0, 1)) }}
              </div>
              <div style="flex:1">
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <div>
                    <span style="font-weight:700;font-size:.9rem;color:var(--navy)">{{ $comment->user?->name ?? $comment->guest_name ?? 'Anonymous' }}</span>
                    <span style="font-size:.75rem;color:var(--muted);margin-left:8px"><i class="ti ti-clock me-1"></i>{{ $comment->created_at->diffForHumans() }}</span>
                  </div>
                  <button onclick="setReply({{ $comment->id }}, '{{ addslashes($comment->user?->name ?? $comment->guest_name ?? 'Anonymous') }}')" style="background:none;border:none;color:var(--muted);font-size:.8rem;cursor:pointer;display:flex;align-items:center;gap:4px" onmouseover="this.style.color='var(--orange)'" onmouseout="this.style.color='var(--muted)'">
                    <i class="ti ti-corner-down-right"></i> {{ __('Reply') }}
                  </button>
                </div>
                <p style="font-size:.9rem;color:#374151;line-height:1.7;margin:0">{{ $comment->content }}</p>
              </div>
            </div>

            {{-- Replies --}}
            @foreach($comment->replies as $reply)
            <div style="background:var(--light);border-radius:10px;padding:14px;margin-top:12px;margin-left:48px">
              <div class="d-flex align-items-start gap-2">
                <div style="width:30px;height:30px;border-radius:50%;background:var(--orange);color:var(--white);font-weight:700;font-size:.75rem;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                  {{ strtoupper(substr($reply->user?->name ?? $reply->guest_name ?? '?', 0, 1)) }}
                </div>
                <div>
                  <div style="font-weight:700;font-size:.85rem;color:var(--navy)">{{ $reply->user?->name ?? $reply->guest_name ?? 'Anonymous' }}</div>
                  <p style="font-size:.875rem;color:var(--muted);margin:4px 0 0;line-height:1.65">{{ $reply->content }}</p>
                  <div style="font-size:.7rem;color:var(--muted);margin-top:4px"><i class="ti ti-clock me-1"></i>{{ $reply->created_at->diffForHumans() }}</div>
                </div>
              </div>
            </div>
            @endforeach
          </div>
          @empty
          <div style="text-align:center;padding:32px;color:var(--muted)">
            <i class="ti ti-message-off" style="font-size:2rem;opacity:.4;display:block;margin-bottom:8px"></i>
            {{ __('No comments yet. Be the first to comment!') }}
          </div>
          @endforelse
        </div>
        @endif
      </div>

      {{-- Sidebar --}}
      <div class="col-lg-4">
        {{-- Related Posts --}}
        @if($related->count())
        <div style="background:var(--white);border:1px solid var(--border);border-radius:16px;padding:24px;margin-bottom:24px" data-aos="fade-left">
          <h6 style="font-weight:700;color:var(--navy);margin-bottom:16px"><i class="ti ti-sparkles me-2" style="color:var(--orange)"></i>{{ __('Related Posts') }}</h6>
          @foreach($related as $r)
          <div class="d-flex gap-3 mb-3 {{ !$loop->last ? 'pb-3' : '' }}" style="{{ !$loop->last ? 'border-bottom:1px solid var(--border)' : '' }}">
            <div style="width:56px;height:56px;border-radius:10px;overflow:hidden;flex-shrink:0;background:var(--light)">
              @if($r->thumbnail)
                <img src="{{ asset("storage/".$r->thumbnail) }}" style="width:100%;height:100%;object-fit:cover">
              @else
                <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center"><i class="ti ti-article" style="color:var(--muted)"></i></div>
              @endif
            </div>
            <div>
              <a href="{{ route('blog.show', $r->slug) }}" style="font-size:.85rem;font-weight:600;color:var(--navy);text-decoration:none;line-height:1.4;display:block" onmouseover="this.style.color='var(--orange)'" onmouseout="this.style.color='var(--navy)'">{{ Str::limit($r->title, 50) }}</a>
              <div style="font-size:.75rem;color:var(--muted);margin-top:3px"><i class="ti ti-clock me-1"></i>{{ $r->read_time }} {{ __('min read') }}</div>
            </div>
          </div>
          @endforeach
        </div>
        @endif

        {{-- CTA --}}
        @guest
        <div style="background:var(--navy);border-radius:16px;padding:24px;text-align:center" data-aos="fade-left" data-aos-delay="80">
          <i class="ti ti-building-community" style="font-size:2.5rem;color:var(--orange);display:block;margin-bottom:12px"></i>
          <div style="font-weight:700;color:var(--white);margin-bottom:8px">{{ __('Manage Your Mess Better') }}</div>
          <p style="color:rgba(255,255,255,.6);font-size:.875rem;margin-bottom:16px">{{ __('Join 500+ messes already using MessManager.') }}</p>
          <a href="{{ route('register') }}" class="btn-orange" style="font-size:.875rem;padding:10px 18px;display:block;text-align:center"><i class="ti ti-rocket"></i> {{ __('Start Free') }}</a>
        </div>
        @endguest
      </div>
    </div>
  </div>
</section>

@endsection

@section('extra-js')
<script>
@auth
const likeBtn  = document.getElementById('likeBtn');
const likeIcon = document.getElementById('likeIcon');
const likeText = document.getElementById('likeText');
const likeCount = document.getElementById('likeCount');

likeBtn.addEventListener('click', async function() {
  try {
    const res = await fetch('/blog/{{ $blog->id }}/like', {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
    });
    const data = await res.json();
    likeBtn.classList.toggle('liked', data.liked);
    likeIcon.className = data.liked ? 'ti ti-heart-filled' : 'ti ti-heart';
    likeText.textContent = data.liked ? '{{ __('Liked') }}' : '{{ __('Like') }}';
    likeCount.textContent = data.count;
    // also update header count
    const hc = document.getElementById('like-count');
    if(hc) hc.textContent = data.count;
  } catch(e) { console.error(e); }
});
@endauth

function setReply(id, name) {
  document.getElementById('commentParentId').value = id;
  document.getElementById('replyNotice').style.display = 'block';
  document.getElementById('replyNotice').innerHTML = `{{ __('Replying to') }} <strong>${name}</strong>. <button type="button" onclick="cancelReply()" style="background:none;border:none;color:var(--orange);text-decoration:underline;cursor:pointer;padding:0">{{ __('Cancel') }}</button>`;
  document.querySelector('textarea[name=content]').focus();
  window.scrollTo({ top: document.querySelector('form').offsetTop - 100, behavior: 'smooth' });
}
function cancelReply() {
  document.getElementById('commentParentId').value = '';
  document.getElementById('replyNotice').style.display = 'none';
}
</script>
@endsection
