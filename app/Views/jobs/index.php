<!-- app/Views/jobs/index.php -->

<!-- ======= サイドバー + メインレイアウト ======= -->
<div class="layout">

  <!-- ===== サイドバー ===== -->
  <aside class="sidebar">
    <div class="logo">
      <div class="logo-text">SelectJob</div>
      <div class="logo-sub">転職管理マイページ</div>
    </div>

    <nav class="nav">
      <a href="<?= h(base_url('/')) ?>" class="nav-item active">
        <svg class="nav-icon" viewBox="0 0 16 16" fill="currentColor"><path d="M2 2h12a1 1 0 011 1v10a1 1 0 01-1 1H2a1 1 0 01-1-1V3a1 1 0 011-1zm0 3v8h12V5H2zm0-2v1h12V3H2z"/></svg>
        Home
      </a>
      <a href="<?= h(base_url('/jobs')) ?>" class="nav-item">
        <svg class="nav-icon" viewBox="0 0 16 16" fill="currentColor"><path d="M11 8a3 3 0 100-6 3 3 0 000 6zm-7.5 6a6 6 0 0113 0H3.5z"/></svg>
        求人検索
      </a>
      <a href="#" class="nav-item">
        <svg class="nav-icon" viewBox="0 0 16 16" fill="currentColor"><path d="M1 3h14v2H1V3zm0 4h14v2H1V7zm0 4h10v2H1v-2z"/></svg>
        選考中の求人
      </a>
      <a href="#" class="nav-item">
        <svg class="nav-icon" viewBox="0 0 16 16" fill="currentColor"><path d="M8 1l2.5 5 5.5.8-4 3.9.9 5.5L8 13.6 3.1 16.2l.9-5.5L0 6.8l5.5-.8z"/></svg>
        ご提案求人
      </a>
      <a href="#" class="nav-item">
        <svg class="nav-icon" viewBox="0 0 16 16" fill="currentColor"><path d="M4 1h8a1 1 0 011 1v12a1 1 0 01-1 1H4a1 1 0 01-1-1V2a1 1 0 011-1zm0 1v12h8V2H4zm1 2h6v1H5V4zm0 3h6v1H5V7zm0 3h4v1H5v-1z"/></svg>
        履歴書・職務経歴書
      </a>
      <a href="#" class="nav-item">
        <svg class="nav-icon" viewBox="0 0 16 16" fill="currentColor"><path d="M8 1.5a6.5 6.5 0 100 13 6.5 6.5 0 000-13zM0 8a8 8 0 1116 0A8 8 0 010 8zm9-1v4H7V7h2zm0-3v2H7V4h2z"/></svg>
        面接攻略マニュアル
      </a>
    </nav>

    <div class="sidebar-promo">
      <p class="promo-title">友達紹介キャンペーン</p>
      <p>最大 <strong>¥20,000</strong><br>Amazonギフト券プレゼント！</p>
    </div>
  </aside>

  <!-- ===== メインコンテンツ ===== -->
  <div class="main">

    <!-- トップバー -->
    <div class="topbar">
      <div class="topbar-icons">
        <span class="topbar-icon" title="ヘルプ">?</span>
        <span class="topbar-icon" title="メッセージ">&#9993;</span>
        <span class="topbar-icon" title="設定">&#9881;</span>
        <span class="topbar-icon" title="通知">&#128276;</span>
      </div>
      <div class="topbar-user">
        <div class="topbar-user-info">
          <span class="topbar-name">山田 太郎</span>
          <span class="topbar-email">taro.yamada@example.com</span>
        </div>
        <div class="avatar">山</div>
      </div>
    </div>

    <div class="content">

      <!-- 検索フォーム -->
      <form method="get" action="<?= h(base_url('/jobs')) ?>" class="search-form">
        <input type="text" name="keyword"  value="<?= h($filters['keyword']) ?>"  placeholder="キーワード（職種・会社名）" class="search-input">
        <input type="text" name="location" value="<?= h($filters['location']) ?>" placeholder="勤務地" class="search-input search-input--sm">
        <label class="check-label"><input type="checkbox" name="remote" value="1" <?= $filters['remote'] === '1' ? 'checked' : '' ?>> リモート可</label>
        <label class="check-label"><input type="checkbox" name="flex"   value="1" <?= $filters['flex']   === '1' ? 'checked' : '' ?>> フレックス</label>
        <button type="submit" class="btn-search">検索</button>
      </form>

      <!-- 件数・ソート -->
      <div class="jobs-header">
        <div>
          <span class="jobs-count-num"><?= $total ?>件</span>
          <span class="jobs-count-label">求人があります</span>
        </div>
        <div class="jobs-controls">
          <form method="get" action="<?= h(base_url('/jobs')) ?>" id="sort-form">
            <?php foreach ($filters as $k => $v): if ($k === 'sort') continue; ?>
              <input type="hidden" name="<?= h($k) ?>" value="<?= h($v) ?>">
            <?php endforeach; ?>
            <select name="sort" class="sort-select" onchange="document.getElementById('sort-form').submit()">
              <option value="created_at DESC" <?= $filters['sort'] === 'created_at DESC' ? 'selected' : '' ?>>追加日が新しい順</option>
              <option value="salary_max DESC" <?= $filters['sort'] === 'salary_max DESC' ? 'selected' : '' ?>>年収が高い順</option>
              <option value="created_at ASC"  <?= $filters['sort'] === 'created_at ASC'  ? 'selected' : '' ?>>追加日が古い順</option>
            </select>
          </form>
        </div>
      </div>

      <!-- 求人テーブル -->
      <div class="jobs-table">
        <div class="table-header">
          <div class="th">募集名</div>
          <div class="th">年収</div>
          <div class="th">勤務地</div>
          <div class="th th--right">操作</div>
        </div>

        <?php if (empty($jobs)): ?>
          <div class="empty-state">
            <p>条件に一致する求人が見つかりませんでした。</p>
          </div>
        <?php else: ?>
          <?php foreach ($jobs as $job): ?>
          <div class="job-row">
            <div class="job-info">
              <div class="job-tags">
                <?php if ($job['is_new']): ?>
                  <span class="tag tag--new">NEW</span>
                <?php endif; ?>
                <?php if ($job['flex_time']): ?>
                  <span class="tag tag--flex">フレックスあり</span>
                <?php endif; ?>
                <?php if ($job['remote_work']): ?>
                  <span class="tag tag--remote">リモートワーク可</span>
                <?php endif; ?>
              </div>
              <!-- 求人名にリンクを設定 -->
              <div class="job-company">
                <a href="<?= h(base_url('/jobs/' . $job['id'])) ?>" class="job-link">
                  <?= h($job['company_name']) ?>
                </a>
              </div>
              <div class="job-title"><?= h($job['title']) ?></div>
              <?php if (!empty($job['tags'])): ?>
              <div class="job-point-tags">
                <?php foreach ($job['tags'] as $tag): ?>
                  <span class="point-tag"><?= h($tag) ?></span>
                <?php endforeach; ?>
              </div>
              <?php endif; ?>
            </div>

            <div class="job-salary"><?= h(salary_label($job['salary_min'], $job['salary_max'])) ?></div>

            <div class="job-location"><?= h($job['location']) ?></div>

            <div class="job-actions">
              <a href="<?= h(base_url('/jobs/' . $job['id'])) ?>" class="btn-detail">詳細を見る</a>
              <form method="post" action="<?= h(base_url('/jobs/apply')) ?>" style="display:inline;">
                <input type="hidden" name="job_id" value="<?= (int)$job['id'] ?>">
                <?php if ($this_applied = (in_array($job['id'], $_SESSION['applied'] ?? []))): ?>
                  <button type="button" class="btn-applied" disabled>応募済み</button>
                <?php else: ?>
                  <button type="submit" class="btn-apply" onclick="return confirm('この求人に応募しますか？')">応募</button>
                <?php endif; ?>
              </form>
            </div>
          </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div><!-- /jobs-table -->

    </div><!-- /content -->
  </div><!-- /main -->
</div><!-- /layout -->
