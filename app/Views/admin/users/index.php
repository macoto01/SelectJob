<?php
$activeNav   = 'admin_users';
$pageTitle   = 'ユーザー管理';
$headerRight = '<span class="admin-stat-chip">'.count($users).'名 登録</span>'
             . '<a href="'.h(base_url('/admin/users/create')).'" class="btn-admin-primary">＋ ユーザーを追加</a>';
require BASE_PATH . '/app/Views/snippets/page_header.php';
require BASE_PATH . '/app/Views/snippets/flash.php';
?>
<div class="admin-table-wrap">
  <table class="admin-table">
    <thead>
      <tr><th>ID</th><th>氏名</th><th>メールアドレス</th><th>権限</th><th>ステータス</th><th>最終ログイン</th><th>登録日</th><th>操作</th></tr>
    </thead>
    <tbody>
      <?php if (empty($users)): ?>
        <tr><td colspan="8" class="admin-empty">ユーザーが登録されていません</td></tr>
      <?php else: ?>
        <?php foreach ($users as $u): ?>
        <tr>
          <td class="admin-td-id"><?= $u['id'] ?></td>
          <td><?php $avatarName=$u['name'];$avatarSize=28;require BASE_PATH.'/app/Views/snippets/user_avatar_cell.php'; ?>
            <a href="<?= h(base_url('/admin/users/'.$u['id'])) ?>" class="admin-link" style="font-size:12px;"><?= h($u['name']) ?></a></td>
          <td style="font-size:12px;color:var(--text-muted);"><?= h($u['email']) ?></td>
          <td><?php $badgeValue=$u['role'];$badgeLabel=$u['role']==='admin'?'管理者':'一般';$badgeType='role';require BASE_PATH.'/app/Views/snippets/status_badge.php'; ?></td>
          <td><?php $badgeValue=$u['is_active']?'active':'closed';$badgeLabel=$u['is_active']?'有効':'無効';$badgeType='status';require BASE_PATH.'/app/Views/snippets/status_badge.php'; ?></td>
          <td style="font-size:12px;color:var(--text-muted);"><?= $u['last_login_at']?date('Y/m/d H:i',strtotime($u['last_login_at'])):'—' ?></td>
          <td style="font-size:12px;color:var(--text-muted);"><?= date('Y/m/d',strtotime($u['created_at'])) ?></td>
          <td>
            <a href="<?= h(base_url('/admin/users/'.$u['id'])) ?>" class="btn-admin-sm btn-admin-sm--edit">詳細</a>
            <a href="<?= h(base_url('/admin/users/'.$u['id'].'/edit')) ?>" class="btn-admin-sm btn-admin-sm--toggle">編集</a>
          </td>
        </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>
