<?php
$activeNav = 'admin';
$keyword   = $keyword   ?? '';
$status    = $status    ?? '';
$companyId = $companyId ?? 0;
$tab       = $tab       ?? 'jobs';
require BASE_PATH . '/app/Views/snippets/flash.php';
?>
<div class="admin-header">
  <h1 class="admin-title">求人管理</h1>
  <div class="admin-header-actions">
    <a href="<?= h(base_url('/admin/companies/create')) ?>" class="btn-admin-secondary">＋ 会社追加</a>
    <a href="<?= h(base_url('/admin/jobs/create')) ?>" class="btn-admin-primary">＋ 求人追加</a>
  </div>
</div>

<div class="admin-stats">
  <div class="admin-stat-card"><span class="admin-stat-num"><?= count($jobs) ?></span><span class="admin-stat-label">表示中</span></div>
  <div class="admin-stat-card" style="cursor:pointer;" onclick="setFilter('status','active')"><span class="admin-stat-num" style="color:#166534;"><?= $totalActive??0 ?></span><span class="admin-stat-label">公開中</span></div>
  <div class="admin-stat-card" style="cursor:pointer;" onclick="setFilter('status','closed')"><span class="admin-stat-num" style="color:var(--text-muted);"><?= $totalClosed??0 ?></span><span class="admin-stat-label">非公開</span></div>
  <div class="admin-stat-card"><span class="admin-stat-num"><?= count($allCompanies) ?></span><span class="admin-stat-label">登録会社数</span></div>
</div>

<div class="admin-search-tabs">
  <a href="<?= h(base_url('/admin?tab=jobs&keyword='.urlencode($keyword).'&status='.$status.'&company_id='.$companyId)) ?>" class="admin-search-tab<?= $tab!=='companies'?' admin-search-tab--active':'' ?>">求人一覧</a>
  <a href="<?= h(base_url('/admin?tab=companies&keyword='.urlencode($keyword))) ?>" class="admin-search-tab<?= $tab==='companies'?' admin-search-tab--active':'' ?>">登録会社一覧</a>
</div>

<form method="get" action="<?= h(base_url('/admin')) ?>" class="admin-search-form" id="search-form">
  <input type="hidden" name="tab" value="<?= h($tab) ?>">
  <div class="admin-search-row">
    <div class="admin-search-input-wrap">
      <svg class="admin-search-icon" width="14" height="14" viewBox="0 0 16 16" fill="currentColor"><path d="M6 1a5 5 0 100 10A5 5 0 006 1zm0 1a4 4 0 110 8A4 4 0 016 2zm5.7 8.3l3 3-.7.7-3-3 .7-.7z"/></svg>
      <input type="text" name="keyword" value="<?= h($keyword) ?>" placeholder="<?= $tab==='companies'?'会社名・業界で検索...':'求人名・会社名・勤務地で検索...' ?>" class="admin-search-keyword">
    </div>
    <?php if ($tab!=='companies'): ?>
    <div class="admin-filter-group">
      <button type="submit" name="status" value="" class="admin-filter-btn<?= $status===''?' admin-filter-btn--active':'' ?>">すべて</button>
      <button type="submit" name="status" value="active" class="admin-filter-btn admin-filter-btn--active-green<?= $status==='active'?' admin-filter-btn--active':'' ?>">公開中</button>
      <button type="submit" name="status" value="closed" class="admin-filter-btn<?= $status==='closed'?' admin-filter-btn--active':'' ?>">非公開</button>
    </div>
    <select name="company_id" class="admin-select admin-select--sm" onchange="this.form.submit()">
      <option value="0">会社を絞り込む</option>
      <?php foreach ($allCompanies as $co): ?>
        <option value="<?= $co['id'] ?>" <?= $companyId===(int)$co['id']?'selected':'' ?>><?= h($co['name']) ?></option>
      <?php endforeach; ?>
    </select>
    <?php endif; ?>
    <button type="submit" class="btn-admin-primary" style="padding:8px 20px;font-size:13px;">検索</button>
    <?php if ($keyword!==''||$status!==''||$companyId>0): ?>
      <a href="<?= h(base_url('/admin?tab='.$tab)) ?>" class="btn-admin-secondary" style="font-size:12px;">クリア</a>
    <?php endif; ?>
  </div>
</form>

<?php if ($tab!=='companies'): ?>
<div class="admin-table-wrap">
  <table class="admin-table">
    <thead><tr><th>ID</th><th>会社名</th><th>職種名</th><th>年収</th><th>勤務地</th><th>ステータス</th><th>操作</th></tr></thead>
    <tbody>
      <?php if (empty($jobs)): ?>
        <tr><td colspan="7" class="admin-empty">条件に一致する求人がありません</td></tr>
      <?php else: ?>
        <?php foreach ($jobs as $job): ?>
        <tr>
          <td class="admin-td-id"><?= $job['id'] ?></td>
          <td><a href="<?= h(base_url('/admin?tab=jobs&company_id='.$job['company_id'])) ?>" class="admin-link" style="font-size:12px;"><?= h($job['company_name']) ?></a></td>
          <td><a href="<?= h(base_url('/admin/jobs/'.$job['id'].'/edit')) ?>" class="admin-link"><?= h($job['title']) ?></a></td>
          <td><?= h(salary_label($job['salary_min'],$job['salary_max'])) ?></td>
          <td style="font-size:12px;"><?= h($job['location']) ?></td>
          <td><?php $badgeValue=$job['status'];$badgeLabel=$job['status']==='active'?'公開中':'非公開';$badgeType='status';require BASE_PATH.'/app/Views/snippets/status_badge.php'; ?></td>
          <td class="admin-td-actions">
            <a href="<?= h(base_url('/admin/jobs/'.$job['id'].'/edit')) ?>" class="btn-admin-sm btn-admin-sm--edit">編集</a>
            <form method="post" action="<?= h(base_url('/admin/jobs/'.$job['id'].'/toggle')) ?>" style="display:inline;">
<?php require BASE_PATH . '/app/Views/snippets/csrf.php'; ?>
              <button type="submit" class="btn-admin-sm btn-admin-sm--toggle"><?= $job['status']==='active'?'非公開':'公開' ?></button>
            </form>
            <form method="post" action="<?= h(base_url('/admin/jobs/'.$job['id'].'/delete')) ?>" style="display:inline;" onsubmit="return confirm('削除しますか？')">
<?php require BASE_PATH . '/app/Views/snippets/csrf.php'; ?>
              <button type="submit" class="btn-admin-sm btn-admin-sm--delete">削除</button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>
<?php else: ?>
<div class="admin-table-wrap">
  <table class="admin-table">
    <thead><tr><th>ID</th><th>会社名</th><th>業界</th><th>従業員数</th><th>求人数</th><th>操作</th></tr></thead>
    <tbody>
      <?php if (empty($companies)): ?>
        <tr><td colspan="6" class="admin-empty">条件に一致する会社がありません</td></tr>
      <?php else: ?>
        <?php foreach ($companies as $co): ?>
        <tr>
          <td class="admin-td-id"><?= $co['id'] ?></td>
          <td><p style="font-weight:600;font-size:13px;color:var(--navy);"><?= h($co['name']) ?></p><?php if($co['website']): ?><a href="<?= h($co['website']) ?>" target="_blank" class="admin-link" style="font-size:11px;"><?= h($co['website']) ?></a><?php endif; ?></td>
          <td style="font-size:12px;"><?= h($co['industry']??'—') ?></td>
          <td style="font-size:12px;"><?= h($co['employees']??'—') ?></td>
          <td><a href="<?= h(base_url('/admin?tab=jobs&company_id='.$co['id'])) ?>" class="admin-link" style="font-size:13px;font-weight:600;"><?= (int)$co['job_count'] ?>件</a></td>
          <td>
            <a href="<?= h(base_url('/admin/companies/'.$co['id'].'/edit')) ?>" class="btn-admin-sm btn-admin-sm--edit">編集</a>
            <a href="<?= h(base_url('/admin?tab=jobs&company_id='.$co['id'])) ?>" class="btn-admin-sm btn-admin-sm--toggle">求人を見る</a>
          </td>
        </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>
<?php endif; ?>
<script>
function setFilter(name,value){var f=document.getElementById('search-form');var i=f.querySelector('[name="'+name+'"]');if(i){i.value=value;}else{var h=document.createElement('input');h.type='hidden';h.name=name;h.value=value;f.appendChild(h);}f.submit();}
</script>
