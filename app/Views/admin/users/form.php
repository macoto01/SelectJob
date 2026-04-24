<?php
$activeNav = 'admin_users';
$isEdit    = !empty($user);
$actionUrl = $isEdit ? base_url('/admin/users/'.$user['id'].'/update') : base_url('/admin/users/store');
$pageLabel = $isEdit ? 'ユーザー編集' : 'ユーザー作成';
require BASE_PATH . '/app/Views/snippets/flash.php';
?>
<div class="admin-header">
  <div style="display:flex;align-items:center;gap:12px;">
    <?php if ($isEdit): ?>
      <a href="<?= h(base_url('/admin/users/'.$user['id'])) ?>" class="btn-back">← 詳細へ</a>
    <?php else: ?>
      <a href="<?= h(base_url('/admin/users')) ?>" class="btn-back">← 一覧へ</a>
    <?php endif; ?>
    <h1 class="admin-title"><?= $pageLabel ?></h1>
  </div>
</div>
<form method="post" action="<?= h($actionUrl) ?>">
<?php require BASE_PATH . '/app/Views/snippets/csrf.php'; ?>
  <div class="admin-form-section">
    <h2 class="admin-form-section-title">基本情報</h2>
    <div class="admin-form-row">
      <label class="admin-label">氏名 <span class="badge-required">必須</span></label>
      <input type="text" name="name" value="<?= h($user['name']??'') ?>" class="admin-input" placeholder="例: 山田 太郎" required>
    </div>
    <div class="admin-form-row">
      <label class="admin-label">メールアドレス <span class="badge-required">必須</span></label>
      <div class="admin-form-col">
        <input type="email" name="email" value="<?= h($user['email']??'') ?>" class="admin-input" placeholder="例: user@example.com" required>
        <p class="admin-hint">ログイン時のIDになります</p>
      </div>
    </div>
    <div class="admin-form-row">
      <label class="admin-label">パスワード<?php if(!$isEdit):?><span class="badge-required">必須</span><?php endif;?></label>
      <div class="admin-form-col">
        <input type="password" name="password" class="admin-input" placeholder="<?= $isEdit?'変更する場合のみ入力':'8文字以上推奨' ?>" <?= !$isEdit?'required':'' ?>>
        <?php if ($isEdit): ?><p class="admin-hint">空欄のままにすると現在のパスワードを維持します</p><?php endif; ?>
      </div>
    </div>
  </div>
  <div class="admin-form-section">
    <h2 class="admin-form-section-title">権限・ステータス</h2>
    <div class="admin-form-row">
      <label class="admin-label">権限</label>
      <div class="admin-form-col">
        <select name="role" class="admin-select admin-select--sm">
          <option value="user"  <?= ($user['role']??'user')==='user' ?'selected':'' ?>>一般ユーザー</option>
          <option value="admin" <?= ($user['role']??'')==='admin'?'selected':'' ?>>管理者</option>
        </select>
      </div>
    </div>
    <div class="admin-form-row">
      <label class="admin-label">ステータス</label>
      <label class="admin-check-label">
        <input type="checkbox" name="is_active" value="1" <?= ($user['is_active']??1)?'checked':'' ?>>
        有効（チェックを外すとログイン不可になります）
      </label>
    </div>
  </div>
  <div class="admin-form-footer">
    <a href="<?= h(base_url($isEdit?'/admin/users/'.$user['id']:'/admin/users')) ?>" class="btn-admin-cancel">キャンセル</a>
    <button type="submit" class="btn-admin-submit"><?= $isEdit?'ユーザーを更新する':'ユーザーを作成する' ?></button>
  </div>
</form>
