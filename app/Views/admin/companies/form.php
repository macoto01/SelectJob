<?php
$activeNav = 'admin';
$isEdit    = !empty($company);
$actionUrl = $isEdit ? base_url('/admin/companies/'.$company['id'].'/update') : base_url('/admin/companies/store');
$pageLabel = $isEdit ? '会社編集' : '会社追加';
require BASE_PATH . '/app/Views/snippets/flash.php';
?>
<div class="admin-header">
  <h1 class="admin-title"><?= $pageLabel ?></h1>
  <a href="<?= h(base_url('/admin')) ?>" class="btn-back">← 一覧へ戻る</a>
</div>
<form method="post" action="<?= h($actionUrl) ?>">
<?php require BASE_PATH . '/app/Views/snippets/csrf.php'; ?>
  <div class="admin-form-section">
    <h2 class="admin-form-section-title">会社情報</h2>
    <div class="admin-form-row"><label class="admin-label">会社名 <span class="badge-required">必須</span></label><input type="text" name="name" value="<?= h($company['name']??'') ?>" class="admin-input" placeholder="例: 株式会社テックスタート" required></div>
    <div class="admin-form-row"><label class="admin-label">業界</label><input type="text" name="industry" value="<?= h($company['industry']??'') ?>" class="admin-input admin-input--sm" placeholder="例: IT・ソフトウェア"></div>
    <div class="admin-form-row"><label class="admin-label">従業員数</label><input type="text" name="employees" value="<?= h($company['employees']??'') ?>" class="admin-input admin-input--sm" placeholder="例: 30〜50名"></div>
    <div class="admin-form-row"><label class="admin-label">設立年</label><input type="number" name="founded" value="<?= h($company['founded']??'') ?>" class="admin-input admin-input--sm" placeholder="例: 2018" min="1800" max="<?= date('Y') ?>"></div>
    <div class="admin-form-row"><label class="admin-label">ウェブサイト</label><input type="url" name="website" value="<?= h($company['website']??'') ?>" class="admin-input" placeholder="https://example.com"></div>
    <div class="admin-form-row"><label class="admin-label">所在地</label><input type="text" name="address" value="<?= h($company['address']??'') ?>" class="admin-input" placeholder="例: 東京都渋谷区渋谷1-1-1"></div>
    <div class="admin-form-row"><label class="admin-label">会社概要</label><textarea name="description" rows="5" class="admin-textarea" placeholder="会社の事業内容や特徴を記述してください"><?= h($company['description']??'') ?></textarea></div>
  </div>
  <div class="admin-form-footer">
    <a href="<?= h(base_url('/admin')) ?>" class="btn-admin-cancel">キャンセル</a>
    <button type="submit" class="btn-admin-submit"><?= $isEdit?'更新する':'会社を追加する' ?></button>
  </div>
</form>
