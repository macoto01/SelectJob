<style>
.login-wrap{min-height:100vh;display:flex;align-items:center;justify-content:center;background:var(--gray-bg);}
.login-card{background:var(--white);border:0.5px solid var(--border);border-radius:14px;padding:40px 44px;width:100%;max-width:420px;}
.login-logo{font-size:22px;font-weight:700;color:var(--navy);margin-bottom:4px;}
.login-sub{font-size:12px;color:var(--text-muted);margin-bottom:32px;}
.login-label{display:block;font-size:12px;font-weight:600;color:var(--navy);margin-bottom:6px;}
.login-input{width:100%;padding:10px 14px;border:0.5px solid var(--border);border-radius:7px;font-size:14px;font-family:inherit;outline:none;transition:border-color .15s;margin-bottom:18px;}
.login-input:focus{border-color:var(--blue);}
.login-btn{width:100%;padding:11px;background:var(--navy);color:#fff;border:none;border-radius:7px;font-size:14px;font-weight:700;font-family:inherit;cursor:pointer;transition:background .15s;margin-top:4px;}
.login-btn:hover{background:var(--navy-mid);}
.login-flash{padding:10px 14px;border-radius:7px;font-size:13px;margin-bottom:20px;border:1px solid transparent;}
.login-flash--error{background:#fef2f2;color:#b91c1c;border-color:#fca5a5;}
.login-flash--success{background:#f0fdf4;color:#166534;border-color:#86efac;}
.login-demo{margin-top:28px;padding-top:20px;border-top:0.5px solid var(--border);}
.login-demo p{font-size:11px;color:var(--text-muted);margin-bottom:8px;}
.login-demo-item{font-size:12px;background:var(--gray-bg);border-radius:6px;padding:8px 12px;margin-bottom:6px;}
.login-demo-item strong{color:var(--navy);}
</style>
<div class="login-wrap">
  <div class="login-card">
    <div class="login-logo">JobNext</div>
    <div class="login-sub">転職管理マイページ</div>
    <?php if (!empty($flash['message'])): ?>
      <div class="login-flash login-flash--<?= h($flash['type']) ?>">
        <?= h($flash['message']) ?>
        <?php if ($flash['type'] === 'error' && str_contains($flash['message'], 'ロック')): ?>
          <p style="margin-top:6px;font-size:11px;opacity:.8;">しばらく時間をおいてから再度お試しください。</p>
        <?php endif; ?>
      </div>
    <?php endif; ?>
    <form method="post" action="<?= h(base_url('/login')) ?>">
      <?php require BASE_PATH . '/app/Views/snippets/csrf.php'; ?>
      <label class="login-label" for="email">メールアドレス</label>
      <input class="login-input" type="email" id="email" name="email" value="<?= h($_POST['email']??'') ?>" placeholder="example@jobnext.jp" required autofocus>
      <label class="login-label" for="password">パスワード</label>
      <input class="login-input" type="password" id="password" name="password" placeholder="パスワードを入力" required>
      <button type="submit" class="login-btn">ログイン</button>
    </form>
    <div style="text-align:right;margin-top:8px;margin-bottom:4px;">
      <a href="<?= h(base_url('/forgot-password')) ?>" style="font-size:12px;color:var(--blue);">パスワードをお忘れの方はこちら</a>
    </div>
    <div class="login-demo">
      <p>デモアカウント（パスワード: <strong>password</strong>）</p>
      <div class="login-demo-item"><strong>一般ユーザー</strong>：kiryu@example.com</div>
      <div class="login-demo-item"><strong>管理者</strong>：admin@jobnext.jp</div>
    </div>
  </div>
</div>
