<div class="row g-0">
  <div class="col-12 col-lg-3 border-end" style="max-height: calc(100vh - 180px); overflow: auto;">
    <div class="p-3">
      <div class="d-flex align-items-center justify-content-between mb-2">
        <h5 class="mb-0">Chats</h5>
        <button class="btn btn-sm btn-outline-primary" id="refresh-conv">Refrescar</button>
      </div>
      <div class="input-group mb-3">
        <span class="input-group-text"><i class="ri ri-search-line"></i></span>
        <input type="text" class="form-control" id="search-input" placeholder="Buscar">
      </div>
      <ul class="list-group" id="chat-list"></ul>
    </div>
  </div>
  <div class="col-12 col-lg-9" style="max-height: calc(100vh - 180px); overflow: hidden;">
    <div class="d-flex flex-column h-100">
      <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom">
        <div class="d-flex align-items-center gap-2">
          <div class="avatar avatar-sm" id="peer-avatar"><span class="avatar-initial rounded-circle bg-primary">W</span></div>
          <div>
            <div class="fw-semibold" id="peer-title">Selecciona un chat</div>
            <div class="text-muted small" id="peer-status">Estado</div>
          </div>
        </div>
        <div class="d-flex align-items-center gap-2">
          <button class="btn btn-sm btn-outline-secondary" id="btn-voice"><i class="ri ri-mic-line"></i></button>
          <label class="btn btn-sm btn-outline-secondary mb-0">
            <i class="ri ri-attachment-2"></i><input type="file" id="file-input" class="d-none">
          </label>
        </div>
      </div>
      <div class="flex-grow-1 overflow-auto p-3" id="message-list" style="background: var(--bs-body-bg);"></div>
      <div class="border-top p-2">
        <form id="composer" class="d-flex align-items-center gap-2">
          <input type="text" class="form-control" id="composer-text" placeholder="Escribe un mensaje" autocomplete="off">
          <button class="btn btn-primary" type="submit"><i class="ri ri-send-plane-2-line"></i></button>
        </form>
      </div>
    </div>
    <button class="btn btn-primary rounded-circle p-0 position-fixed" id="btn-new-chat" style="width:56px;height:56px;right:24px;bottom:24px;">
      <i class="ri ri-chat-new-line fs-4"></i>
    </button>
  </div>
</div>
<script>
  window.__WA__ = { base: @json($apiUrl), apiKey: @json($apiKey) };
  (function(){
    const base = window.__WA__.base;
    const apiKey = window.__WA__.apiKey;
    const headers = { 'X-API-Key': apiKey, 'Content-Type': 'application/json' };
    let currentPeer = null;
    const chatList = document.getElementById('chat-list');
    const messageList = document.getElementById('message-list');
    const composer = document.getElementById('composer');
    const input = document.getElementById('composer-text');
    const fileInput = document.getElementById('file-input');
    const refreshBtn = document.getElementById('refresh-conv');
    const searchInput = document.getElementById('search-input');
    const peerTitle = document.getElementById('peer-title');
    const peerStatus = document.getElementById('peer-status');
    const btnNewChat = document.getElementById('btn-new-chat');
    function el(tag, cls, html){ const e=document.createElement(tag); if(cls)e.className=cls; if(html!=null)e.innerHTML=html; return e; }
    function fmtTime(ts){ const d=new Date(ts); return d.toLocaleString(); }
    async function loadConversations(){
      const res = await fetch(base + '/api/whatsapp/conversations', { headers });
      const json = await res.json();
      const items = (json.conversations||[]);
      const q = (searchInput.value||'').toLowerCase();
      const filtered = q ? items.filter(x => (x.peer||'').toLowerCase().includes(q)) : items;
      chatList.innerHTML = '';
      filtered.forEach(c=>{
        const item = el('li','list-group-item list-group-item-action d-flex align-items-center justify-content-between');
        const left = el('div','d-flex align-items-center gap-2');
        const av = el('div','avatar avatar-xs'); av.innerHTML = '<span class="avatar-initial rounded-circle bg-primary">W</span>';
        const name = el('div',null, c.peer);
        left.appendChild(av); left.appendChild(name);
        const when = el('small','text-muted', fmtTime(c.createdAt));
        item.appendChild(left); item.appendChild(when);
        item.style.cursor='pointer';
        item.addEventListener('click', ()=>selectPeer(c.peer));
        chatList.appendChild(item);
      });
    }
    async function loadThread(peer){
      const url = base + '/api/whatsapp/thread?peer=' + encodeURIComponent(peer);
      const res = await fetch(url, { headers });
      const json = await res.json();
      const msgs = json.messages||[];
      messageList.innerHTML='';
      msgs.forEach(m=>{
        const isOut = (m.from||'').includes('@s.whatsapp.net') ? m.from.includes('s.whatsapp.net') && (m.from=== (window.__WA_USER__||'')) : false;
        const wrap = el('div','mb-2 d-flex '+(isOut?'justify-content-end':'justify-content-start'));
        const bubble = el('div','p-2 rounded '+(isOut?'bg-primary text-white':'bg-light'));
        let text = '';
        try { const obj = JSON.parse(m.message||'{}'); text = obj.conversation || obj?.extendedTextMessage?.text || '[media]'; } catch { text = '[message]'; }
        bubble.innerHTML = '<div class="small">'+text+'</div><div class="text-end small opacity-75">'+fmtTime(m.createdAt)+' · '+(m.status||'')+'</div>';
        wrap.appendChild(bubble);
        messageList.appendChild(wrap);
      });
      messageList.scrollTop = messageList.scrollHeight;
    }
    async function selectPeer(peer){
      currentPeer = peer;
      peerTitle.textContent = peer;
      peerStatus.textContent = '';
      await loadThread(peer);
    }
    composer.addEventListener('submit', async (e)=>{
      e.preventDefault();
      if(!currentPeer) return;
      const text = (input.value||'').trim();
      if(!text) return;
      input.value='';
      await fetch(base + '/api/whatsapp/send', { method:'POST', headers, body: JSON.stringify({ to: currentPeer, message: text }) });
      await loadThread(currentPeer);
    });
    fileInput.addEventListener('change', async ()=>{
      if(!currentPeer) return;
      const f = fileInput.files[0]; if(!f) return;
      const fd = new FormData(); fd.append('document', f); fd.append('to', currentPeer);
      await fetch(base + '/api/whatsapp/send-document', { method:'POST', headers: { 'X-API-Key': apiKey }, body: fd });
      fileInput.value=''; await loadThread(currentPeer);
    });
    refreshBtn.addEventListener('click', loadConversations);
    searchInput.addEventListener('input', loadConversations);
    btnNewChat.addEventListener('click', async ()=>{
      const phone = prompt('Número (con +código país), ej: +58XXXXXXXXXX');
      if(!phone) return;
      currentPeer = phone;
      peerTitle.textContent = phone;
      await loadThread(phone);
    });
    function initSocket(){
      const s = document.createElement('script');
      s.src = base + '/socket.io/socket.io.js';
      s.onload = ()=>{
        const socket = io(base, { transports: ['websocket'] });
        socket.on('message-received', (m)=>{ if(m && m.from===currentPeer) loadThread(currentPeer); });
        socket.on('message-updated', (m)=>{ if(currentPeer) loadThread(currentPeer); });
        socket.on('presence-update', (p)=>{ if(p && p.id===currentPeer) peerStatus.textContent = p?.lastKnownPresence || ''; });
      };
      document.body.appendChild(s);
    }
    loadConversations();
    initSocket();
  })();
</script>
