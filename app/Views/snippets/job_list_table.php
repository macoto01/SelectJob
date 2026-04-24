<div class="jobs-header">
  <div><span class="jobs-count-num"><?= $total ?>件</span><span class="jobs-count-label">求人があります</span></div>
  <select class="sort-select" onchange="location.href='<?= h(base_url('/jobs?')) ?>'+new URLSearchParams({...Object.fromEntries(new URLSearchParams(location.search)),sort:this.value})">
    <option value="created_at DESC" <?= ($filters['sort']??'')==='created_at DESC'?'selected':'' ?>>新着順</option>
    <option value="salary_max DESC" <?= ($filters['sort']??'')==='salary_max DESC'?'selected':'' ?>>年収順</option>
    <option value="created_at ASC"  <?= ($filters['sort']??'')==='created_at ASC' ?'selected':'' ?>>古い順</option>
  </select>
</div>
<div class="jobs-table">
  <div class="table-header">
    <div class="th">求人情報</div><div class="th">年収</div><div class="th">勤務地</div><div class="th th--right">操作</div>
  </div>
  <?php if (empty($jobs)): ?>
    <div class="empty-state">条件に一致する求人が見つかりませんでした。</div>
  <?php else: ?>
    <?php foreach ($jobs as $job): ?>
    <div class="job-row">
      <div>
        <div class="job-tags">
          <?php if ($job['is_new']): ?><span class="tag tag--new">NEW</span><?php endif; ?>
          <?php if ($job['remote_work']): ?><span class="tag tag--remote">リモート可</span><?php endif; ?>
          <?php if ($job['flex_time']): ?><span class="tag tag--flex">フレックス</span><?php endif; ?>
        </div>
        <a href="<?= h(base_url('/jobs/'.$job['id'])) ?>" class="job-link"><?= h($job['company_name']) ?></a>
        <div class="job-title"><?= h($job['title']) ?></div>
        <div class="job-point-tags">
          <?php foreach (array_slice($job['tags'],0,4) as $tag): ?>
            <span class="point-tag"><?= h($tag) ?></span>
          <?php endforeach; ?>
        </div>
      </div>
      <div class="job-salary"><?= h(salary_label($job['salary_min'],$job['salary_max'])) ?></div>
      <div class="job-location"><?= h($job['location']) ?></div>
      <div class="job-actions">
        <a href="<?= h(base_url('/jobs/'.$job['id'])) ?>" class="btn-detail">詳細を見る</a>
      </div>
    </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>
