<?php
$avatarName  = $avatarName  ?? '';
$avatarEmail = $avatarEmail ?? '';
$avatarSize  = $avatarSize  ?? 36;
$avatarFs    = $avatarSize <= 28 ? 11 : 14;
?>
<div class="user-avatar-cell">
  <div class="user-avatar" style="width:<?= $avatarSize ?>px;height:<?= $avatarSize ?>px;font-size:<?= $avatarFs ?>px;">
    <?= h(mb_substr($avatarName,0,1)) ?>
  </div>
  <div>
    <p class="user-avatar-cell__name"><?= h($avatarName) ?></p>
    <?php if ($avatarEmail): ?><p class="user-avatar-cell__email"><?= h($avatarEmail) ?></p><?php endif; ?>
  </div>
</div>
