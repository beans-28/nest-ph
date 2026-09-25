@php
    $isAnnounceAdmin = auth()->user()->role?->role_name === 'admin';
@endphp

<div class="announce-outer" id="announceCard" data-is-admin="{{ $isAnnounceAdmin ? '1' : '0' }}">
  <div class="announce-header">
    <span class="announce-header-icon">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 11l18-5v12L3 13v-2z"/><path d="M11.6 16.3L13 21h-3l-1-4"/></svg>
    </span>
    <h2>Announcements</h2>
  </div>

  @if($isAnnounceAdmin)
    <div class="announce-composer">
      <textarea id="announceComposerInput" placeholder="Post an announcement to all tenants..." maxlength="5000"></textarea>
      <button class="announce-btn" id="announcePostBtn">Post</button>
    </div>
  @endif

  <div class="announce-feed" id="announceFeed"></div>
  <div class="announce-empty" id="announceEmptyNote" style="display:none;">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M3 11l18-5v12L3 13v-2z"/><path d="M11.6 16.3L13 21h-3l-1-4"/></svg>
    <span>No announcements yet.</span>
  </div>
</div>

<style>
  .announce-outer{
    background:var(--card-bg, #fff);
    border:1px solid var(--border, #e2e6e2);
    border-radius:14px;
    padding:22px 24px;
    margin-bottom:20px;
    box-shadow:0 1px 2px rgba(20,30,20,0.03);
  }
  .announce-header{ display:flex; align-items:center; gap:10px; margin-bottom:18px; }
  .announce-header-icon{ width:30px; height:30px; border-radius:50%; background:var(--status-vacant-bg, #d9f2dd); color:var(--green-accent, #2f6f3c); display:flex; align-items:center; justify-content:center; flex-shrink:0; }
  .announce-header-icon svg{ width:15px; height:15px; }
  .announce-header h2{ font-size:15px; font-weight:700; margin:0; color:var(--text-dark, #243026); }

  .announce-composer{ display:flex; gap:10px; align-items:flex-start; background:#f6faf6; border:1px solid #e0ece1; border-radius:10px; padding:12px; margin-bottom:18px; }
  .announce-composer textarea{ flex:1; min-height:50px; resize:vertical; border:1px solid var(--border, #e2e6e2); border-radius:8px; padding:10px 12px; font-size:13px; font-family:inherit; color:var(--text-dark, #243026); background:#fff; }
  .announce-composer textarea:focus{ outline:none; border-color:var(--green-accent, #2f6f3c); }
  .announce-btn{ background:var(--green-btn, #2f6b3a); color:#fff; border:none; border-radius:8px; padding:10px 18px; font-size:12.5px; font-weight:600; cursor:pointer; white-space:nowrap; align-self:flex-start; }
  .announce-btn:hover{ background:var(--green-btn-hover, #255a2f); }
  .announce-btn:disabled{ opacity:0.5; cursor:not-allowed; }
  .announce-btn.secondary{ background:transparent; color:var(--text-mid, #5b6b60); border:1px solid var(--border, #e2e6e2); }
  .announce-btn.secondary:hover{ background:#f3f6f3; }
  .announce-btn.danger-text{ background:transparent; color:var(--status-occupied, #d9564f); border:none; padding:4px 8px; font-size:11.5px; }

  .announce-empty{ display:flex; flex-direction:column; align-items:center; gap:8px; padding:28px 0; color:var(--text-light, #8a9690); font-size:12.5px; }
  .announce-empty svg{ width:26px; height:26px; opacity:0.5; }

  .announce-post{ border:1px solid var(--border, #e2e6e2); border-radius:12px; padding:16px 18px; margin-bottom:12px; transition:box-shadow .15s; }
  .announce-post:hover{ box-shadow:0 2px 6px rgba(20,30,20,0.05); }
  .announce-post:last-child{ margin-bottom:0; }
  .announce-post-head{ display:flex; align-items:center; gap:10px; margin-bottom:12px; }
  .announce-avatar{ width:36px; height:36px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:12.5px; color:var(--green-dark, #3f6b4a); background:var(--status-vacant-bg, #d9f2dd); border:1px solid #c3e3c6; flex-shrink:0; }
  .announce-post-meta{ flex:1; min-width:0; }
  .announce-post-name{ font-weight:700; font-size:13px; color:var(--text-dark, #243026); display:flex; align-items:center; flex-wrap:wrap; gap:4px 6px; }
  .announce-post-name .tag{ flex-shrink:0; }
  .announce-restrict-btn{ flex-shrink:0; }
  .announce-post-name .tag{ font-size:9.5px; font-weight:700; text-transform:uppercase; letter-spacing:0.3px; color:var(--green-accent, #2f6f3c); background:var(--status-vacant-bg, #d9f2dd); padding:2px 7px; border-radius:20px; }
  .announce-post-time{ font-size:11px; color:var(--text-light, #8a9690); margin-top:1px; }
  .announce-post-body{ font-size:13.5px; color:var(--text-dark, #243026); line-height:1.6; margin-bottom:10px; white-space:pre-wrap; padding-left:46px; }

  /* Social-app action bar: a thin divider, small muted count above it,
     then a plain icon+label row (no border/pill) with just a soft hover
     background — reads as an action, not a form control. */
  .announce-count-row{ padding-left:46px; margin-bottom:6px; }
  .announce-count-link{ background:none; border:none; padding:0; font-size:11.5px; color:var(--text-light, #8a9690); cursor:pointer; }
  .announce-count-link:hover{ text-decoration:underline; }
  .announce-restricted-tag{ font-size:10px; font-weight:700; color:var(--status-maintenance, #c9962f); background:var(--status-maintenance-bg, #f6ecd6); text-transform:uppercase; letter-spacing:0.3px; padding:4px 10px; border-radius:20px; margin-left:8px; }

  .announce-action-bar{ border-top:1px solid #f0f2f0; padding-left:46px; display:flex; }
  .announce-action-btn{ display:flex; align-items:center; gap:7px; background:none; border:none; padding:9px 14px 0 0; margin-top:2px; font-size:12.5px; font-weight:600; color:var(--text-mid, #5b6b60); cursor:pointer; border-radius:6px; }
  .announce-action-btn:hover{ color:var(--green-accent, #2f6f3c); }
  .announce-action-btn svg{ width:15px; height:15px; }

  .announce-thread{ margin-top:6px; padding:12px 0 2px 46px; }
  .announce-comment{ display:flex; gap:8px; margin-bottom:10px; align-items:flex-start; }
  .announce-comment .announce-avatar{ width:26px; height:26px; font-size:10px; }
  .announce-comment-body{ flex:1; background:#f7f9f7; border-radius:10px; padding:9px 12px; }
  .announce-comment-name{ font-weight:600; font-size:12px; color:var(--text-dark, #243026); }
  .announce-comment-text{ font-size:12.5px; color:var(--text-dark, #243026); margin-top:2px; }
  .announce-comment-time{ font-size:10.5px; color:var(--text-light, #8a9690); margin-top:3px; }

  .announce-comment-composer{ display:flex; gap:8px; margin-top:6px; }
  .announce-comment-composer input{ flex:1; border:1px solid var(--border, #e2e6e2); border-radius:20px; padding:9px 14px; font-size:12.5px; font-family:inherit; }
  .announce-comment-composer input:focus{ outline:none; border-color:var(--green-accent, #2f6f3c); }
  .announce-send-comment{ border-radius:20px; padding:9px 16px; }
  .announce-comment-disabled-note{ font-size:12px; color:var(--text-light, #8a9690); font-style:italic; }

  .announce-toast{ position:fixed; bottom:24px; right:24px; background:var(--text-dark, #243026); color:#fff; padding:10px 18px; border-radius:8px; font-size:12.5px; z-index:999; display:none; box-shadow:0 4px 12px rgba(0,0,0,0.15); }
  .announce-toast.error{ background:var(--status-occupied, #d9564f); }
  .announce-toast.visible{ display:block; }

  @media (max-width: 640px){
    .announce-post{ padding:14px; }
    .announce-post-head{ align-items:flex-start; }
    .announce-restrict-btn{ padding:6px 10px; font-size:11.5px; }
    /* Drop the avatar-width indent so text gets the full card width */
    .announce-post-body, .announce-count-row, .announce-action-bar, .announce-thread{ padding-left:0; }
    .announce-composer{ flex-direction:column; align-items:stretch; }
    .announce-composer textarea{ width:100%; box-sizing:border-box; }
    .announce-composer .announce-btn{ align-self:flex-end; }
    .announce-toast{ left:16px; right:16px; bottom:16px; text-align:center; }
  }
</style>

<script>
(function(){
  const card = document.getElementById('announceCard');
  if (!card) return;

  const isAdmin = card.dataset.isAdmin === '1';
  const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
  const feedEl = document.getElementById('announceFeed');
  const emptyNote = document.getElementById('announceEmptyNote');

  function escapeHtml(str){
    const d = document.createElement('div');
    d.textContent = str ?? '';
    return d.innerHTML;
  }

  function showToast(message, isError){
    let toast = document.querySelector('.announce-toast');
    if (!toast) {
      toast = document.createElement('div');
      toast.className = 'announce-toast';
      document.body.appendChild(toast);
    }
    toast.textContent = message;
    toast.classList.toggle('error', !!isError);
    toast.classList.add('visible');
    setTimeout(() => toast.classList.remove('visible'), 2500);
  }

  async function apiFetch(url, options = {}) {
    const { headers: extraHeaders, ...restOptions } = options;
    const res = await fetch(url, {
      credentials: 'same-origin',
      ...restOptions,
      headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json', ...(extraHeaders || {}) },
    });
    const data = await res.json().catch(() => ({}));
    if (!res.ok) throw new Error(data.message || 'Something went wrong.');
    return data;
  }

  function commentCountLabel(count){
    return `${count} ${count === 1 ? 'comment' : 'comments'}`;
  }

  function postCard(a){
    return `
      <div class="announce-post" data-id="${a.id}">
        <div class="announce-post-head">
          <div class="announce-avatar">${escapeHtml(a.poster_initials)}</div>
          <div class="announce-post-meta">
            <div class="announce-post-name">${escapeHtml(a.poster_name)} <span class="tag">Admin</span></div>
            <div class="announce-post-time">${escapeHtml(a.posted_at)}</div>
          </div>
          ${isAdmin ? `<button class="announce-btn secondary announce-restrict-btn" data-id="${a.id}">${a.comments_restricted ? 'Unrestrict' : 'Restrict comments'}</button>` : ''}
        </div>
        <div class="announce-post-body">${escapeHtml(a.body)}</div>

        <div class="announce-count-row">
          <button class="announce-count-link" data-id="${a.id}">${commentCountLabel(a.comments_count)}</button>
          ${a.comments_restricted ? '<span class="announce-restricted-tag">Restricted</span>' : ''}
        </div>

        <div class="announce-action-bar">
          <button class="announce-action-btn announce-comment-action" data-id="${a.id}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
            Comment
          </button>
        </div>

        <div class="announce-thread" data-id="${a.id}" style="display:none;">
          <div class="announce-thread-list"></div>
        </div>
      </div>
    `;
  }

  function commentRow(c){
    return `
      <div class="announce-comment" data-comment-id="${c.id}">
        <div class="announce-avatar">${escapeHtml(c.author_initials)}</div>
        <div class="announce-comment-body">
          <div class="announce-comment-name">${escapeHtml(c.author_name)}${c.is_admin ? ' <span style="font-weight:400;color:var(--text-light,#8a9690);">(Admin)</span>' : ''}</div>
          <div class="announce-comment-text">${escapeHtml(c.body)}</div>
          <div class="announce-comment-time">${escapeHtml(c.posted_at)}</div>
        </div>
        ${isAdmin ? `<button class="announce-btn danger-text announce-delete-comment" data-comment-id="${c.id}">Delete</button>` : ''}
      </div>
    `;
  }

  function commentComposerHtml(announcementId, restricted){
    if (restricted && !isAdmin) {
      return '<div class="announce-comment-disabled-note">Comments are restricted on this announcement.</div>';
    }
    return `
      <div class="announce-comment-composer">
        <input type="text" placeholder="Write a comment..." maxlength="2000" class="announce-comment-input" data-id="${announcementId}">
        <button class="announce-btn announce-send-comment" data-id="${announcementId}">Send</button>
      </div>
    `;
  }

  async function loadFeed(){
    try {
      const data = await apiFetch('/announcements');
      const items = data.announcements || [];
      emptyNote.style.display = items.length ? 'none' : 'flex';
      feedEl.innerHTML = items.map(postCard).join('');
    } catch (e) {
      showToast(e.message, true);
    }
  }

  async function openThread(id){
    const thread = card.querySelector(`.announce-thread[data-id="${id}"]`);
    if (!thread) return;

    thread.style.display = 'block';
    const list = thread.querySelector('.announce-thread-list');
    list.innerHTML = '<div class="announce-post-time">Loading...</div>';

    try {
      const data = await apiFetch(`/announcements/${id}/comments`);
      const comments = data.comments || [];
      list.innerHTML = comments.map(commentRow).join('') + commentComposerHtml(id, data.comments_restricted);
    } catch (e) {
      list.innerHTML = '';
      showToast(e.message, true);
    }

    return thread;
  }

  async function toggleThread(id){
    const thread = card.querySelector(`.announce-thread[data-id="${id}"]`);
    if (!thread) return;

    if (thread.style.display !== 'none') {
      thread.style.display = 'none';
      return;
    }

    await openThread(id);
  }

  async function focusCommentBox(id){
    let thread = card.querySelector(`.announce-thread[data-id="${id}"]`);
    if (thread.style.display === 'none') {
      thread = await openThread(id);
    }
    const input = thread.querySelector('.announce-comment-input');
    if (input) input.focus();
  }

  async function sendComment(id, input){
    const body = input.value.trim();
    if (!body) return;

    try {
      const data = await apiFetch(`/announcements/${id}/comments`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ body }),
      });

      const list = card.querySelector(`.announce-thread[data-id="${id}"] .announce-thread-list`);
      const composer = list.querySelector('.announce-comment-composer');
      composer.insertAdjacentHTML('beforebegin', commentRow(data.comment));
      input.value = '';

      const countLink = card.querySelector(`.announce-count-link[data-id="${id}"]`);
      if (countLink) countLink.textContent = commentCountLabel(data.comments_count);
    } catch (e) {
      showToast(e.message, true);
    }
  }

  async function deleteComment(commentId, row){
    if (!confirm('Delete this comment?')) return;

    try {
      const data = await apiFetch(`/announcements/comments/${commentId}`, { method: 'DELETE' });
      const post = row.closest('.announce-post');
      row.remove();
      if (post) {
        const countLink = post.querySelector('.announce-count-link');
        if (countLink) countLink.textContent = commentCountLabel(data.comments_count);
      }
    } catch (e) {
      showToast(e.message, true);
    }
  }

  async function toggleRestrict(id, btn){
    try {
      const data = await apiFetch(`/announcements/${id}/restrict`, { method: 'PATCH' });
      btn.textContent = data.comments_restricted ? 'Unrestrict' : 'Restrict comments';
      const post = card.querySelector(`.announce-post[data-id="${id}"]`);
      const row = post?.querySelector('.announce-count-row');
      const tag = row?.querySelector('.announce-restricted-tag');
      if (data.comments_restricted && !tag) {
        row.insertAdjacentHTML('beforeend', '<span class="announce-restricted-tag">Restricted</span>');
      } else if (!data.comments_restricted && tag) {
        tag.remove();
      }
      showToast(data.message);
    } catch (e) {
      showToast(e.message, true);
    }
  }

  if (isAdmin) {
    const composerInput = document.getElementById('announceComposerInput');
    const postBtn = document.getElementById('announcePostBtn');

    postBtn.addEventListener('click', async () => {
      const body = composerInput.value.trim();
      if (!body) { showToast('Write something before posting.', true); return; }

      postBtn.disabled = true;
      try {
        const data = await apiFetch('/announcements', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ body }),
        });
        feedEl.insertAdjacentHTML('afterbegin', postCard(data.announcement));
        emptyNote.style.display = 'none';
        composerInput.value = '';
        showToast('Announcement posted.');
      } catch (e) {
        showToast(e.message, true);
      } finally {
        postBtn.disabled = false;
      }
    });
  }

  card.addEventListener('click', (e) => {
    const countLink = e.target.closest('.announce-count-link');
    if (countLink) { toggleThread(countLink.dataset.id); return; }

    const commentActionBtn = e.target.closest('.announce-comment-action');
    if (commentActionBtn) { focusCommentBox(commentActionBtn.dataset.id); return; }

    const sendBtn = e.target.closest('.announce-send-comment');
    if (sendBtn) {
      const input = card.querySelector(`.announce-comment-input[data-id="${sendBtn.dataset.id}"]`);
      sendComment(sendBtn.dataset.id, input);
      return;
    }

    const deleteBtn = e.target.closest('.announce-delete-comment');
    if (deleteBtn) { deleteComment(deleteBtn.dataset.commentId, deleteBtn.closest('.announce-comment')); return; }

    const restrictBtn = e.target.closest('.announce-restrict-btn');
    if (restrictBtn) { toggleRestrict(restrictBtn.dataset.id, restrictBtn); return; }
  });

  card.addEventListener('keydown', (e) => {
    if (e.key === 'Enter' && e.target.classList.contains('announce-comment-input')) {
      e.preventDefault();
      sendComment(e.target.dataset.id, e.target);
    }
  });

  loadFeed();
})();
</script>