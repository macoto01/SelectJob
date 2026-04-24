<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>履歴書</title>
<style>
  @media print { .no-print { display:none; } @page { margin: 15mm; } }
  body { font-family: 'Hiragino Sans', 'Noto Sans JP', sans-serif; font-size: 11pt; color: #111; margin: 0; padding: 20px; }
  h1 { font-size: 18pt; text-align: center; margin-bottom: 24px; border-bottom: 2px solid #1a2b4a; padding-bottom: 8px; color: #1a2b4a; }
  h2 { font-size: 13pt; color: #1a2b4a; border-left: 4px solid #3a7bd5; padding-left: 8px; margin: 24px 0 10px; }
  table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
  th, td { border: 1px solid #ccc; padding: 7px 10px; vertical-align: top; }
  th { background: #f0f4fb; width: 160px; font-weight: 600; color: #1a2b4a; white-space: nowrap; }
  .print-btn { position: fixed; top: 16px; right: 16px; background: #3a7bd5; color: #fff; border: none; padding: 10px 20px; border-radius: 6px; font-size: 13px; cursor: pointer; }
  .date { text-align: right; color: #555; font-size: 10pt; margin-bottom: 16px; }
</style>
</head>
<body>
<?php
// ヘルパー関数は app/Helpers/helpers.php から自動ロード済み


$b = $basic;
$fullName = trim(($b['last_name']??'') . ' ' . ($b['first_name']??''));
$kana     = trim(($b['last_name_kana']??'') . ' ' . ($b['first_name_kana']??''));
$bd       = $b['birthdate'] ?? '';
$bdStr    = $bd ? date('Y年n月j日', strtotime($bd)) : '';
?>
<button class="print-btn no-print" onclick="window.print()">印刷 / PDF保存</button>
<p class="date">作成日：<?= date('Y年m月d日') ?></p>
<h1>履 歴 書</h1>

<h2>基本情報</h2>
<table>
  <tr><th>氏名</th><td><?= h($fullName) ?>（<?= h($kana) ?>）</td></tr>
  <tr><th>性別</th><td><?= h($b['gender']??'') ?></td></tr>
  <tr><th>生年月日</th><td><?= h($bdStr) ?></td></tr>
  <tr><th>電話番号</th><td><?= h($b['phone']??'') ?></td></tr>
  <tr><th>住所</th><td><?= h(implode(' ', array_filter([$b['address_pref']??'', $b['address_city']??'', $b['address_street']??'', $b['address_building']??'']))) ?></td></tr>
  <tr><th>現在の年収</th><td><?= $b['salary'] ? h($b['salary']).'万円' : '' ?></td></tr>
</table>

<h2>学歴</h2>
<table>
  <tr><th>期間</th><th>学校名</th><th>学部・学科</th><th>学位</th></tr>
  <?php foreach ($educations as $e): ?>
  <tr>
    <td><?= h(ym($e['start_year'],$e['start_month'])) ?> 〜 <?= h(ym($e['end_year'],$e['end_month'],$e['is_current'])) ?></td>
    <td><?= h($e['school_name']) ?></td>
    <td><?= h($e['faculty']??'') ?></td>
    <td><?= h($e['degree']??'') ?></td>
  </tr>
  <?php endforeach; ?>
</table>

<h2>資格</h2>
<table>
  <tr><th>取得年月</th><th>資格名</th><th>発行機関</th></tr>
  <?php foreach ($qualifications as $q): ?>
  <tr>
    <td><?= h(ym($q['acquired_year'] ?? null, $q['acquired_month'] ?? null)) ?></td>
    <td><?= h($q['name']) ?></td>
    <td><?= h($q['issuer']??'') ?></td>
  </tr>
  <?php endforeach; ?>
</table>

<h2>語学</h2>
<table>
  <tr><th>言語</th><th>レベル</th><th>資格・スコア</th></tr>
  <?php foreach ($languages as $l): ?>
  <tr>
    <td><?= h($l['language']) ?></td>
    <td><?= h($l['level']??'') ?></td>
    <td><?= h($l['cert_name']??'') ?> <?= h($l['cert_score']??'') ?></td>
  </tr>
  <?php endforeach; ?>
</table>

<h2>希望条件</h2>
<table>
  <tr><th>希望職種</th><td><?= h($desired['desired_job']??'') ?></td></tr>
  <tr><th>希望勤務地</th><td><?= h($desired['desired_location']??'') ?></td></tr>
  <tr><th>希望年収</th><td><?= h(salary_label($desired['desired_salary_min']??null, $desired['desired_salary_max']??null)) ?></td></tr>
  <tr><th>入社可能時期</th><td><?= h($desired['desired_start']??'') ?></td></tr>
  <tr><th>自己PR</th><td><?= nl2br(h($desired['appeal']??'')) ?></td></tr>
</table>
</body>
</html>
