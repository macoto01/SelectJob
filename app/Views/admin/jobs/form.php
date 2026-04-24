<?php
$activeNav  = 'admin';
$isEdit     = !empty($job);
$actionUrl  = $isEdit ? base_url('/admin/jobs/'.$job['id'].'/update') : base_url('/admin/jobs/store');
$pageLabel  = $isEdit ? '求人編集' : '求人追加';
require BASE_PATH . '/app/Views/snippets/flash.php';
?>
<div class="admin-header">
  <h1 class="admin-title"><?= $pageLabel ?></h1>
  <a href="<?= h(base_url('/admin')) ?>" class="btn-back">← 一覧へ戻る</a>
</div>
<form method="post" action="<?= h($actionUrl) ?>">
<?php require BASE_PATH . '/app/Views/snippets/csrf.php'; ?>
  <div class="admin-form-section">
    <h2 class="admin-form-section-title">基本情報</h2>
    <div class="admin-form-row">
      <label class="admin-label">会社 <span class="badge-required">必須</span></label>
      <div class="admin-form-col">
        <select name="company_id" class="admin-select" required>
          <option value="">選択してください</option>
          <?php foreach ($companies as $c): ?>
            <option value="<?= $c['id'] ?>" <?= ($job['company_id']??'')==$c['id']?'selected':'' ?>><?= h($c['name']) ?></option>
          <?php endforeach; ?>
        </select>
        <p class="admin-hint">会社がない場合は先に<a href="<?= h(base_url('/admin/companies/create')) ?>" class="admin-link" target="_blank">会社を追加</a>してください。</p>
      </div>
    </div>
    <div class="admin-form-row">
      <label class="admin-label">職種名 <span class="badge-required">必須</span></label>
      <input type="text" name="title" value="<?= h($job['title']??'') ?>" class="admin-input" placeholder="例: バックエンドエンジニア" required>
    </div>
    <div class="admin-form-row">
      <label class="admin-label">雇用形態</label>
      <select name="job_type" class="admin-select admin-select--sm">
        <?php foreach (['','正社員','契約社員','派遣社員','業務委託','パート・アルバイト'] as $jt): ?>
          <option <?= ($job['job_type']??'')===$jt?'selected':'' ?>><?= h($jt) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="admin-form-row">
      <label class="admin-label">年収（万円）</label>
      <div class="admin-inline">
        <input type="number" name="salary_min" value="<?= h($job['salary_min']??'') ?>" class="admin-input admin-input--sm" placeholder="最低">
        <span class="admin-range-sep">〜</span>
        <input type="number" name="salary_max" value="<?= h($job['salary_max']??'') ?>" class="admin-input admin-input--sm" placeholder="最高">万円
      </div>
    </div>
    <div class="admin-form-row">
      <label class="admin-label">勤務地</label>
      <input type="text" name="location" value="<?= h($job['location']??'') ?>" class="admin-input" placeholder="例: 東京都渋谷区">
    </div>
  </div>
  <div class="admin-form-section">
    <h2 class="admin-form-section-title">求人詳細</h2>
    <div class="admin-form-row"><label class="admin-label">職務内容</label><textarea name="work_description" rows="6" class="admin-textarea"><?= h($job['work_description']??'') ?></textarea></div>
    <div class="admin-form-row"><label class="admin-label">職務内容の変更の範囲</label><input type="text" name="job_change_scope" value="<?= h($job['job_change_scope']??'') ?>" class="admin-input" placeholder="例: 会社の定める業務"></div>
    <div class="admin-form-row"><label class="admin-label">必要な経験/資格</label><textarea name="requirements" rows="4" class="admin-textarea"><?= h($job['requirements']??'') ?></textarea></div>
    <div class="admin-form-row"><label class="admin-label">歓迎スキル</label><textarea name="preferred" rows="3" class="admin-textarea"><?= h($job['preferred']??'') ?></textarea></div>
    <div class="admin-form-row"><label class="admin-label">待遇・福利厚生</label><textarea name="benefits" rows="3" class="admin-textarea"><?= h($job['benefits']??'') ?></textarea></div>
    <div class="admin-form-row"><label class="admin-label">勤務時間</label><input type="text" name="working_hours" value="<?= h($job['working_hours']??'') ?>" class="admin-input"></div>
    <div class="admin-form-row"><label class="admin-label">休日</label><input type="text" name="holiday" value="<?= h($job['holiday']??'') ?>" class="admin-input"></div>
  </div>
  <div class="admin-form-section">
    <h2 class="admin-form-section-title">タグ・オプション</h2>
    <div class="admin-form-row">
      <label class="admin-label">求人ポイントタグ</label>
      <div class="admin-form-col">
        <input type="text" name="tags" value="<?= h(implode(', ',$job['tags']??[])) ?>" class="admin-input" placeholder="例: 第二新卒歓迎, フルリモート（カンマ区切り）">
        <p class="admin-hint">カンマ区切りで複数入力できます</p>
      </div>
    </div>
    <div class="admin-form-row">
      <label class="admin-label">オプション</label>
      <div class="admin-checks">
        <label class="admin-check-label"><input type="checkbox" name="remote_work" value="1" <?= ($job['remote_work']??0)?'checked':'' ?>> リモートワーク可</label>
        <label class="admin-check-label"><input type="checkbox" name="flex_time" value="1" <?= ($job['flex_time']??0)?'checked':'' ?>> フレックスあり</label>
        <label class="admin-check-label"><input type="checkbox" name="is_new" value="1" <?= ($job['is_new']??0)?'checked':'' ?>> NEWバッジ表示</label>
      </div>
    </div>
    <div class="admin-form-row">
      <label class="admin-label">ステータス</label>
      <select name="status" class="admin-select admin-select--sm">
        <option value="active" <?= ($job['status']??'active')==='active'?'selected':'' ?>>公開中</option>
        <option value="closed" <?= ($job['status']??'')==='closed'?'selected':'' ?>>非公開</option>
      </select>
    </div>
  </div>
  <div class="admin-form-footer">
    <a href="<?= h(base_url('/admin')) ?>" class="btn-admin-cancel">キャンセル</a>
    <button type="submit" class="btn-admin-submit"><?= $isEdit ? '更新する' : '保存する' ?></button>
  </div>
</form>