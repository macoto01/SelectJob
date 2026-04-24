<?php
$activeNav = 'admin_chat';
$me = auth_user();
?>
<div class="admin-header" style="margin-bottom:12px;">
  <div style="display:flex;align-items:center;gap:12px;">
    <a href="<?= h(base_url('/admin/chat')) ?>" class="btn-back">← 一覧へ</a>
    <?php $avatarName=$roomUser['name'];$avatarEmail=$roomUser['email'];require BASE_PATH.'/app/Views/snippets/user_avatar_cell.php'; ?>
  </div>
</div>
<div class="chat-wrap">
  <div class="chat-messages" id="chat-messages">
    <?php if (empty($messages)): ?>
      <div class="chat-empty">
        <p>まだメッセージがありません。</p>
      </div>
    <?php else: ?>
      <?php foreach ($messages as $msg): ?>
        <?php $isMine = ((int)$msg['sender_id'] === (int)$me['id']); ?>
        <div class="chat-bubble-row <?= $isMine?'chat-bubble-row--mine':'chat-bubble-row--theirs' ?>">
          <?php if (!$isMine): ?>
            <div class="chat-bubble-avatar">
              <div class="user-avatar" style="width:32px;height:32px;font-size:13px;">
                <?= h(mb_substr($msg['sender_name'], 0, 1)) ?>
              </div>
            </div>
          <?php endif; ?>
          <div class="chat-bubble-col">
            <?php if (!$isMine): ?>
              <span class="chat-bubble-name"><?= h($msg['sender_name']) ?></span>
            <?php endif; ?>
            
            <div class="chat-bubble chat-bubble--<?= $isMine ? 'mine' : 'theirs' ?>">
              <?= nl2br(h($msg['body'])) ?>
            </div>
            
            <span class="chat-bubble-time">
              <?= date('m/d H:i', strtotime($msg['created_at'])) ?>
              <?php if ($isMine && $msg['is_read']): ?>
                <span class="chat-read">既読</span>
              <?php endif; ?>
            </span>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
  <div class="chat-input-wrap">
    <form method="post" action="<?= h(base_url('/admin/chat/' . $room['id'] . '/send')) ?>" class="chat-form" id="chat-form">
      <?php require BASE_PATH . '/app/Views/snippets/csrf.php'; ?>
      <textarea name="body" id="chat-input" class="chat-textarea" placeholder="メッセージを入力（Shift+Enter で送信）" rows="1" required></textarea>
      <button type="submit" class="chat-send-btn"><svg width="18" height="18" viewBox="0 0 16 16" fill="currentColor"><path d="M1 1l14 7-14 7V9.5l10-1.5-10-1.5V1z"/></svg></button>
    </form>
  </div>
</div>
<script>

(function(){
  var el = document.getElementById('chat-messages');
  var ta = document.getElementById('chat-input');
  var form = document.getElementById('chat-form');
  
  var latestId = <?= (int)$latestId ?>;
  var roomId = <?= (int)$room['id'] ?>;
  var pollUrl = '<?= h(base_url('/admin/chat/')) ?>' + roomId + '/poll';
  var sendUrl = form.getAttribute('action');
  
  function scrollBottom() {
    el.scrollTop = el.scrollHeight;
  }
  scrollBottom();
  
  ta.addEventListener('keydown', function(e) {
    if (e.key === 'Enter' && e.shiftKey) {
      e.preventDefault();
      form.dispatchEvent(new Event('submit'));
    }
  });
  
  // 非同期送信処理
  form.addEventListener('submit', function(e) {
    e.preventDefault();
    
    var body = ta.value.trim();
    if (body === '') return;

    var btn = form.querySelector('button');
    if (btn.disabled) return;

    // 送信ボタンを無効化（二重送信防止）
    btn.disabled = true;
    btn.style.opacity = '0.5';

    var formData = new FormData(form);

    fetch(sendUrl, {
      method: 'POST',
      body: formData,
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(r) {
      if (!r.ok) throw new Error('送信に失敗しました');
      return r.json();
    })
    .then(function(res) {
      ta.value = ''; // 入力欄をクリア
      ta.style.height = 'auto';
      poll(); // 即座に最新メッセージを確認
    })
    .catch(function(err) {
      alert('メッセージの送信に失敗しました。時間をおいて再度お試しください。');
    })
    .finally(function() {
      btn.disabled = false;
      btn.style.opacity = '1';
    });
  });
  
  function esc(s) {
    return String(s)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;');
  }
  
  function append(msg) {
    var isMine = msg.sender_role === 'admin';
    var t = new Date(msg.created_at.replace(/-/g, '/')); // ブラウザ互換性のため
    var hh = String(t.getMonth() + 1).padStart(2, '0') + '/' + 
             String(t.getDate()).padStart(2, '0') + ' ' + 
             String(t.getHours()).padStart(2, '0') + ':' + 
             String(t.getMinutes()).padStart(2, '0');
             
    var row = document.createElement('div');
    row.className = 'chat-bubble-row ' + (isMine ? 'chat-bubble-row--mine' : 'chat-bubble-row--theirs');
    
    if (!isMine) {
      row.innerHTML = '<div class="chat-bubble-avatar"><div class="user-avatar" style="width:32px;height:32px;font-size:13px;">' + esc(msg.sender_name.charAt(0)) + '</div></div>' +
                      '<div class="chat-bubble-col"><span class="chat-bubble-name">' + esc(msg.sender_name) + '</span>' +
                      '<div class="chat-bubble chat-bubble--theirs">' + esc(msg.body).replace(/\n/g, '<br>') + '</div>' +
                      '<span class="chat-bubble-time">' + hh + '</span></div>';
    } else {
      row.innerHTML = '<div class="chat-bubble-col"><div class="chat-bubble chat-bubble--mine">' + esc(msg.body).replace(/\n/g, '<br>') + '</div>' +
                      '<span class="chat-bubble-time">' + hh + '</span></div>';
    }
    
    el.appendChild(row);
    latestId = Math.max(latestId, msg.id);
    scrollBottom();
  }
  
  function poll() {
    fetch(pollUrl + '?after=' + latestId)
      .then(function(r) { 
        if (!r.ok) throw new Error();
        return r.json(); 
      })
      .then(function(msgs) { 
        if (msgs && msgs.length > 0) msgs.forEach(append); 
      })
      .catch(function() {
        console.warn('Polling failed, retrying in next cycle...');
        // 連続して失敗する場合の処理（必要に応じて追加）
      });
  }

  // テキストエリアの自動リサイズ
  ta.addEventListener('input', function() {
    this.style.height = 'auto';
    this.style.height = (this.scrollHeight) + 'px';
  });

  // 3秒ごとのポーリング開始
  var polling = setInterval(poll, 3000);

  // タブが非表示の時はポーリングを停止して負荷を下げる（オプション）
  document.addEventListener('visibilitychange', function() {
    if (document.hidden) clearInterval(polling);
    else polling = setInterval(poll, 3000);
  });
})();
</script>
