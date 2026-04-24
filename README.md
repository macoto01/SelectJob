# JobNext - PHP MVC 転職サイト

## ディレクトリ構成

```
jobnext/
├── public/                          ← Webサーバーのドキュメントルート
│   ├── index.php                    エントリーポイント
│   ├── .htaccess                    URLリライト (Apache)
│   ├── css/
│   │   ├── style.css                ★ エントリー（@import でまとめるだけ）
│   │   ├── base.css                 リセット・CSS変数・body
│   │   ├── layout.css               .layout / サイドバー / トップバー / .content
│   │   ├── jobs.css                 求人検索・一覧テーブル
│   │   ├── dashboard.css            Homeダッシュボード（タブ・アドバイザー）
│   │   ├── resume.css               履歴書ページ（閲覧・編集フォーム）
│   │   └── utilities.css            汎用ボタン・レスポンシブ
│   └── js/
│       └── main.js                  タブ切り替えなど UI スクリプト
│
├── routes/
│   └── web.php                      ルーティングテーブル（URLパターン定義）
│
├── config/
│   ├── database.php                 DB接続設定（PDO）
│   └── nav.php                      ★ サイドバーナビの定義（追加・順序変更はここ）
│
├── app/
│   ├── Helpers/
│   │   └── helpers.php              ★ h() / base_url() / salary_label() / ym() を一元管理
│   │
│   ├── Controllers/
│   │   ├── BaseController.php       render / redirect / flash / input を共通化
│   │   ├── JobController.php        求人一覧・詳細・応募
│   │   └── ResumeController.php     履歴書 閲覧・編集・保存・削除・PDF
│   │
│   ├── Models/
│   │   ├── JobModel.php             求人 DB操作
│   │   ├── CompanyModel.php         会社 DB操作
│   │   └── ResumeModel.php          履歴書 DB操作（upsert/deleteRow で重複排除）
│   │
│   └── Views/
│       ├── shared/
│       │   ├── header.php           HTMLヘッド・CSSリンク
│       │   ├── footer.php           フッター・JSリンク
│       │   └── 404.php              404ページ
│       ├── snippets/
│       │   ├── sidebar.php          サイドバー（config/nav.php を読み込む）
│       │   ├── topbar.php           トップバー
│       │   ├── home_dashboard.php   Homeダッシュボード Snippet
│       │   ├── job_search_form.php  検索フォーム Snippet
│       │   └── job_list_table.php   求人一覧テーブル Snippet
│       ├── jobs/
│       │   ├── index.php            求人一覧ページ
│       │   └── show.php             求人詳細ページ
│       └── resume/
│           ├── index.php            履歴書 閲覧ページ
│           ├── edit.php             履歴書 編集フォーム
│           ├── pdf_resume.php       履歴書 PDF
│           ├── pdf_career.php       職務経歴書 PDF
│           └── pdf_career_en.php    英文職務経歴書 PDF
│
└── database/
    ├── schema.sql                   求人・会社テーブル＋サンプルデータ
    └── resume_schema.sql            履歴書テーブル＋サンプルデータ
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
export DB_NAME=jobnext
export DB_USER=root
export DB_PASS=yourpassword
```

または `config/database.php` の define() のデフォルト値を直接編集。

### 3. 起動

```bash
cd jobnext/public
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
