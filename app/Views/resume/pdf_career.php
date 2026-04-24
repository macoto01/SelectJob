<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>職務経歴書</title>
<style>
  @media print { .no-print { display:none; } @page { margin: 15mm; } }
  body { font-family: 'Hiragino Sans', 'Noto Sans JP', sans-serif; font-size: 11pt; color: #111; margin: 0; padding: 20px; }
  h1 { font-size: 18pt; text-align: center; margin-bottom: 8px; border-bottom: 2px solid #1a2b4a; padding-bottom: 8px; color: #1a2b4a; }
  h2 { font-size: 13pt; color: #1a2b4a; border-left: 4px solid #3a7bd5; padding-left: 8px; margin: 24px 0 10px; }
  h3 { font-size: 12pt; margin: 0 0 4px; color: #1a2b4a; }
  table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
  th, td { border: 1px solid #ccc; padding: 7px 10px; vertical-align: top; }
  th { background: #f0f4fb; font-weight: 600; color: #1a2b4a; white-space: nowrap; }
  .career-block { border: 1px solid #dde; border-radius: 6px; padding: 14px 16px; margin-bottom: 16px; }
  .career-period { font-size: 10pt; color: #555; margin-bottom: 4px; }
  .career-meta { font-size: 10pt; color: #666; margin-bottom: 6px; }
  .career-desc { white-space: pre-wrap; font-size: 10.5pt; margin-top: 8px; }
  .badge-current { display:inline-block; background:#3a7bd5; color:#fff; font-size:9pt; padding:1px 6px; border-radius:3px; margin-left:6px; }
  .print-btn { position: fixed; top: 16px; right: 16px; background: #3a7bd5; color: #fff; border: none; padding: 10px 20px; border-radius: 6px; font-size: 13px; cursor: pointer; }
  .date { text-align: right; color: #555; font-size: 10pt; margin-bottom: 4px; }
  .name { text-align: right; font-size: 11pt; margin-bottom: 16px; }
</style>
</head>
<body>
<?php
// ヘルパー関数は app/Helpers/helpers.php から自動ロード済み


$b = $basic;
$fullName = trim(($b['last_name']??'') . ' ' . ($b['first_name']??''));
?>
<button class="print-btn no-print" onclick="window.print()">印刷 / PDF保存</button>
<p class="date">作成日：<?= date('Y年m月d日') ?></p>
<p class="name">氏名：<?= h($fullName) ?></p>
<h1>職 務 経 歴 書</h1>

<h2>職務要約</h2>
<p style="white-space:pre-wrap;"><?= h($skill['summary']??'') ?></p>

<h2>スキル</h2>
<table>
  <tr><th style="width:140px;">技術スキル</th><td><?= h($skill['tech_skills']??'') ?></td></tr>
  <tr><th>ビジネススキル</th><td><?= h($skill['business_skills']??'') ?></td></tr>
  <tr><th>使用ツール</th><td><?= h($skill['tools']??'') ?></td></tr>
</table>

<h2>職務経歴</h2>
<?php foreach ($careers as $c): ?>
<div class="career-block">
  <p class="career-period"><?= h(ym($c['start_year'],$c['start_month'])) ?> 〜 <?= h(ym($c['end_year'],$c['end_month'],$c['is_current'])) ?><?php if ($c['is_current']): ?><span class="badge-current">現職</span><?php endif; ?></p>
  <h3><?= h($c['company_name']) ?></h3>
  <p class="career-meta"><?= h($c['industry']??'') ?> | 従業員：<?= h($c['employees']??'') ?> | <?= h($c['department']??'') ?> <?= h($c['position']??'') ?></p>
  <p class="career-desc"><?= h($c['description']??'') ?></p>
</div>
<?php endforeach; ?>

<h2>資格・語学</h2>
<table>
  <tr><th style="width:140px;">取得年月</th><th>資格名</th><th>発行機関</th></tr>
  <?php foreach ($qualifications as $q): ?>
  <tr>
    <td><?= h(ym($q['acquired_year'],$q['acquired_month'])) ?></td>
    <td><?= h($q['name']) ?></td>
    <td><?= h($q['issuer']??'') ?></td>
  </tr>
  <?php endforeach; ?>
  <?php foreach ($languages as $l): ?>
  <tr>
    <td></td>
    <td><?= h($l['language']) ?> <?= h($l['level']??'') ?><?= $l['cert_name'] ? '（' . h($l['cert_name']) . ' ' . h($l['cert_score']??'') . '）' : '' ?></td>
    <td></td>
  </tr>
  <?php endforeach; ?>
</table>

<?php if (!empty($awards)): ?>
<h2>表彰・受賞</h2>
<table>
  <tr><th style="width:100px;">年月</th><th>表彰名</th><th>授与機関</th></tr>
  <?php foreach ($awards as $a): ?>
  <tr>
    <td><?= h(ym($a['award_year'],$a['award_month'])) ?></td>
    <td><?= h($a['title']) ?></td>
    <td><?= h($a['organization']??'') ?></td>
  </tr>
  <?php endforeach; ?>
</table>
<?php endif; ?>
</body>
</html>
