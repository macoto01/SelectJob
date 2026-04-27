<?php
$activeNav = 'admin_users';
require BASE_PATH . '/app/Views/snippets/flash.php';
?>
<div class="admin-header">
  <div style="display:flex;align-items:center;gap:12px;">
    <a href="<?= h(base_url('/admin/users')) ?>" class="btn-back">← 一覧へ</a>
    <h1 class="admin-title">ユーザー詳細</h1>
  </div>
  <div style="display:flex;gap:8px;align-items:center;">
    <a href="<?= h(base_url('/admin/users/'.$user['id'].'/edit')) ?>" class="btn-admin-primary">編集</a>
    <a href="<?= h(base_url('/admin/resume/'.$user['id'])) ?>" class="btn-admin-secondary">
      <svg width="13" height="13" viewBox="0 0 16 16" fill="currentColor" style="vertical-align:middle;margin-right:4px;"><path d="M4 1h8a1 1 0 011 1v12a1 1 0 01-1 1H4a1 1 0 01-1-1V2a1 1 0 011-1zm0 1v12h8V2H4zm1 2h6v1H5V4zm0 3h6v1H5V7zm0 3h4v1H5v-1z"/></svg>
      履歴書を確認
    </a>
    <?php if (!empty($user['chat_room_id'])): ?>
      <a href="<?= h(base_url('/admin/chat/'.$user['chat_room_id'])) ?>" class="btn-admin-secondary">チャットを開く</a>
    <?php endif; ?>
    <?php if ($user['role']!=='admin'): ?>
    <form method="post" action="<?= h(base_url('/admin/users/'.$user['id'].'/toggle')) ?>" onsubmit="return confirm('ステータスを変更しますか？')">
<?php require BASE_PATH . '/app/Views/snippets/csrf.php'; ?>
      <button type="submit" class="btn-admin-sm <?= $user['is_active']?'btn-admin-sm--delete':'btn-admin-sm--edit' ?>" style="padding:7px 14px;font-size:12px;"><?= $user['is_active']?'無効化':'有効化' ?></button>
    </form>
    <?php endif; ?>
  </div>
</div>
<div style="display:grid;grid-template-columns:1fr 280px;gap:16px;align-items:start;">
  <div class="admin-form-card">
    <div class="admin-form-section">
      <h2 class="admin-form-section-title">アカウント情報</h2>
      <div style="display:flex;align-items:center;gap:16px;margin-bottom:20px;padding:16px;background:var(--gray-bg);border-radius:8px;">
        <div style="width:56px;height:56px;border-radius:50%;background:var(--blue);color:#fff;display:flex;align-items:center;justify-content:center;font-size:22px;font-weight:700;flex-shrink:0;"><?= h(mb_substr($user['name'],0,1)) ?></div>
        <div>
          <p style="font-size:18px;font-weight:700;color:var(--navy);"><?= h($user['name']) ?></p>
          <p style="font-size:12px;color:var(--text-muted);margin-top:2px;"><?= h($user['email']) ?></p>
          <div style="display:flex;gap:6px;margin-top:6px;">
            <?php $badgeValue=$user['role'];$badgeLabel=$user['role']==='admin'?lang('role.admin'):lang('role.user');$badgeType='role';require BASE_PATH.'/app/Views/snippets/status_badge.php'; ?>
            <?php $badgeValue=$user['is_active']?'active':'closed';$badgeLabel=$user['is_active']?lang('active'):lang('inactive');$badgeType='status';require BASE_PATH.'/app/Views/snippets/status_badge.php'; ?>
          </div>
        </div>
      </div>
      <table class="resume-table">
        <tr><th style="width:160px;">ユーザーID</th><td><?= (int)$user['id'] ?></td></tr>
        <tr><th>氏名</th><td><?= h($user['name']) ?></td></tr>
        <tr><th>メールアドレス</th><td><?= h($user['email']) ?></td></tr>
        <tr><th>権限</th><td><?= $user['role']==='admin'?lang('role.admin'):lang('role.user'); ?></td></tr>
        <tr><th>ステータス</th><td><?= $user['is_active']?lang('active'):lang('inactive') ?></td></tr>
        <tr><th>最終ログイン</th><td><?= $user['last_login_at']?date('Y年m月d日 H:i',strtotime($user['last_login_at'])):'未ログイン' ?></td></tr>
        <tr><th>登録日時</th><td><?= date('Y年m月d日 H:i',strtotime($user['created_at'])) ?></td></tr>
      </table>
    </div>
  </div>
  <div style="display:flex;flex-direction:column;gap:12px;">
    <div class="admin-stat-card"><span class="admin-stat-num"><?= (int)$user['application_count'] ?></span><span class="admin-stat-label">応募件数</span></div>
    <div class="admin-stat-card"><span class="admin-stat-num"><?= (int)$user['message_count'] ?></span><span class="admin-stat-label">チャットメッセージ数</span></div>
    <?php if (!empty($user['chat_room_id'])): ?>
    <div style="background:var(--white);border:0.5px solid var(--border);border-radius:10px;padding:16px;">
      <p style="font-size:12px;font-weight:600;color:var(--navy);margin-bottom:10px;">クイックアクション</p>
      <a href="<?= h(base_url('/admin/chat/'.$user['chat_room_id'])) ?>" class="btn-admin-secondary" style="justify-content:center;">チャット履歴を見る</a>
    </div>
    <?php endif; ?>
  </div>
</div>
