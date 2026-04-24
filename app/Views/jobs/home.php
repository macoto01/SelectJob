<?php $activeNav = 'home'; ?>

<?php require BASE_PATH . '/app/Views/snippets/home_dashboard.php'; ?>

<div class="screening-section">
  <div class="screening-header">
    <h2 class="screening-title">
      選考中の求人
      <?php if (!empty($screenings)): ?>
        <span class="screening-count"><?= count($screenings) ?>件</span>
      <?php endif; ?>
    </h2>
    <a href="<?= h(base_url('/jobs')) ?>" class="btn-screening-search">求人を探す</a>
  </div>

  <?php if (empty($screenings)): ?>
    <div class="screening-empty">
      <p>応募中の求人はありません。</p>
      <a href="<?= h(base_url('/jobs')) ?>" class="btn-primary" style="margin-top:12px;font-size:13px;padding:8px 20px;">求人を探す</a>
    </div>
  <?php else: ?>
    <div class="screening-filter-row">
      <label class="screening-filter-check"><input type="checkbox" id="filter-ended"> 選考終了済みも表示</label>
    </div>
    <div class="screening-table-wrap">
      <table class="screening-table">
        <thead>
          <tr>
            <th class="scr-th-job">求人案件</th>
            <?php foreach ($stepNames as $i => $name): if ($i > 3) continue; ?>
              <th class="scr-th-step"><?= h($name) ?></th>
            <?php endforeach; ?>
            <th class="scr-th-step">内定</th>
            <th class="scr-th-date">内定承諾日</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($screenings as $s): ?>
            <?php $isEnded = in_array($s['overall_status'],['offered','rejected','withdrawn']); ?>
            <tr class="scr-row<?= $isEnded?' scr-row--ended':'' ?>" data-ended="<?= $isEnded?'1':'0' ?>" style="<?= $isEnded?'display:none;':'' ?>">
              <td class="scr-td-job">
                <a href="<?= h(base_url('/screening/'.$s['id'])) ?>" class="scr-job-link">
                  <?= h($s['company_name']) ?>/<?= h($s['job_title']) ?>
                  <svg width="12" height="12" viewBox="0 0 16 16" fill="currentColor" style="margin-left:3px;opacity:.5;"><path d="M6 3h7v7h-1V4.7L4.7 12 4 11.3 11.3 4H6V3z"/></svg>
                </a>
                <div class="scr-overall-badge scr-overall--<?= h($s['overall_status']) ?>"><?= h($overallLabels[$s['overall_status']]??'') ?></div>
              </td>
              <?php for ($step=0;$step<=3;$step++): $st=$s['steps'][$step]??null; ?>
                <td class="scr-td-step">
                  <?php if ($st&&$st['scheduled_at']&&in_array($st['step_status'],['scheduled','passed','failed'])): ?>
                    <a href="<?= h(base_url('/screening/'.$s['id'])) ?>" class="scr-date-link"><?= date('Y/m/d(D)',strtotime($st['scheduled_at'])) ?><br><?= date('H:i',strtotime($st['scheduled_at'])) ?></a>
                  <?php endif; ?>
                  <?php if ($st&&$st['meet_url']&&in_array($st['step_status'],['scheduled','passed'])): ?>
                    <a href="<?= h($st['meet_url']) ?>" target="_blank" rel="noopener" class="scr-meet-btn"><svg width="12" height="12" viewBox="0 0 16 16" fill="currentColor"><path d="M1 3h9v7H1V3zm10 1l4-2v8l-4-2V4z"/></svg>Meet</a>
                  <?php endif; ?>
                  <?php if ($st): ?>
                    <div class="scr-step-status scr-step--<?= h($st['step_status']) ?>"><?= h(lang('result_status.' . $st['step_status'])) ?></div>
                  <?php else: ?><span class="scr-step-empty">—</span><?php endif; ?>
                </td>
              <?php endfor; ?>
              <td class="scr-td-step">
                <?php if ($s['overall_status']==='offered'): ?>
                  <div class="scr-offered-badge"><svg width="14" height="14" viewBox="0 0 16 16" fill="none"><circle cx="8" cy="8" r="7" stroke="#2ecc71" stroke-width="1.5"/><path d="M5 8l2 2 4-4" stroke="#2ecc71" stroke-width="1.5" stroke-linecap="round"/></svg>内定</div>
                <?php elseif ($s['overall_status']==='rejected'): ?>
                  <span class="scr-step-status scr-step--failed">不合格</span>
                <?php elseif ($s['overall_status']==='withdrawn'): ?>
                  <span class="scr-step-status scr-step--cancelled">辞退</span>
                <?php else: ?><span class="scr-step-empty">—</span><?php endif; ?>
              </td>
              <td class="scr-td-date">
                <?php $offered=$s['steps'][4]??null; ?>
                <?php if ($offered&&$offered['result_at']&&$s['overall_status']==='offered'): ?>
                  <span class="scr-accept-date"><?= date('Y/m/d(D)',strtotime($offered['result_at'])) ?><br><?= date('H:i',strtotime($offered['result_at'])) ?></span>
                <?php else: ?><span class="scr-step-empty">—</span><?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<script>
(function(){
  var cb=document.getElementById('filter-ended');
  var rows=document.querySelectorAll('.scr-row');
  if(cb) cb.addEventListener('change',function(){
    rows.forEach(function(r){ if(r.dataset.ended==='1') r.style.display=cb.checked?'':'none'; });
  });
})();
</script>
