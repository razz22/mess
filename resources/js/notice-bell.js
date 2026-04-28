/**
 * Notice Bell — polls every 5 s, shows toast on new notices.
 */
(function () {
    'use strict';

    var body      = document.body;
    var messId    = body.dataset.messId;
    var latestUrl = body.dataset.noticesLatestUrl;
    var markAllUrl= body.dataset.noticesMarkallUrl;
    var csrf      = (document.querySelector('meta[name="csrf-token"]') || {}).content || '';

    console.log('[NoticeBell] init. messId=', messId, 'url=', latestUrl);

    if (!messId || !latestUrl) {
        console.warn('[NoticeBell] missing data attributes on body — aborting');
        return;
    }

    var knownIds  = null; // null = first load

    var $badge   = document.getElementById('notice-bell-badge');
    var $list    = document.getElementById('notice-bell-list');
    var $sb      = document.getElementById('sidebar-notice-badge');
    var $markAll = document.getElementById('notice-mark-all-btn');

    // ── XHR helpers ──────────────────────────────────────────────────────────
    function getJSON(url, cb) {
        var xhr = new XMLHttpRequest();
        xhr.open('GET', url);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.setRequestHeader('Accept', 'application/json');
        xhr.onload = function () {
            console.log('[NoticeBell] GET', url, '→', xhr.status, xhr.responseText.substring(0, 80));
            if (xhr.status === 200) {
                try { cb(JSON.parse(xhr.responseText)); }
                catch (e) { console.error('[NoticeBell] JSON parse error', e); }
            } else {
                console.warn('[NoticeBell] non-200 response:', xhr.status);
            }
        };
        xhr.onerror = function () { console.error('[NoticeBell] XHR network error'); };
        xhr.send();
    }

    function postJSON(url) {
        var xhr = new XMLHttpRequest();
        xhr.open('POST', url);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.setRequestHeader('X-CSRF-TOKEN', csrf);
        xhr.setRequestHeader('Content-Type', 'application/json');
        xhr.setRequestHeader('Accept', 'application/json');
        xhr.onload = function () { if (xhr.status === 200) fetchAndRender(); };
        xhr.send('{}');
    }

    // ── Fetch + render ────────────────────────────────────────────────────────
    function fetchAndRender() {
        getJSON(latestUrl, function (data) {
            var notices = data.notices || [];
            var unread  = data.unread  || 0;

            // Build set of current IDs
            var currentIds = notices.map(function (n) { return n.id; }).join(',');

            if (knownIds !== null && currentIds !== knownIds) {
                // Find newly appeared unread notices
                var knownSet = knownIds.split(',');
                var newOnes  = notices.filter(function (n) {
                    return !n.is_read && knownSet.indexOf(String(n.id)) === -1;
                });
                console.log('[NoticeBell] new notices detected:', newOnes.length);
                if (newOnes.length > 0) {
                    showToast(newOnes[0].title);
                }
            }

            knownIds = currentIds;
            renderBadge(unread);
            renderList(notices);
        });
    }

    function renderBadge(unread) {
        if ($badge) {
            $badge.textContent   = unread > 9 ? '9+' : unread;
            $badge.style.display = unread > 0 ? '' : 'none';
        }
        if ($sb) {
            $sb.textContent = unread > 9 ? '9+' : unread;
            unread > 0 ? $sb.classList.remove('d-none') : $sb.classList.add('d-none');
        }
    }

    function renderList(notices) {
        if (!$list) return;
        if (!notices.length) {
            $list.innerHTML = '<li class="text-center text-muted p-3 small">No notices yet.</li>';
            return;
        }
        $list.innerHTML = notices.map(function (n) {
            return '<li class="notification-message' + (n.is_read ? '' : ' fw-semibold') + '">' +
                '<a href="' + n.url + '">' +
                '<div class="media d-flex align-items-start gap-2 p-2">' +
                '<span class="flex-shrink-0 rounded bg-primary-subtle d-flex align-items-center justify-content-center" style="width:34px;height:34px;">' +
                '<i class="ti ti-speakerphone text-primary"></i></span>' +
                '<div class="flex-grow-1 min-w-0">' +
                '<p class="noti-details mb-0">' +
                (!n.is_read ? '<span class="badge bg-danger me-1" style="font-size:9px;">NEW</span>' : '') +
                n.title + '</p>' +
                '<p class="small text-muted mb-0 text-truncate">' + n.body_preview + '</p>' +
                '<p class="noti-time mb-0">' + n.published_at + ' &middot; ' + n.author + '</p>' +
                '</div></div></a></li>';
        }).join('');
    }

    // ── Toast — works with or without toastr ─────────────────────────────────
    function showToast(title) {
        console.log('[NoticeBell] showToast:', title);
        if (typeof toastr !== 'undefined') {
            toastr.info(title, 'New Notice', { timeOut: 7000, closeButton: true, progressBar: true });
            return;
        }
        // Fallback: inject a simple fixed banner
        var div = document.createElement('div');
        div.style.cssText = 'position:fixed;top:20px;right:20px;z-index:99999;' +
            'background:#206bc4;color:#fff;padding:14px 20px;border-radius:8px;' +
            'box-shadow:0 4px 20px rgba(0,0,0,.3);max-width:320px;font-size:14px;cursor:pointer;';
        div.innerHTML = '<strong>&#128226; New Notice</strong><br>' + title;
        div.onclick = function () { document.body.removeChild(div); };
        document.body.appendChild(div);
        setTimeout(function () {
            if (document.body.contains(div)) document.body.removeChild(div);
        }, 7000);
    }

    if ($markAll) {
        $markAll.addEventListener('click', function () { postJSON(markAllUrl); });
    }

    // ── Boot ─────────────────────────────────────────────────────────────────
    fetchAndRender();
    setInterval(fetchAndRender, 5000);

})();
