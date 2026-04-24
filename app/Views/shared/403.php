<div style="min-height: calc(100vh - 200px); display: flex; align-items: center; justify-content: center; text-align: center; padding: 20px;">
    <div>
        <h1 style="font-size: 64px; font-weight: 800; color: #e74c3c; margin-bottom: 16px;">403</h1>
        <h2 style="font-size: 24px; font-weight: 700; color: var(--navy); margin-bottom: 12px;"><?= h($title) ?></h2>
        <p style="color: var(--text-muted); margin-bottom: 32px; font-size: 15px;"><?= h($message) ?></p>
        <a href="<?= h(base_url('/')) ?>" class="btn-admin-primary" style="display: inline-flex; align-items: center; padding: 12px 32px; text-decoration: none;">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor" style="margin-right: 8px;"><path d="M8 1L1 8h2v7h10V8h2L8 1z"/></svg>
            <?= h($link) ?>
        </a>
    </div>
</div>