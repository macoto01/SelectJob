// public/js/main.js

// タブ切り替え（詳細ページ）
document.querySelectorAll('.tab').forEach(function(btn) {
  btn.addEventListener('click', function() {
    var tabName = this.dataset.tab;
    document.querySelectorAll('.tab').forEach(function(t) { t.classList.remove('active'); });
    document.querySelectorAll('.tab-content').forEach(function(c) { c.style.display = 'none'; });
    this.classList.add('active');
    var target = document.getElementById('tab-' + tabName);
    if (target) target.style.display = 'block';
  });
});
