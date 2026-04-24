<?php
/**
 * app/Views/settings/index.php
 * ユーザー設定：パスワード変更
 */
$activeNav = 'settings';
require BASE_PATH . '/app/Views/snippets/flash.php';
?>

<div class="settings-wrap">

  <!-- ===== パスワード変更 ===== -->
  <div class="settings-card">
    <h2 class="settings-title">
      <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor"><path d="M8 1a4 4 0 014 4v1h1v9H3V6h1V5a4 4 0 014-4zm0 1a3 3 0 00-3 3v1h6V5a3 3 0 00-3-3zm0 7a1 1 0 110 2 1 1 0 010-2z"/></svg>
      パスワード変更
    </h2>
    <form method="post" action="<?= h(base_url('/settings/password')) ?>" class="settings-form">
      <?php require BASE_PATH . '/app/Views/snippets/csrf.php'; ?>
      <div class="settings-form-row">
        <label class="settings-label">現在のパスワード</label>
        <input type="password" name="current_password" class="settings-input" required>
      </div>
      <div class="settings-form-row">
        <label class="settings-label">新しいパスワード</label>
        <input type="password" name="new_password" class="settings-input" placeholder="8文字以上" required>
      </div>
      <div class="settings-form-row">
        <label class="settings-label">新しいパスワード（確認）</label>
        <input type="password" name="new_password_confirm" class="settings-input" required>
      </div>
      <div class="settings-form-footer">
        <button type="submit" class="btn-admin-submit">パスワードを変更する</button>
      </div>
    </form>
  </div>

  <!-- ===== アカウント情報 ===== -->
  <div class="settings-card">
    <h2 class="settings-title">
      <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor"><path d="M8 1a3 3 0 100 6 3 3 0 000-6zM3 14a5 5 0 0110 0H3z"/></svg>
      アカウント情報
    </h2>
    <?php $me = auth_user(); ?>
    <div class="settings-info-row">
      <span class="settings-info-label">氏名</span>
      <span class="settings-info-value"><?= h($me['name']) ?></span>
    </div>
    <div class="settings-info-row">
      <span class="settings-info-label">メールアドレス</span>
      <span class="settings-info-value"><?= h($me['email']) ?></span>
    </div>
    <div class="settings-info-row">
      <span class="settings-info-label">権限</span>
      <span class="settings-info-value"><?= h(lang('role.' . ($me['role'] ?? 'user'))) ?></span>
    </div>
  </div>

</div>
