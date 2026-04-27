// ─── 設定テーマ・背景の適用（全ページ共通） ─────────────
(function() {
  try {
    var settings = JSON.parse(localStorage.getItem('SelectJob_settings')) || {};
    var root = document.documentElement;
    if (settings.theme) root.dataset.theme = settings.theme;
    if (settings.bg)    root.dataset.bg    = settings.bg;
    var fontMap  = { small:'12px', medium:'13px', large:'15px' };
    var widthMap = { narrow:'180px', normal:'200px', wide:'240px' };
    if (settings.fontSize)    root.style.fontSize = fontMap[settings.fontSize] || '13px';
    if (settings.sidebarWidth) root.style.setProperty('--sidebar-w', widthMap[settings.sidebarWidth] || '200px');
  } catch(e) {}
})();

// public/js/main.js

// ── タブ切り替え（.hc-tab） ─────────────────────────────────
document.querySelectorAll('.hc-tab').forEach(function(tab) {
  tab.addEventListener('click', function() {
    var target = this.dataset.hctab;
    document.querySelectorAll('.hc-tab').forEach(function(t) { t.classList.remove('hc-tab--active'); });
    document.querySelectorAll('.hc-panel').forEach(function(p) { p.style.display = 'none'; });
    this.classList.add('hc-tab--active');
    var panel = document.getElementById('hc-' + target);
    if (panel) panel.style.display = 'block';
  });
});

// ── 郵便番号自動入力（zipcloud API） ───────────────────────
(function() {
  var zipInput  = document.getElementById('zip');
  var searchBtn = document.getElementById('zip-search-btn');
  var msgEl     = document.getElementById('zip-msg');
  if (!zipInput || !searchBtn) return;

  function normalize(zip) {
    return zip.replace(/[０-９]/g, function(c) { return String.fromCharCode(c.charCodeAt(0) - 0xFEE0); }).replace(/[^\d]/g, '');
  }
  function setField(id, value) {
    var el = document.getElementById(id);
    if (el && el.value === '') el.value = value;
  }
  function showMsg(text, isError) {
    if (!msgEl) return;
    msgEl.textContent = text;
    msgEl.style.display = text ? 'block' : 'none';
    msgEl.style.color = isError ? '#e74c3c' : '#2ecc71';
  }

  async function lookupZip() {
    var zip = normalize(zipInput.value);
    if (zip.length !== 7) { showMsg('郵便番号は7桁で入力してください', true); return; }
    searchBtn.disabled = true;
    searchBtn.textContent = '検索中...';
    showMsg('', false);
    try {
      var res  = await fetch('https://zipcloud.ibsnet.co.jp/api/search?zipcode=' + zip);
      var data = await res.json();
      if (data.status !== 200 || !data.results) { showMsg('該当する住所が見つかりませんでした', true); return; }
      var r = data.results[0];
      setField('address_pref',   r.address1);
      setField('address_city',   r.address2);
      setField('address_street', r.address3);
      zipInput.value = zip.slice(0, 3) + '-' + zip.slice(3);
      showMsg('住所を自動入力しました。番地・建物名を追記してください。(元から情報が入っていると更新されません)', false);
    } catch(e) {
      showMsg('通信エラーが発生しました。手動で入力してください。', true);
    } finally {
      searchBtn.disabled = false;
      searchBtn.textContent = '住所を自動入力';
    }
  }

  searchBtn.addEventListener('click', lookupZip);
  zipInput.addEventListener('keydown', function(e) { if (e.key === 'Enter') { e.preventDefault(); lookupZip(); } });
  zipInput.addEventListener('input', function() { if (normalize(this.value).length === 7) lookupZip(); });
})();
