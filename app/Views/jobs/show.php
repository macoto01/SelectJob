<?php
/**
 * app/Views/jobs/show.php
 * 求人詳細ページ
 */
$activeNav   = 'jobs';
$contentClass = 'content--detail';
?>
<div class="detail-header">
  <button class="btn-close" onclick="history.back()">✕</button>
  <div class="detail-title-block">
    <h1 class="detail-company">
      <a href="<?= h($job['company_website']??'#') ?>" target="_blank" class="company-link"><?= h($job['company_name']) ?></a>
    </h1>
    <div class="detail-job-title"><?= h($job['title']) ?></div>
  </div>
  <div class="detail-header-right">
    <?php if ($isApplied): ?>
      <div class="applied-badge">✓ 応募済み</div>
    <?php else: ?>
      <form method="post" action="<?= h(base_url('/jobs/apply')) ?>">
<?php require BASE_PATH . '/app/Views/snippets/csrf.php'; ?>
        <input type="hidden" name="job_id" value="<?= $job['id'] ?>">
        <button type="submit" class="btn-apply btn-apply--lg">この求人に応募する</button>
      </form>
      <p class="detail-note">応募後は選考進行管理から確認できます</p>
    <?php endif; ?>
  </div>
</div>

<div class="detail-tabs">
  <button class="tab active" data-tab="detail">求人情報</button>
  <button class="tab" data-tab="company">会社情報</button>
</div>

<div class="tab-content" id="tab-detail">
  <table class="detail-table">
    <tr><th>職種名</th><td><?= h($job['title']) ?></td></tr>
    <tr><th>雇用形態</th><td><?= h($job['job_type']??'—') ?></td></tr>
    <tr><th>年収</th><td><?= h(salary_label($job['salary_min'],$job['salary_max'])) ?></td></tr>
    <tr><th>勤務地</th><td><?= h($job['location']??'—') ?></td></tr>
    <tr><th>勤務時間</th><td><?= h($job['working_hours']??'—') ?></td></tr>
    <tr><th>休日</th><td><?= h($job['holiday']??'—') ?></td></tr>
    <?php if ($job['work_description']): ?><tr><th>職務内容</th><td class="detail-body"><?= nl2br(h($job['work_description'])) ?></td></tr><?php endif; ?>
    <?php if ($job['requirements']): ?><tr><th>必要な経験/資格</th><td class="detail-body"><?= nl2br(h($job['requirements'])) ?></td></tr><?php endif; ?>
    <?php if ($job['preferred']): ?><tr><th>歓迎スキル</th><td class="detail-body"><?= nl2br(h($job['preferred'])) ?></td></tr><?php endif; ?>
    <?php if ($job['benefits']): ?><tr><th>待遇・福利厚生</th><td class="detail-body"><?= nl2br(h($job['benefits'])) ?></td></tr><?php endif; ?>
    <?php if (!empty($job['tags'])): ?><tr><th>求人のポイント</th><td><div class="point-tags-detail"><?php foreach($job['tags'] as $t):?><span class="point-tag-detail"><?= h($t) ?></span><?php endforeach;?></div></td></tr><?php endif; ?>
  </table>
</div>

<div class="tab-content" id="tab-company" style="display:none;">
  <table class="detail-table">
    <tr><th>会社名</th><td><?= h($job['company_name']) ?></td></tr>
    <?php if ($job['company_desc']??''): ?><tr><th>事業内容</th><td><?= nl2br(h($job['company_desc'])) ?></td></tr><?php endif; ?>
    <?php if ($job['industry']??''): ?><tr><th>業界</th><td><?= h($job['industry']) ?></td></tr><?php endif; ?>
    <?php if ($job['employees']??''): ?><tr><th>従業員数</th><td><?= h($job['employees']) ?></td></tr><?php endif; ?>
    <?php if ($job['founded']??''): ?><tr><th>設立</th><td><?= h($job['founded']) ?>年</td></tr><?php endif; ?>
    <?php if ($job['company_website']??''): ?><tr><th>ウェブサイト</th><td><a href="<?= h($job['company_website']) ?>" target="_blank" class="admin-link"><?= h($job['company_website']) ?></a></td></tr><?php endif; ?>
    <?php if ($job['company_address']??''): ?><tr><th>所在地</th><td><?= h($job['company_address']) ?></td></tr><?php endif; ?>
  </table>
</div>

<script>
document.querySelectorAll('.tab').forEach(function(tab){
  tab.addEventListener('click',function(){
    document.querySelectorAll('.tab').forEach(function(t){t.classList.remove('active');});
    document.querySelectorAll('.tab-content').forEach(function(c){c.style.display='none';});
    this.classList.add('active');
    document.getElementById('tab-'+this.dataset.tab).style.display='block';
  });
});
</script>
