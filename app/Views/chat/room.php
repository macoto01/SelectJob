<?php $activeNav='chat'; $me=auth_user(); ?>
<div class="chat-wrap">
  <div class="chat-header">
    <div class="chat-header-avatar"><svg width="40" height="40" viewBox="0 0 44 44" fill="none"><circle cx="22" cy="22" r="22" fill="#3a7bd5"/><circle cx="22" cy="17" r="8" fill="rgba(255,255,255,0.8)"/><ellipse cx="22" cy="38" rx="13" ry="9" fill="rgba(255,255,255,0.6)"/></svg></div>
    <div class="chat-header-info"><p class="chat-header-name">担当アドバイザー</p><p class="chat-header-sub">平日 10:00〜19:30 対応</p></div>
  </div>
  <div class="chat-messages" id="chat-messages">
    <?php if (empty($messages)): ?>
      <div class="chat-empty">
        <p>アドバイザーへのご質問・ご相談はこちらからどうぞ。</p>
      </div>
    <?php else: ?>
      <?php foreach ($messages as $msg): ?>
        <?php $isMine = ((int)$msg['sender_id'] === (int)$me['id']); ?>
        <div class="chat-bubble-row <?= $isMine?'chat-bubble-row--mine':'chat-bubble-row--theirs' ?>">
          <?php if (!$isMine): ?>
            <div class="chat-bubble-avatar">
              <svg width="32" height="32" viewBox="0 0 44 44" fill="none">
                <circle cx="22" cy="22" r="22" fill="#1a2b4a"/>
                <circle cx="22" cy="17" r="8" fill="rgba(255,255,255,0.7)"/>
                <ellipse cx="22" cy="38" rx="13" ry="9" fill="rgba(255,255,255,0.5)"/>
              </svg>
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
    <form method="post" action="<?= h(base_url('/chat/send')) ?>" class="chat-form" id="chat-form">
      <?php require BASE_PATH . '/app/Views/snippets/csrf.php'; ?>
      <textarea name="body" id="chat-input" class="chat-textarea" placeholder="メッセージを入力（Shift+Enter で送信）" rows="1" required></textarea>
      <button type="submit" class="chat-send-btn"><svg width="18" height="18" viewBox="0 0 16 16" fill="currentColor"><path d="M1 1l14 7-14 7V9.5l10-1.5-10-1.5V1z"/></svg></button>
    </form>
  </div>
</div>
<script>
(function(){
  var el=document.getElementById('chat-messages'),ta=document.getElementById('chat-input'),form=document.getElementById('chat-form');
  var latestId=<?= (int)$latestId ?>;
  function scrollBottom(){el.scrollTop=el.scrollHeight;}scrollBottom();
  ta.addEventListener('keydown',function(e){if(e.key==='Enter'&&e.shiftKey){e.preventDefault();form.dispatchEvent(new Event('submit'));}});
  form.addEventListener('submit',function(){clearInterval(polling);});
  function esc(s){return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');}
  function append(msg){
    var isMine=msg.sender_role!=='admin';
    var t=new Date(msg.created_at.replace(' ','T'));
    var hh=String(t.getMonth()+1).padStart(2,'0')+'/'+String(t.getDate()).padStart(2,'0')+' '+String(t.getHours()).padStart(2,'0')+':'+String(t.getMinutes()).padStart(2,'0');
    var row=document.createElement('div');row.className='chat-bubble-row '+(isMine?'chat-bubble-row--mine':'chat-bubble-row--theirs');
    if(!isMine){row.innerHTML='<div class="chat-bubble-avatar"><svg width="32" height="32" viewBox="0 0 44 44" fill="none"><circle cx="22" cy="22" r="22" fill="#1a2b4a"/></svg></div><div class="chat-bubble-col"><span class="chat-bubble-name">'+esc(msg.sender_name)+'</span><div class="chat-bubble chat-bubble--theirs">'+esc(msg.body).replace(/\n/g,'<br>')+'</div><span class="chat-bubble-time">'+hh+'</span></div>';}
    else{row.innerHTML='<div class="chat-bubble-col"><div class="chat-bubble chat-bubble--mine">'+esc(msg.body).replace(/\n/g,'<br>')+'</div><span class="chat-bubble-time">'+hh+'</span></div>';}
    el.appendChild(row);latestId=Math.max(latestId,msg.id);scrollBottom();
  }
  var polling=setInterval(function(){fetch('<?= h(base_url('/chat/poll')) ?>?after='+latestId).then(function(r){return r.json();}).then(function(msgs){msgs.forEach(append);}).catch(function(){});},3000);
})();
</script>
