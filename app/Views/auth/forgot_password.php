<!-- app/Views/auth/forgot_password.php -->
<style>
.login-wrap{min-height:100vh;display:flex;align-items:center;justify-content:center;background:var(--gray-bg);}
.login-card{background:var(--white);border:0.5px solid var(--border);border-radius:14px;padding:40px 44px;width:100%;max-width:420px;}
.login-logo{font-size:22px;font-weight:700;color:var(--navy);margin-bottom:4px;}
.login-sub{font-size:12px;color:var(--text-muted);margin-bottom:24px;}
.login-desc{font-size:13px;color:var(--text-muted);margin-bottom:24px;line-height:1.7;}
.login-label{display:block;font-size:12px;font-weight:600;color:var(--navy);margin-bottom:6px;}
.login-input{width:100%;padding:10px 14px;border:0.5px solid var(--border);border-radius:7px;font-size:14px;font-family:inherit;outline:none;transition:border-color .15s;margin-bottom:18px;}
.login-input:focus{border-color:var(--blue);}
.login-btn{width:100%;padding:11px;background:var(--navy);color:#fff;border:none;border-radius:7px;font-size:14px;font-weight:700;font-family:inherit;cursor:pointer;transition:background .15s;}
.login-btn:hover{background:var(--navy-mid);}
.login-flash{padding:10px 14px;border-radius:7px;font-size:13px;margin-bottom:20px;border:1px solid transparent;}
.login-flash--error{background:#fef2f2;color:#b91c1c;border-color:#fca5a5;}
.login-flash--success{background:#f0fdf4;color:#166534;border-color:#86efac;}
.login-back{display:block;text-align:center;margin-top:16px;font-size:13px;color:var(--blue);}
</style>
<div class="login-wrap">
  <div class="login-card">
    <div class="login-logo">SelectJob</div>
    <div class="login-sub">パスワードをお忘れの方</div>
    <?php if (!empty($flash['message'])): ?>
      <div class="login-flash login-flash--<?= h($flash['type']) ?>"><?= h($flash['message']) ?></div>
    <?php endif; ?>
    <p class="login-desc">登録されたメールアドレスを入力してください。パスワードリセット用のリンクを送信します。</p>
    <form method="post" action="<?= h(base_url('/forgot-password')) ?>">
<?php require BASE_PATH . '/app/Views/snippets/csrf.php'; ?>
      <label class="login-label" for="email">メールアドレス</label>
      <input class="login-input" type="email" id="email" name="email" placeholder="example@SelectJob.jp" required autofocus>
      <button type="submit" class="login-btn">リセットリンクを送信</button>
    </form>
    <a href="<?= h(base_url('/login')) ?>" class="login-back">← ログインに戻る</a>
  </div>
</div>
