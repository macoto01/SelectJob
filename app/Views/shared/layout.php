<?php
$activeNav = $activeNav ?? 'home';
$navItems  = require BASE_PATH . '/config/nav.php';
$me        = auth_user();
$isAdmin   = auth_check_role('admin');
$chatUnreadCount = 0;
if ($me && class_exists('ChatModel')) {
    $cm = new ChatModel();
    $chatUnreadCount = $isAdmin ? $cm->getUnreadCountForAdmin() : $cm->getUnreadCountForUser($me['id']);
}
?>
<div class="layout">
  <aside class="sidebar<?= $isAdmin ? ' sidebar--admin' : '' ?>">
    <div class="logo">
      <a href="<?= h(base_url('/')) ?>" style="text-decoration:none;">
        <div class="logo-text">JobNext</div>
        <div class="logo-sub"><?= $isAdmin ? '管理者ページ' : '転職管理マイページ' ?></div>
      </a>
    </div>
    <nav class="nav">
      <?php foreach ($navItems as $key => $item): ?>
        <?php
          if (!empty($item['role'])) {
              if ($item['role']==='admin' && !$isAdmin) continue;
              if ($item['role']==='user'  &&  $isAdmin) continue;
          }
          $unread = (!empty($item['show_unread'])) ? $chatUnreadCount : 0;
        ?>
        <a href="<?= h(base_url($item['href'])) ?>"
           class="nav-item<?= $activeNav===$key?' active':'' ?>">
          <?= icon($item['icon']) ?>
          <?= h($item['label']) ?>
          <?php if ($unread > 0): ?><span class="nav-unread"><?= $unread>99?'99+':$unread ?></span><?php endif; ?>
        </a>
      <?php endforeach; ?>
    </nav>

    <!-- 設定リンク -->
    <div class="sidebar-settings-link">
      <a href="<?= h(base_url('/settings')) ?>"
         class="nav-item<?= ($activeNav==='settings')?' active':'' ?>">
        <?= icon('settings') ?>
        設定
      </a>
    </div>

    <div class="sidebar-footer">
      <?php if ($me): ?>
        <div class="sidebar-user">
          <div class="sidebar-user-info">
            <p class="sidebar-user-name"><?= h($me['name']) ?></p>
            <p class="sidebar-user-role"><?= $isAdmin?'管理者':'一般ユーザー' ?></p>
          </div>
        </div>
        <a href="<?= h(base_url('/logout')) ?>" class="sidebar-logout-btn" onclick="return confirm('ログアウトしますか？')">
          <?= icon('logout', 'sidebar-logout-icon', 14) ?>
          ログアウト
        </a>
      <?php endif; ?>
    </div>
    <?php if (!$isAdmin): ?>
    <div class="sidebar-promo">
      <p class="promo-title">友達紹介キャンペーン</p>
      <p>最大 <strong>¥20,000</strong><br>Amazonギフト券プレゼント！</p>
    </div>
    <?php endif; ?>
  </aside>
  
  <div class="main">
    <div class="content <?= h($contentClass ?? '') ?>">
      <?= $viewContent ?>
    </div>
  </div>
</div>
