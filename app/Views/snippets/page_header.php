<?php $backLabel = $backLabel ?? '← 戻る'; ?>
<div class="page-header">
  <div class="page-header__left">
    <?php if (!empty($backUrl)): ?>
      <a href="<?= h($backUrl) ?>" class="btn-back"><?= h($backLabel) ?></a>
    <?php endif; ?>
    <?php if (!empty($pageTitle)): ?>
      <div>
        <h1 class="page-header__title"><?= h($pageTitle) ?></h1>
        <?php if (!empty($pageSubtitle)): ?>
          <p class="page-header__subtitle"><?= h($pageSubtitle) ?></p>
        <?php endif; ?>
      </div>
    <?php endif; ?>
  </div>
  <?php if (!empty($headerRight)): ?>
    <div class="page-header__right"><?= $headerRight ?></div>
  <?php endif; ?>
</div>
