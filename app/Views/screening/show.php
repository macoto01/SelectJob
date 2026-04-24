<?php
$activeNav = auth_check_role('admin') ? 'admin_screenings' : 'screening';
$me        = auth_user();
$isAdmin   = auth_check_role('admin');
?>
<?php require BASE_PATH . '/app/Views/snippets/flash.php'; ?>

<div class="admin-header">
  <div style="display:flex;align-items:center;gap:12px;">
    <a href="<?= h(base_url($isAdmin?'/admin/screenings':'/')) ?>" class="btn-back">← 戻る</a>
    <div>
      <h1 class="admin-title" style="margin:0;"><?= h($screening['company_name']) ?></h1>
      <p style="font-size:13px;color:var(--text-muted);margin:2px 0 0;"><?= h($screening['job_title']) ?></p>
    </div>
  </div>
  <div style="display:flex;gap:8px;align-items:center;">
    <?php if ($isAdmin): ?>
      <span style="font-size:12px;color:var(--text-muted);">対象: <?= h($screening['user_name']) ?></span>
      <form method="post" action="<?= h(base_url('/screening/'.$screening['id'].'/status')) ?>" style="display:flex;gap:6px;align-items:center;">
        <?php require BASE_PATH . '/app/Views/snippets/csrf.php'; ?>
        <select name="overall_status" class="admin-select admin-select--sm" onchange="this.form.submit()">
          <?php foreach ($overallLabels as $val=>$label): ?>
            <option value="<?= h($val) ?>" <?= $screening['overall_status']===$val?'selected':'' ?>><?= h($label) ?></option>
          <?php endforeach; ?>
        </select>
      </form>
    <?php else: ?>
      <?php $badgeValue=$screening['overall_status']; $badgeLabel=$overallLabels[$screening['overall_status']]??''; $badgeType='overall'; require BASE_PATH.'/app/Views/snippets/status_badge.php'; ?>
    <?php endif; ?>
  </div>
</div>

<div class="scr-steps-wrap">
  <?php for ($step=0;$step<=4;$step++):
    $st=$screening['steps'][$step]??null;
    $stepName = $stepNames[$step] ?? ($step === 4 ? '内定' : 'ステップ ' . ($step + 1));
    $status=$st['step_status']??'pending';
    $isCurrent=(int)$screening['current_step']===$step;
  ?>
  <div class="scr-step-card scr-step-card--<?= $status ?><?= $isCurrent?' scr-step-card--current':'' ?>">
    <div class="scr-step-card-header">
      <span class="scr-step-badge"><?= $step===4?'★':($step+1) ?></span>
      <span class="scr-step-card-name"><?= h($stepName) ?></span>
      <span class="scr-step-pill scr-step-pill--<?= $status ?>"><?= h($statusLabels[$status]??'未実施') ?></span>
    </div>
    <div class="scr-step-card-body">
      <?php if ($st&&$st['scheduled_at']): ?>
        <div class="scr-info-row"><svg width="13" height="13" viewBox="0 0 16 16" fill="currentColor"><path d="M3 1h10v2H3V1zm-1 3h12v10H2V4zm2 2v6h8V6H4zm1 1h2v2H5V7zm4 0h2v2H9V7zm-4 3h2v2H5v-2zm4 0h2v2H9v-2z"/></svg><?= date('Y年m月d日(D) H:i',strtotime($st['scheduled_at'])) ?></div>
      <?php endif; ?>
      <?php if ($st&&$st['meet_url']): ?>
        <div class="scr-info-row"><svg width="13" height="13" viewBox="0 0 16 16" fill="currentColor"><path d="M1 3h9v7H1V3zm10 1l4-2v8l-4-2V4z"/></svg><a href="<?= h($st['meet_url']) ?>" target="_blank" rel="noopener" class="scr-meet-link">Google Meetに参加</a></div>
      <?php endif; ?>
      <?php if ($st&&$st['location_note']): ?>
        <div class="scr-info-row"><svg width="13" height="13" viewBox="0 0 16 16" fill="currentColor"><path d="M8 1a4 4 0 100 8A4 4 0 008 1zm0 1a3 3 0 110 6A3 3 0 018 2zm0 8c-3.3 0-6 1.3-6 3v1h12v-1c0-1.7-2.7-3-6-3z"/></svg><?= h($st['location_note']) ?></div>
      <?php endif; ?>
      <?php if (!$st||(!$st['scheduled_at']&&!$st['meet_url']&&!$st['location_note'])): ?>
        <p style="font-size:11px;color:var(--text-muted);padding:4px 0;">未実施</p>
      <?php endif; ?>
    </div>
    <?php if ($isAdmin): ?>
    <details class="scr-step-edit">
      <summary class="scr-step-edit-btn">このステップを更新</summary>
      <form method="post" action="<?= h(base_url('/screening/'.$screening['id'].'/step')) ?>" class="scr-step-form">
        <?php require BASE_PATH . '/app/Views/snippets/csrf.php'; ?>
        <input type="hidden" name="step" value="<?= $step ?>">
        <div class="scr-form-row">
          <label class="scr-form-label">ステータス</label>
          <select name="step_status" class="admin-select admin-select--sm">
            <?php foreach ($statusLabels as $val=>$label): ?>
              <option value="<?= h($val) ?>" <?= $status===$val?'selected':'' ?>><?= h($label) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <?php if ($step>0): ?>
        <div class="scr-form-row">
          <label class="scr-form-label">面接日時</label>
          <input type="datetime-local" name="scheduled_at" value="<?= $st&&$st['scheduled_at']?date('Y-m-d\TH:i',strtotime($st['scheduled_at'])):'' ?>" class="admin-input admin-input--sm">
        </div>
        <div class="scr-form-row">
          <label class="scr-form-label">Google Meet URL</label>
          <div style="display:flex;gap:8px;align-items:center;flex:1;">
            <input type="url" name="meet_url" value="<?= h($st['meet_url']??'') ?>" placeholder="https://meet.google.com/xxx-xxxx-xxx" class="admin-input">
            <a href="https://meet.google.com/new" target="_blank" rel="noopener" class="btn-meet-create"><svg width="14" height="14" viewBox="0 0 16 16" fill="currentColor"><path d="M1 3h9v7H1V3zm10 1l4-2v8l-4-2V4z"/></svg>新規作成</a>
          </div>
        </div>
        <div class="scr-form-row">
          <label class="scr-form-label">場所・形式</label>
          <input type="text" name="location_note" value="<?= h($st['location_note']??'') ?>" placeholder="例: オンライン / 東京オフィス" class="admin-input">
        </div>
        <?php endif; ?>
        <div style="display:flex;justify-content:flex-end;margin-top:10px;">
          <button type="submit" class="btn-admin-submit" style="padding:7px 20px;font-size:12px;">更新する</button>
        </div>
      </form>
    </details>
    <?php endif; ?>
  </div>
  <?php endfor; ?>
</div>

<div id="feedbacks" class="scr-feedback-section">
  <h2 class="scr-feedback-title">フィードバック</h2>
  <form method="post" action="<?= h(base_url('/screening/'.$screening['id'].'/feedback')) ?>" class="scr-feedback-form">
<?php require BASE_PATH . '/app/Views/snippets/csrf.php'; ?>
    <div class="scr-feedback-form-row">
      <select name="step" class="admin-select admin-select--sm">
        <option value="">全体へのコメント</option>
        <?php foreach ($stepNames as $i=>$name): ?><option value="<?= $i ?>"><?= h($name) ?>へのフィードバック</option><?php endforeach; ?>
      </select>
    </div>
    <textarea name="body" rows="3" class="admin-textarea" placeholder="<?= $isAdmin?'アドバイザーとしてフィードバックを入力...':'面接の感想・気になった点などを入力...' ?>" required></textarea>
    <div style="display:flex;justify-content:flex-end;margin-top:8px;">
      <button type="submit" class="btn-admin-submit" style="padding:8px 24px;">投稿する</button>
    </div>
  </form>
  <h2 class="scr-feedback-title">フィードバックリスト</h2>
  <div class="scr-feedback-list">
    <?php if (empty($screening['feedbacks'])): ?>
      <p style="text-align:center;color:var(--text-muted);font-size:13px;padding:24px 0;">まだフィードバックはありません。</p>
    <?php else: ?>
      <?php foreach ($screening['feedbacks'] as $fb): ?>
        <?php $isMyFeedback=((int)$fb['author_id']===(int)$me['id']); ?>
        <div class="scr-feedback-item scr-feedback-item--<?= h($fb['author_role']) ?>">
          <div class="scr-feedback-meta">
            <div class="scr-feedback-author">
              <span class="scr-author-badge scr-author-badge--<?= h($fb['author_role']) ?>"><?= $fb['author_role']==='admin'?'アドバイザー':'ユーザー' ?></span>
              <span class="scr-author-name"><?= h($fb['author_name']) ?></span>
              <?php if ($fb['step']!==null): ?><span class="scr-feedback-step">[<?= h($stepNames[(int)$fb['step']]??'') ?>]</span><?php endif; ?>
            </div>
            <div style="display:flex;align-items:center;gap:8px;">
              <span class="scr-feedback-time"><?= date('Y/m/d H:i',strtotime($fb['created_at'])) ?></span>
              <?php if ($isMyFeedback): ?>
                <form method="post" action="<?= h(base_url('/screening/feedback/'.$fb['id'].'/delete')) ?>" onsubmit="return confirm('削除しますか？')">
                  <?php require BASE_PATH . '/app/Views/snippets/csrf.php'; ?>
                  <input type="hidden" name="screening_id" value="<?= (int)$screening['id'] ?>">
                  <button type="submit" class="scr-feedback-delete">削除</button>
                </form>
              <?php endif; ?>
            </div>
          </div>
          <p class="scr-feedback-body"><?= nl2br(h($fb['body'])) ?></p>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>
