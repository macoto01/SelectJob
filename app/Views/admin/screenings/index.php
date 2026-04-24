<?php
$activeNav   = 'admin_screenings';
$pageTitle   = '選考管理';
$headerRight = '<span class="admin-stat-chip">'.count($screenings).'件</span>';
require BASE_PATH . '/app/Views/snippets/page_header.php';
require BASE_PATH . '/app/Views/snippets/flash.php';
?>
<div class="admin-table-wrap">
  <table class="admin-table">
    <thead>
      <tr><th>ユーザー</th><th>企業 / 職種</th><th>現在のステップ</th><th>ステータス</th><th>最終更新</th><th>操作</th></tr>
    </thead>
    <tbody>
      <?php if (empty($screenings)): ?>
        <tr><td colspan="6" class="admin-empty">選考データがありません</td></tr>
      <?php else: ?>
        <?php foreach ($screenings as $s): ?>
        <tr>
          <td><?php $avatarName=$s['user_name']; $avatarSize=28; require BASE_PATH.'/app/Views/snippets/user_avatar_cell.php'; ?></td>
          <td>
            <p style="font-weight:600;font-size:13px;color:var(--navy);"><?= h($s['company_name']) ?></p>
            <p style="font-size:11px;color:var(--text-muted);"><?= h($s['job_title']) ?></p>
          </td>
          <td><?php $badgeValue='scheduled';$badgeLabel=$stepNames[$s['current_step']]??'書類選考';$badgeType='step';require BASE_PATH.'/app/Views/snippets/status_badge.php'; ?></td>
          <td><?php $badgeValue=$s['overall_status'];$badgeLabel=$overallLabels[$s['overall_status']]??'';$badgeType='overall';require BASE_PATH.'/app/Views/snippets/status_badge.php'; ?></td>
          <td style="font-size:12px;color:var(--text-muted);"><?= date('Y/m/d H:i',strtotime($s['updated_at'])) ?></td>
          <td><a href="<?= h(base_url('/screening/'.$s['id'])) ?>" class="btn-admin-sm btn-admin-sm--edit">詳細・管理</a></td>
        </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>
