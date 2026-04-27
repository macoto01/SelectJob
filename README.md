# SelectJob - PHP MVC 転職サイト
## ディレクトリ構成

```
SelectJob
├── .devcontainer/               # VSCode 開発コンテナ設定
│   └── devcontainer.json
├── .docker/                     # Docker環境詳細設定
│   ├── nginx/
│   │   └── default.conf         # Nginx サーバー設定
│   └── php/
│       └── Dockerfile           # PHP-FPM イメージビルド定義
├── app/                         # アプリケーションのメインロジック
│   ├── Controllers/             # 各機能のコントローラー（Auth, Admin, Job, etc.）
│   ├── Helpers/
│   │   └── helpers.php          # グローバル関数（h, base_url等）
│   ├── Models/                  # DB操作クラス（JobModel, ChatModel等）
│   └── Views/                   # 画面テンプレート
│       └── shared/
│           ├── layout.php       # 共通レイアウト（サイドバー等）
│           ├── header.php
│           ├── footer.php
│           └── 404.php
├── config/                      # 設定ファイル
│   ├── database.php             # DB接続設定（環境変数読み込み）
│   └── nav.php                  # サイドバーメニュー定義
├── database/                    # SQLスキーマ・データ
│   ├── schema.sql               # テーブル定義
│   └── sample_schema.sql        # 初期サンプルデータ
├── public/                      # Web公開ディレクトリ（ドキュメントルート）
│   ├── index.php                # 全リクエストのエントリーポイント
│   ├── js/
│   │   └── main.js              # フロントエンドJavaScript
│   └── .htaccess                # URLリライト設定（Apache用）
├── routes/
│   └── web.php                  # ルーティング定義と実行ロジック
├── .env                         # 環境変数（Git管理対象外）
├── .gitignore                   # Git除外設定
├── docker-compose.yml           # Dockerコンテナ構成定義
└── vendor/                      # Composer 外部ライブラリ（Git管理対象外）
```

---

## セットアップ

### 1. DB作成

```bash
mysql -u root -p < database/schema.sql
mysql -u root -p < database/resume_schema.sql
mysql -u root -p < database/auth_schema.sql
```

### 2. DB接続設定

```bash
export DB_HOST=localhost
export DB_NAME=SelectJob
export DB_USER=root
export DB_PASS=yourpassword
```

または `config/database.php` の define() のデフォルト値を直接編集。

### 3. 起動

```bash
cd SelectJob/public
php -S localhost:8000
```

---

## よくある修正箇所

| やりたいこと | 編集するファイル |
|---|---|
| サイドバーメニューを追加・変更 | `config/nav.php` |
| 色・フォント・サイズを変更 | `public/css/base.css` |
| レイアウト（サイドバー幅など）を変更 | `public/css/layout.css` |
| 求人一覧・テーブルのデザイン変更 | `public/css/jobs.css` |
| ダッシュボードタブのデザイン変更 | `public/css/dashboard.css` |
| 履歴書ページのデザイン変更 | `public/css/resume.css` |
| ヘルパー関数の追加・修正 | `app/Helpers/helpers.php` |
| URLルートの追加 | `routes/web.php` |
| 共通処理（render/redirect/flash）の変更 | `app/Controllers/BaseController.php` |

---

## URLルーティング

| Method | URL | Controller | Action |
|---|---|---|---|
| GET | `/` | JobController | index |
| GET | `/jobs` | JobController | index |
| GET | `/jobs/{id}` | JobController | show |
| POST | `/jobs/apply` | JobController | apply |
| GET | `/resume` | ResumeController | index |
| GET | `/resume/edit` | ResumeController | edit |
| POST | `/resume/save` | ResumeController | save |
| POST | `/resume/delete` | ResumeController | delete |
| GET | `/resume/pdf` | ResumeController | pdf |
