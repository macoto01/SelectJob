<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Resume (English)</title>
<style>
  @media print { .no-print { display:none; } @page { margin: 15mm; } }
  body { font-family: 'Georgia', 'Times New Roman', serif; font-size: 11pt; color: #111; margin: 0; padding: 20px; }
  h1 { font-size: 20pt; text-align: center; margin-bottom: 4px; color: #1a2b4a; }
  .subtitle { text-align:center; font-size:11pt; color:#555; margin-bottom: 16px; }
  h2 { font-size: 13pt; color: #1a2b4a; border-bottom: 1.5px solid #3a7bd5; padding-bottom: 4px; margin: 22px 0 10px; text-transform: uppercase; letter-spacing: 0.5px; }
  h3 { font-size: 12pt; margin: 0 0 2px; }
  .period { font-size: 10pt; color: #555; float: right; }
  .meta { font-size: 10pt; color: #666; margin: 2px 0 6px; }
  .career-block { margin-bottom: 18px; }
  .desc { white-space: pre-wrap; font-size: 10.5pt; }
  table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
  td { padding: 4px 8px; vertical-align: top; font-size: 10.5pt; }
  .bullet::before { content: "• "; }
  .print-btn { position: fixed; top: 16px; right: 16px; background: #3a7bd5; color: #fff; border: none; padding: 10px 20px; border-radius: 6px; font-size: 13px; cursor: pointer; font-family: sans-serif; }
  .clearfix::after { content:''; display:block; clear:both; }
</style>
</head>
<body>
<?php
// ヘルパー関数は app/Helpers/helpers.php から自動ロード済み

function ym_en(?int $y, ?int $m, bool $cur=false): string {
    if ($cur) return 'Present';
    if (!$y) return '—';
    $months = ['','Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    return ($m ? $months[$m] . ' ' : '') . $y;
}
$b = $basic;
$enName = trim(($b['first_name_en']??'') . ' ' . ($b['last_name_en']??''));
if (!$enName) $enName = trim(($b['first_name']??'') . ' ' . ($b['last_name']??''));
?>
<button class="print-btn no-print" onclick="window.print()">Print / Save PDF</button>

<h1><?= h($enName) ?></h1>
<p class="subtitle"><?= h($b['phone']??'') ?></p>

<h2>Professional Summary</h2>
<p><?= h($skill['summary']??'') ?></p>

<h2>Skills</h2>
<table>
  <?php if ($skill['tech_skills']??''): ?><tr><td style="width:160px;font-weight:600;">Technical Skills</td><td><?= h($skill['tech_skills']??'') ?></td></tr><?php endif; ?>
  <?php if ($skill['business_skills']??''): ?><tr><td style="font-weight:600;">Business Skills</td><td><?= h($skill['business_skills']??'') ?></td></tr><?php endif; ?>
  <?php if ($skill['tools']??''): ?><tr><td style="font-weight:600;">Tools</td><td><?= h($skill['tools']??'') ?></td></tr><?php endif; ?>
</table>

<h2>Work Experience</h2>
<?php foreach ($careers as $c): ?>
<div class="career-block clearfix">
  <span class="period"><?= h(ym_en($c['start_year'],$c['start_month'])) ?> – <?= h(ym_en($c['end_year'],$c['end_month'],$c['is_current'])) ?></span>
  <h3><?= h($c['company_name']) ?></h3>
  <p class="meta"><?= h($c['position']??'') ?><?= $c['department'] ? ' | ' . h($c['department']) : '' ?><?= $c['industry'] ? ' | ' . h($c['industry']) : '' ?></p>
  <p class="desc"><?= h($c['description']??'') ?></p>
</div>
<?php endforeach; ?>

<h2>Education</h2>
<?php foreach ($educations as $e): ?>
<div class="career-block clearfix">
  <span class="period"><?= h(ym_en($e['start_year'],$e['start_month'])) ?> – <?= h(ym_en($e['end_year'],$e['end_month'],$e['is_current'])) ?></span>
  <h3><?= h($e['school_name']) ?></h3>
  <p class="meta"><?= h($e['faculty']??'') ?><?= $e['degree'] ? ' | ' . h($e['degree']) : '' ?></p>
</div>
<?php endforeach; ?>

<h2>Languages &amp; Qualifications</h2>
<table>
  <?php foreach ($languages as $l): ?>
  <tr><td class="bullet" style="width:200px;"><?= h($l['language']) ?> – <?= h($l['level']??'') ?></td><td><?= $l['cert_name'] ? h($l['cert_name']) . ' ' . h($l['cert_score']??'') : '' ?></td></tr>
  <?php endforeach; ?>
  <?php foreach ($qualifications as $q): ?>
  <tr><td class="bullet"><?= h($q['name']) ?></td><td><?= h(ym_en($q['acquired_year']??null,$q['acquired_month']??null)) ?><?= $q['issuer'] ? ' | ' . h($q['issuer']) : '' ?></td></tr>
  <?php endforeach; ?>
</table>

<?php if (!empty($awards)): ?>
<h2>Awards &amp; Honors</h2>
<table>
  <?php foreach ($awards as $a): ?>
  <tr><td class="bullet" style="width:200px;"><?= h($a['title']) ?></td><td><?= h(ym_en($a['award_year']??null,$a['award_month']??null)) ?><?= $a['organization'] ? ' | ' . h($a['organization']) : '' ?></td></tr>
  <?php endforeach; ?>
</table>
<?php endif; ?>
</body>
</html>
