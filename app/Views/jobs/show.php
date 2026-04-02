<!-- app/Views/jobs/show.php -->

<div class="layout">

  <!-- サイドバー -->
  <aside class="sidebar">
    <div class="logo">
      <div class="logo-text">JobNext</div>
      <div class="logo-sub">転職管理マイページ</div>
    </div>
    <nav class="nav">
      <a href="<?= h(base_url('/')) ?>" class="nav-item">
        <svg class="nav-icon" viewBox="0 0 16 16" fill="currentColor"><path d="M2 2h12a1 1 0 011 1v10a1 1 0 01-1 1H2a1 1 0 01-1-1V3a1 1 0 011-1zm0 3v8h12V5H2zm0-2v1h12V3H2z"/></svg>
        Home
      </a>
      <a href="<?= h(base_url('/jobs')) ?>" class="nav-item active">
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
    </nav>
    <div class="sidebar-promo">
      <p class="promo-title">友達紹介キャンペーン</p>
      <p>最大 <strong>¥20,000</strong><br>Amazonギフト券プレゼント！</p>
    </div>
  </aside>

  <!-- メイン -->
  <div class="main">

    <!-- トップバー -->
    <div class="topbar">
      <div class="topbar-icons">
        <span class="topbar-icon">?</span>
        <span class="topbar-icon">&#9993;</span>
        <span class="topbar-icon">&#9881;</span>
        <span class="topbar-icon">&#128276;</span>
      </div>
      <div class="topbar-user">
        <div class="topbar-user-info">
          <span class="topbar-name">山田 太郎</span>
          <span class="topbar-email">taro.yamada@example.com</span>
        </div>
        <div class="avatar">山</div>
      </div>
    </div>

    <div class="content content--detail">

      <!-- ===== 詳細ヘッダー ===== -->
      <div class="detail-header">
        <button class="btn-close" onclick="history.back()" title="閉じる">&#10005;</button>

        <div class="detail-title-block">
          <!-- 会社名にリンクを設定（参考画像の要件） -->
          <h1 class="detail-company">
            <a href="<?= h($job['website'] ?? '#') ?>" target="_blank" rel="noopener" class="company-link">
              <?= h($job['company_name']) ?>
            </a>
          </h1>
          <p class="detail-job-title"><?= h($job['title']) ?></p>
        </div>

        <div class="detail-header-right">
          <?php if ($isApplied): ?>
            <span class="applied-badge">
              <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><circle cx="8" cy="8" r="7" stroke="#2ecc71" stroke-width="1.5"/><path d="M5 8l2 2 4-4" stroke="#2ecc71" stroke-width="1.5" stroke-linecap="round"/></svg>
              応募済み
            </span>
          <?php else: ?>
            <form method="post" action="<?= h(base_url('/jobs/apply')) ?>">
              <input type="hidden" name="job_id" value="<?= (int)$job['id'] ?>">
              <button type="submit" class="btn-apply btn-apply--lg" onclick="return confirm('この求人に応募しますか？')">応募する</button>
            </form>
          <?php endif; ?>
          <p class="detail-note">応募した求人は書類選考に進むと「選考中の求人」に表示されます</p>
        </div>
      </div>

      <!-- タブ -->
      <div class="detail-tabs">
        <button class="tab active" data-tab="job">求人情報</button>
        <button class="tab" data-tab="company">会社情報</button>
        <div class="tab-actions">
          <a href="<?= h($job['website'] ?? '#') ?>" target="_blank" rel="noopener" class="tab-action-link">
            <svg width="13" height="13" viewBox="0 0 16 16" fill="currentColor"><path d="M8 1a7 7 0 100 14A7 7 0 008 1zm0 1.5a5.5 5.5 0 110 11 5.5 5.5 0 010-11zm1 2.5v1.5H7V5h2zm0 3v5H7V8h2z"/></svg>
            別タブで開く
          </a>
          <button class="tab-action-link" onclick="navigator.clipboard.writeText(location.href).then(()=>alert('URLをコピーしました'))">
            <svg width="13" height="13" viewBox="0 0 16 16" fill="currentColor"><path d="M4 4V2a1 1 0 011-1h8a1 1 0 011 1v10a1 1 0 01-1 1h-2v2a1 1 0 01-1 1H3a1 1 0 01-1-1V5a1 1 0 011-1h1zm1 0h5a1 1 0 011 1v7h1V2H5v2zM3 5v9h7V5H3z"/></svg>
            URLをコピーする
          </button>
        </div>
      </div>

      <!-- ===== 求人情報タブ ===== -->
      <div id="tab-job" class="tab-content active">
        <table class="detail-table">
          <tr>
            <th>職種</th>
            <td><?= h($job['title']) ?></td>
          </tr>

          <tr>
            <th>求人のポイント</th>
            <td>
              <div class="point-tags-detail">
                <?php foreach ($job['tags'] as $tag): ?>
                  <span class="point-tag-detail"><?= h($tag) ?></span>
                <?php endforeach; ?>
              </div>
            </td>
          </tr>

          <tr>
            <th>職務内容</th>
            <td class="detail-body">
              <?= nl2br(h($job['work_description'])) ?>
            </td>
          </tr>

          <tr>
            <th>職務内容の変更の範囲</th>
            <td><?= h($job['job_change_scope'] ?? '会社の定める業務') ?></td>
          </tr>

          <tr>
            <th>必要な経験/資格</th>
            <td class="detail-body">
              <?= nl2br(h($job['requirements'] ?? '')) ?>
            </td>
          </tr>

          <?php if (!empty($job['preferred'])): ?>
          <tr>
            <th>歓迎スキル</th>
            <td class="detail-body"><?= nl2br(h($job['preferred'])) ?></td>
          </tr>
          <?php endif; ?>

          <tr>
            <th>年収</th>
            <td><?= h(salary_label($job['salary_min'], $job['salary_max'])) ?></td>
          </tr>

          <tr>
            <th>勤務地</th>
            <td><?= h($job['location']) ?></td>
          </tr>

          <?php if (!empty($job['working_hours'])): ?>
          <tr>
            <th>勤務時間</th>
            <td><?= h($job['working_hours']) ?></td>
          </tr>
          <?php endif; ?>

          <?php if (!empty($job['holiday'])): ?>
          <tr>
            <th>休日</th>
            <td><?= h($job['holiday']) ?></td>
          </tr>
          <?php endif; ?>

          <?php if (!empty($job['benefits'])): ?>
          <tr>
            <th>待遇・福利厚生</th>
            <td class="detail-body"><?= nl2br(h($job['benefits'])) ?></td>
          </tr>
          <?php endif; ?>

          <tr>
            <th>リモートワーク</th>
            <td><?= $job['remote_work'] ? '可' : '不可' ?></td>
          </tr>

          <tr>
            <th>フレックスタイム</th>
            <td><?= $job['flex_time'] ? 'あり' : 'なし' ?></td>
          </tr>
        </table>
      </div>

      <!-- ===== 会社情報タブ ===== -->
      <div id="tab-company" class="tab-content" style="display:none;">
        <table class="detail-table">
          <tr>
            <th>会社名</th>
            <td>
              <a href="<?= h($job['website'] ?? '#') ?>" target="_blank" rel="noopener" class="company-link">
                <?= h($job['company_name']) ?>
              </a>
            </td>
          </tr>
          <?php if (!empty($job['company_description'])): ?>
          <tr>
            <th>会社概要</th>
            <td class="detail-body"><?= nl2br(h($job['company_description'])) ?></td>
          </tr>
          <?php endif; ?>
          <?php if (!empty($job['industry'])): ?>
          <tr><th>業界</th><td><?= h($job['industry']) ?></td></tr>
          <?php endif; ?>
          <?php if (!empty($job['employees'])): ?>
          <tr><th>従業員数</th><td><?= h($job['employees']) ?></td></tr>
          <?php endif; ?>
          <?php if (!empty($job['founded'])): ?>
          <tr><th>設立</th><td><?= h($job['founded']) ?>年</td></tr>
          <?php endif; ?>
          <?php if (!empty($job['address'])): ?>
          <tr><th>所在地</th><td><?= h($job['address']) ?></td></tr>
          <?php endif; ?>
          <?php if (!empty($job['website'])): ?>
          <tr>
            <th>ウェブサイト</th>
            <td><a href="<?= h($job['website']) ?>" target="_blank" rel="noopener" class="company-link"><?= h($job['website']) ?></a></td>
          </tr>
          <?php endif; ?>
        </table>
      </div>

      <!-- 前後ナビ -->
      <div class="detail-nav">
        <a href="<?= h(base_url('/jobs/' . max(1, $job['id'] - 1))) ?>" class="btn-nav">&larr; 前の求人</a>
        <a href="<?= h(base_url('/jobs/' . ($job['id'] + 1))) ?>" class="btn-nav">次の求人 &rarr;</a>
      </div>

    </div><!-- /content -->
  </div><!-- /main -->
</div><!-- /layout -->
