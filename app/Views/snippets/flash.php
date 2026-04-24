<?php if (!empty($flash['message'])): ?>
  <div class="flash flash--<?= h($flash['type']) ?>"><?= h($flash['message']) ?></div>
<?php endif; ?>
