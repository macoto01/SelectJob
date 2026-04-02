# JobNext - PHP MVC 転職サイト

## ディレクトリ構成

```
jobnext/
├── .docker/                # Docker設定の詳細（Dockerfileなど）を格納
│   ├── php/
│   │   └── Dockerfile
│   └── nginx/
│       └── default.conf
├── app/
│   ├── Controllers/
│   │   └── JobController.php     # ルートハンドラ・ロジック振り分け
│   ├── Models/
│   │   ├── JobModel.php          # 求人DB操作
│   │   └── CompanyModel.php      # 会社DB操作
│   └── Views/
│       ├── jobs/
│       │   ├── index.php         # 求人一覧ページ
│       │   └── show.php          # 求人詳細ページ
│       └── shared/
│           ├── header.php        # 共通ヘッダー・ヘルパー関数
│           ├── footer.php        # 共通フッター
│           └── 404.php           # 404ページ
├── config/
│   └── database.php              # DB接続設定 (PDO)
├── database/
│   └── schema.sql                # テーブル定義 + サンプルデータ
├── public/                       # ← Webサーバーのドキュメントルートに設定
│   ├── index.php                 # エントリーポイント
│   ├── .htaccess                 # URLリライト (Apache)
│   ├── css/
│   │   └── style.css
│   └── js/
│       └── main.js
├── docker-compose.yml       # Docker全体の構成図
├── .env                     # ローカル開発用の環境変数
└── routes/
    └── web.php                   # シンプルルーター
```

## セットアップ手順

### 1. データベース作成

```bash
mysql -u root -p < database/schema.sql
```

### 2. DB接続情報の設定

環境変数で設定（推奨）:

```bash
export DB_HOST=localhost
export DB_PORT=3306
export DB_NAME=jobnext
export DB_USER=your_user
export DB_PASS=your_password
```

または `config/database.php` の define() のデフォルト値を直接編集。

### 3. Webサーバー設定

**Apache** の場合、`public/` をドキュメントルートに設定し、`mod_rewrite` を有効化:

```apache
<VirtualHost *:80>
    ServerName jobnext.local
    DocumentRoot /path/to/jobnext/public
    <Directory /path/to/jobnext/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

**PHP ビルトインサーバー** で素早く確認する場合:

```bash
cd jobnext/public
php -S localhost:8000
# → http://localhost:8000 でアクセス
```

## URL ルーティング

| Method | URL          | 処理                   |
|--------|--------------|------------------------|
| GET    | `/`          | 求人一覧               |
| GET    | `/jobs`      | 求人一覧（検索・ソート）|
| GET    | `/jobs/{id}` | 求人詳細               |
| POST   | `/jobs/apply`| 応募処理（PRGパターン）|

## 主な機能

- **求人一覧**: キーワード・勤務地・リモート可・フレックスでフィルタリング、年収/追加日でソート
- **求人詳細**: 求人情報タブ・会社情報タブの切り替え、URL コピー
- **会社名リンク**: 求人名に詳細ページへのリンクを設定
- **応募機能**: セッションで応募済み状態を管理（本番はDBの `applications` テーブルを使用）
- **前後ナビ**: 詳細ページで前の求人・次の求人へ移動

## DB テーブル

| テーブル       | 用途             |
|----------------|------------------|
| `companies`    | 会社情報         |
| `jobs`         | 求人情報         |
| `job_tags`     | 求人のポイントタグ|
| `users`        | ユーザー（応募者）|
| `applications` | 応募履歴         |

## 本番化の際の追加実装

1. `users` テーブルを使ったログイン認証（セッション管理）
2. `applications` テーブルへの応募データ永続化
3. CSRF トークン（フォーム送信保護）
4. ページネーション（`LIMIT / OFFSET`）
5. 管理画面（求人の CRUD）
