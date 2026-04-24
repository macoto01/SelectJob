<?php
$activeNav = 'resume';
require BASE_PATH . '/app/Views/snippets/flash.php';
$sectionLabels = ['basic'=>'基本情報','desired'=>'希望条件','skill'=>'スキル','career'=>'職務経歴','education'=>'学歴','award'=>'表彰','language'=>'語学','qualification'=>'資格'];
?>
<div class="resume-pdf-bar">
  <a href="<?= h(base_url('/resume/pdf?type=resume')) ?>" class="btn-pdf" target="_blank">履歴書PDF</a>
  <a href="<?= h(base_url('/resume/pdf?type=career')) ?>" class="btn-pdf" target="_blank">職務経歴書PDF</a>
  <a href="<?= h(base_url('/resume/pdf?type=career_en')) ?>" class="btn-pdf" target="_blank">英文PDF</a>
</div>
<div class="resume-layout">
  <nav class="resume-nav">
    <?php foreach ($sectionLabels as $key => $label): ?>
      <a href="<?= h(base_url('/resume?section='.$key)) ?>" class="resume-nav-item<?= $section===$key?' active':'' ?>"><?= h($label) ?></a>
    <?php endforeach; ?>
  </nav>
  <div class="resume-body">
    <div class="resume-section-header">
      <h2 class="resume-section-title"><?= h($sectionLabels[$section]??'') ?></h2>
      <a href="<?= h(base_url('/resume/edit?section='.$section)) ?>" class="btn-edit">編集</a>
    </div>

    <?php if ($section==='basic'): ?>
      <table class="resume-table">
        <?php $fullName=trim(($basic['last_name']??'').' '.($basic['first_name']??'')); $kana=trim(($basic['last_name_kana']??'').' '.($basic['first_name_kana']??'')); ?>
        <tr><th>氏名</th><td><?= h($fullName) ?>（<?= h($kana) ?>）<span class="lock-badge">🔒 非公開</span></td></tr>
        <tr><th>性別</th><td><?= h($basic['gender']??'') ?></td></tr>
        <tr><th>生年月日</th><td><?= h($basic['birthdate']??'') ?><span class="lock-badge">🔒 非公開</span></td></tr>
        <tr><th>電話番号</th><td><?= h($basic['phone']??'') ?><span class="lock-badge">🔒 非公開</span></td></tr>
        <tr><th>住所</th><td><?= h(implode(' ',array_filter([$basic['address_pref']??'',$basic['address_city']??'',$basic['address_street']??'',$basic['address_building']??'']))) ?><span class="lock-badge">🔒 非公開</span></td></tr>
        <tr><th>現在の年収</th><td><?= $basic['salary']??'' ?><?= ($basic['salary']??'')?'万円':'' ?></td></tr>
      </table>

    <?php elseif ($section==='desired'): ?>
      <table class="resume-table">
        <tr><th>希望職種</th><td><?= h($desired['desired_job']??'') ?></td></tr>
        <tr><th>希望勤務地</th><td><?= h($desired['desired_location']??'') ?></td></tr>
        <tr><th>希望年収</th><td><?= h(salary_label($desired['desired_salary_min']??null,$desired['desired_salary_max']??null)) ?></td></tr>
        <tr><th>入社可能時期</th><td><?= h($desired['desired_start']??'') ?></td></tr>
        <tr><th>転職理由</th><td><?= nl2br(h($desired['change_reason']??'')) ?></td></tr>
        <tr><th>自己PR</th><td><?= nl2br(h($desired['appeal']??'')) ?></td></tr>
      </table>

    <?php elseif ($section==='skill'): ?>
      <table class="resume-table">
        <tr><th>ITスキル</th><td><?= nl2br(h($skill['it_skills']??'')) ?></td></tr>
        <tr><th>語学</th><td><?= nl2br(h($skill['languages']??'')) ?></td></tr>
        <tr><th>資格・免許</th><td><?= nl2br(h($skill['certifications']??'')) ?></td></tr>
        <tr><th>その他スキル</th><td><?= nl2br(h($skill['other_skills']??'')) ?></td></tr>
      </table>

    <?php elseif ($section==='career'): ?>
      <?php if (empty($careers)): ?>
        <p class="resume-empty">職務経歴が登録されていません。</p>
      <?php else: ?>
        <?php foreach ($careers as $c): ?>
        <div class="career-card">
          <div class="career-card-header">
            <span class="career-period"><?= ym($c['start_year']??null,$c['start_month']??null) ?> 〜 <?= ym($c['end_year']??null,$c['end_month']??null,$c['is_current']??false) ?></span>
            <?php if ($c['is_current']): ?><span class="badge-current">現在</span><?php endif; ?>
          </div>
          <p class="career-company"><?= h($c['company_name']??'') ?></p>
          <?php if ($c['employment_type']??''): ?><p class="career-meta"><?= h($c['employment_type']) ?></p><?php endif; ?>
          <?php if ($c['position']??''): ?><p class="career-pos"><?= h($c['position']) ?></p><?php endif; ?>
          <?php if ($c['description']??''): ?><p class="career-desc"><?= nl2br(h($c['description'])) ?></p><?php endif; ?>
        </div>
        <?php endforeach; ?>
      <?php endif; ?>
      <div class="resume-actions"><a href="<?= h(base_url('/resume/edit?section=career')) ?>" class="btn-add">＋ 追加</a></div>

    <?php elseif ($section==='education'): ?>
      <?php if (empty($educations)): ?><p class="resume-empty">学歴が登録されていません。</p>
      <?php else: foreach($educations as $e): ?>
        <div class="career-card">
          <div class="career-card-header"><span class="career-period"><?= ym($e['start_year']??null,$e['start_month']??null) ?> 〜 <?= ym($e['end_year']??null,$e['end_month']??null,$e['is_current']??false) ?></span></div>
          <p class="career-company"><?= h($e['school_name']??'') ?></p>
          <?php if ($e['faculty']??''): ?><p class="career-meta"><?= h($e['faculty']??'') ?> <?= h($e['major']??'') ?></p><?php endif; ?>
          <?php if ($e['degree']??''): ?><p class="career-pos"><?= h($e['degree']) ?></p><?php endif; ?>
        </div>
      <?php endforeach; endif; ?>
      <div class="resume-actions"><a href="<?= h(base_url('/resume/edit?section=education')) ?>" class="btn-add">＋ 追加</a></div>

    <?php elseif ($section==='award'): ?>
      <?php if (empty($awards)): ?><p class="resume-empty">表彰履歴が登録されていません。</p>
      <?php else: foreach($awards as $a): ?>
        <div class="career-card">
          <p class="career-company"><?= h($a['title']??'') ?></p>
          <p class="career-meta"><?= h($a['organization']??'') ?> <?= h($a['award_date']??'') ?></p>
          <?php if ($a['description']??''): ?><p class="career-desc"><?= nl2br(h($a['description'])) ?></p><?php endif; ?>
        </div>
      <?php endforeach; endif; ?>
      <div class="resume-actions"><a href="<?= h(base_url('/resume/edit?section=award')) ?>" class="btn-add">＋ 追加</a></div>

    <?php elseif ($section==='language'): ?>
      <?php if (empty($languages)): ?><p class="resume-empty">語学情報が登録されていません。</p>
      <?php else: ?><table class="resume-table"><tr><th>言語</th><th>レベル</th><th>試験名</th><th>スコア</th></tr>
        <?php foreach($languages as $l): ?><tr><td><?= h($l['language']??'') ?></td><td><?= h($l['level']??'') ?></td><td><?= h($l['test_name']??'') ?></td><td><?= h($l['test_score']??'') ?></td></tr><?php endforeach; ?>
      </table><?php endif; ?>
      <div class="resume-actions"><a href="<?= h(base_url('/resume/edit?section=language')) ?>" class="btn-add">＋ 追加</a></div>

    <?php elseif ($section==='qualification'): ?>
      <?php if (empty($qualifications)): ?><p class="resume-empty">資格が登録されていません。</p>
      <?php else: ?><table class="resume-table"><tr><th>資格名</th><th>取得日</th><th>取得予定</th></tr>
        <?php foreach($qualifications as $q): ?><tr><td><?= h($q['name']??'') ?></td><td><?= h(ym($q['acquired_year']??null, $q['acquired_month']??null)) ?></td><td><?= $q['in_progress']??0?'✓':'' ?></td></tr><?php endforeach; ?>
      </table><?php endif; ?>
      <div class="resume-actions"><a href="<?= h(base_url('/resume/edit?section=qualification')) ?>" class="btn-add">＋ 追加</a></div>
    <?php endif; ?>

  </div>
</div>
