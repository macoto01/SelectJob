<?php
$activeNav   = 'admin_chat';
$pageTitle   = 'チャット管理';
$headerRight = $totalUnread>0?'<span class="chat-admin-unread-total">'.$totalUnread.'件の未読メッセージ</span>':'';
require BASE_PATH . '/app/Views/snippets/page_header.php';
?>
<div class="admin-table-wrap">
  <table class="admin-table">
    <thead><tr><th>ユーザー</th><th>最新メッセージ</th><th>最終更新</th><th>未読</th><th>操作</th></tr></thead>
    <tbody>
      <?php if (empty($rooms)): ?>
        <tr><td colspan="5" class="admin-empty">まだチャットの履歴がありません。</td></tr>
      <?php else: ?>
        <?php foreach ($rooms as $room): ?>
        <tr>
          <td><?php $avatarName=$room['user_name'];$avatarEmail=$room['user_email'];require BASE_PATH.'/app/Views/snippets/user_avatar_cell.php'; ?></td>
          <td class="chat-last-msg"><?= $room['last_message']?h(mb_substr($room['last_message'],0,40)).(mb_strlen($room['last_message'])>40?'…':''):'<span style="color:var(--text-muted)">メッセージなし</span>' ?></td>
          <td class="chat-last-at"><?= $room['last_message_at']?date('m/d H:i',strtotime($room['last_message_at'])):'—' ?></td>
          <td><?php if($room['unread_count']>0):?><span class="chat-unread-badge"><?= (int)$room['unread_count'] ?></span><?php else:?><span style="color:var(--text-muted);font-size:12px;">—</span><?php endif;?></td>
          <td><a href="<?= h(base_url('/admin/chat/'.$room['room_id'])) ?>" class="btn-admin-sm btn-admin-sm--edit">チャットを開く</a></td>
        </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>
