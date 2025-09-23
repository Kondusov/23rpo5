<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/lib/auth.php';

start_session_if_needed();
$me = current_user();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Chat App</title>
    <link rel="stylesheet" href="/style.css" />
</head>
<body>
<div id="app">
    <div class="auth" <?php echo $me ? 'style="display:none"' : '' ?>>
        <h2>Вход / Регистрация</h2>
        <input id="auth-username" placeholder="Логин" />
        <input id="auth-password" placeholder="Пароль" type="password" />
        <div class="auth-actions">
            <button id="btn-login">Войти</button>
            <button id="btn-register">Регистрация</button>
        </div>
        <div id="auth-error" class="error"></div>
    </div>

    <div class="chat" <?php echo $me ? '' : 'style="display:none"' ?>>
        <div class="sidebar">
            <div class="me">
                <span id="me-name"><?php echo $me ? htmlspecialchars($me['username']) : '' ?></span>
                <button id="btn-logout">Выйти</button>
            </div>
            <div class="new-chat">
                <input id="new-chat-name" placeholder="Название группы" />
                <input id="new-chat-members" placeholder="Участники (id через запятую)" />
                <button id="btn-create-chat">Создать чат</button>
            </div>
            <h3>Чаты</h3>
            <ul id="chat-list"></ul>
        </div>
        <div class="messages">
            <div id="chat-title">Выберите чат</div>
            <div id="message-list"></div>
            <div class="composer">
                <input id="message-input" placeholder="Сообщение..." />
                <button id="btn-send">Отправить</button>
            </div>
        </div>
    </div>
</div>

<script>
async function fetchJSON(url, options){
  const res = await fetch(url, options);
  const contentType = res.headers.get('content-type') || '';
  if (contentType.includes('application/json')){
    const data = await res.json().catch(()=>{ throw new Error('Некорректный JSON от сервера'); });
    if (!res.ok || (data && data.error)){
      throw new Error((data && data.error) || `HTTP ${res.status}`);
    }
    return data;
  } else {
    const text = await res.text();
    // Часто HTML-страница ошибки PHP/Apache -> начинаются с '<'
    const hint = text.trim().startsWith('<') ? ' (сервер вернул HTML — проверьте пути и PHP-лог)' : '';
    throw new Error(`Неверный ответ сервера${hint}. Код ${res.status}`);
  }
}

const api = {
  async me(){
    return fetchJSON('api/auth.php?action=me');
  },
  async login(username, password){
    const body = new URLSearchParams({action:'login', username, password});
    return fetchJSON('api/auth.php', {method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body});
  },
  async register(username, password){
    const body = new URLSearchParams({action:'register', username, password});
    return fetchJSON('api/auth.php', {method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body});
  },
  async logout(){
    const body = new URLSearchParams({action:'logout'});
    return fetchJSON('api/auth.php', {method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body});
  },
  async chats(){
    return fetchJSON('api/chats.php');
  },
  async createChat(name, membersCsv){
    const body = new URLSearchParams({name, members: membersCsv, is_direct: '0'});
    return fetchJSON('api/chats.php', {method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body});
  },
  async messages(chatId, afterId=0){
    return fetchJSON(`api/messages.php?chat_id=${chatId}&after_id=${afterId}`);
  },
  async send(chatId, content){
    const body = new URLSearchParams({chat_id: String(chatId), content});
    return fetchJSON('api/messages.php', {method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body});
  },
  async poll(chatId, afterId){
    return fetchJSON(`api/poll.php?chat_id=${chatId}&after_id=${afterId}`);
  }
};

let state = {
  me: <?php echo $me ? json_encode(['id'=>(int)$me['id'],'username'=>$me['username']]) : 'null' ?>,
  chats: [],
  currentChatId: null,
  lastMessageId: 0,
  polling: false
};

const el = s => document.querySelector(s);
const chatList = el('#chat-list');
const msgList = el('#message-list');

function renderChats(){
  chatList.innerHTML = '';
  state.chats.forEach(c => {
    const li = document.createElement('li');
    li.textContent = c.is_direct ? `Диалог #${c.id}` : (c.name || `Чат #${c.id}`);
    li.dataset.id = c.id;
    li.addEventListener('click', ()=> selectChat(c.id, c));
    if (state.currentChatId === c.id) li.classList.add('active');
    chatList.appendChild(li);
  });
}

function renderMessages(messages){
  for (const m of messages) {
    state.lastMessageId = Math.max(state.lastMessageId, Number(m.id));
    const div = document.createElement('div');
    div.className = 'msg' + (Number(m.user_id) === Number(state.me.id) ? ' mine' : '');
    div.innerHTML = `<span class="from">#${m.user_id}</span> <span class="text"></span> <span class="time">${m.created_at}</span>`;
    div.querySelector('.text').textContent = m.content || '';
    msgList.appendChild(div);
  }
  msgList.scrollTop = msgList.scrollHeight;
}

async function selectChat(chatId, chat){
  state.currentChatId = chatId;
  state.lastMessageId = 0;
  el('#chat-title').textContent = chat?.name || (chat?.is_direct ? `Диалог #${chatId}` : `Чат #${chatId}`);
  msgList.innerHTML = '';
  try{
    const data = await api.messages(chatId, 0);
    renderMessages(data.messages || []);
  }catch(e){
    alert('Ошибка загрузки сообщений: ' + e.message);
  }
  startPolling();
}

async function loadChats(){
  try{
    const data = await api.chats();
    state.chats = data.chats || [];
    renderChats();
  }catch(e){
    alert('Ошибка загрузки чатов: ' + e.message);
  }
}

async function startPolling(){
  if (state.polling) return;
  state.polling = true;
  while (state.polling && state.currentChatId){
    try{
      const res = await api.poll(state.currentChatId, state.lastMessageId);
      const list = res.messages || [];
      if (list.length) renderMessages(list);
    }catch(e){
      await new Promise(r=>setTimeout(r, 1000));
    }
  }
  state.polling = false;
}

document.addEventListener('DOMContentLoaded', ()=>{
  // Auth
  el('#btn-login')?.addEventListener('click', async ()=>{
    const u = el('#auth-username').value.trim();
    const p = el('#auth-password').value.trim();
    try{
      await api.login(u,p);
      location.reload();
    }catch(e){
      el('#auth-error').textContent = e.message;
    }
  });
  el('#btn-register')?.addEventListener('click', async ()=>{
    const u = el('#auth-username').value.trim();
    const p = el('#auth-password').value.trim();
    try{
      await api.register(u,p);
      location.reload();
    }catch(e){
      el('#auth-error').textContent = e.message;
    }
  });
  el('#btn-logout')?.addEventListener('click', async ()=>{
    try{
      await api.logout();
      location.reload();
    }catch(e){
      alert('Ошибка выхода: ' + e.message);
    }
  });

  // Chats
  el('#btn-create-chat')?.addEventListener('click', async ()=>{
    const name = el('#new-chat-name').value.trim();
    const members = el('#new-chat-members').value.trim();
    try{
      await api.createChat(name, members);
      await loadChats();
    }catch(e){
      alert('Ошибка создания чата: ' + e.message);
    }
  });

  // Sending
  el('#btn-send')?.addEventListener('click', async ()=>{
    const input = el('#message-input');
    const text = input.value.trim();
    if (!text || !state.currentChatId) return;
    input.value = '';
    try{
      const r = await api.send(state.currentChatId, text);
      if (r.message){ renderMessages([r.message]); }
    }catch(e){
      alert('Ошибка отправки: ' + e.message);
    }
  });

  if (state.me){
    loadChats();
  }
});
</script>
</body>
</html>


