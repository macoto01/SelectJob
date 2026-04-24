<?php
$badgeType  = $badgeType  ?? 'status';
$badgeValue = $badgeValue ?? '';
$badgeLabel = $badgeLabel ?? '';
$classMap = ['status'=>'admin-status admin-status--','role'=>'user-role-badge user-role-badge--','overall'=>'scr-overall-badge scr-overall--','step'=>'scr-step-pill scr-step-pill--'];
$class = ($classMap[$badgeType] ?? 'admin-status admin-status--') . h($badgeValue);
?><span class="<?= $class ?>"><?= h($badgeLabel) ?></span>
