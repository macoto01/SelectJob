<div class="home-dashboard">
  <div class="home-card-left">
    <div class="hc-tabs">
      <button class="hc-tab" data-hctab="schedule">
        <span class="hc-tab-icon"><svg width="20" height="20" viewBox="0 0 16 16" fill="currentColor"><path d="M3 1h10a1 1 0 011 1v12a1 1 0 01-1 1H3a1 1 0 01-1-1V2a1 1 0 011-1zm0 2v10h10V3H3zm2 2h6v1H5V5zm0 3h4v1H5V8z"/></svg></span>
        <span class="hc-tab-text"><span class="hc-tab-label">面接日程一覧</span><strong class="hc-tab-count">0件</strong></span>
      </button>
      <button class="hc-tab hc-tab--active" data-hctab="tasks">
        <span class="hc-tab-icon"><svg width="20" height="20" viewBox="0 0 16 16" fill="currentColor"><path d="M2 2h12a1 1 0 011 1v10a1 1 0 01-1 1H2a1 1 0 01-1-1V3a1 1 0 011-1zm1 2v8h10V4H3zm2 2h6v1H5V6zm0 3h4v1H5V9z"/></svg></span>
        <span class="hc-tab-text"><span class="hc-tab-label">要対応事項一覧</span><strong class="hc-tab-count">0件</strong></span>
      </button>
    </div>
    <div id="hc-schedule" class="hc-panel" style="display:none;">
      <div class="hc-panel-grid">
        <div><p class="hc-panel-title">本日</p><p class="hc-panel-empty">本日の予定はありません</p></div>
        <div><p class="hc-panel-title">今後の面接日程</p><p class="hc-panel-empty">今後の予定はありません</p></div>
      </div>
    </div>
    <div id="hc-tasks" class="hc-panel">
      <div class="hc-panel-grid">
        <div><p class="hc-panel-title">本日期限の要対応事項</p><p class="hc-panel-empty">本日期限の要対応事項はありません</p></div>
        <div><p class="hc-panel-title">明日以降が期限の要対応事項</p><p class="hc-panel-empty">明日以降が期限の要対応事項はありません</p></div>
      </div>
    </div>
  </div>
  <div class="home-advisor-card">
    <p class="adv-label-top">アドバイザーへ連絡はこちら</p>
    <p class="adv-role">担当アドバイザー</p>
    <div class="adv-main">
      <div class="adv-avatar"><svg width="44" height="44" viewBox="0 0 44 44" fill="none"><circle cx="22" cy="22" r="22" fill="rgba(255,255,255,0.15)"/><circle cx="22" cy="17" r="8" fill="rgba(255,255,255,0.55)"/><ellipse cx="22" cy="38" rx="13" ry="9" fill="rgba(255,255,255,0.35)"/></svg></div>
      <div class="adv-info">
        <p class="adv-name">後藤美穂</p>
        <a href="<?= h(base_url('/chat')) ?>" class="adv-contact-btn" style="display:block;text-align:center;text-decoration:none;">連絡する</a>
      </div>
    </div>
    <div class="adv-meta">
      <div><p class="adv-meta-label">メールアドレス</p><p class="adv-meta-value adv-meta-value--link">miho.goto@SelectJob.co.jp</p></div>
      <div><p class="adv-meta-label">携帯電話番号<br><span style="font-size:9px;opacity:.7;">（平日 10:00〜19:30）</span></p><p class="adv-meta-value adv-meta-value--bold">080-7422-9591</p></div>
    </div>
  </div>
</div>
