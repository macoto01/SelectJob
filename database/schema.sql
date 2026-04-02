-- =============================================
-- JobNext - データベーススキーマ
-- =============================================

-- クライアント接続の文字コードをUTF-8に設定
SET NAMES utf8mb4;

CREATE DATABASE IF NOT EXISTS jobnext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE jobnext;

-- 会社テーブル
CREATE TABLE IF NOT EXISTS companies (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(255) NOT NULL COMMENT '会社名',
    description TEXT         COMMENT '会社概要',
    industry    VARCHAR(100) COMMENT '業界',
    employees   VARCHAR(50)  COMMENT '従業員数',
    founded     YEAR         COMMENT '設立年',
    website     VARCHAR(255) COMMENT 'ウェブサイト',
    address     VARCHAR(255) COMMENT '所在地',
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 求人テーブル
CREATE TABLE IF NOT EXISTS jobs (
    id                  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id          INT UNSIGNED NOT NULL,
    title               VARCHAR(255) NOT NULL  COMMENT '職種名',
    job_type            VARCHAR(100)           COMMENT '雇用形態',
    salary_min          INT                    COMMENT '最低年収（万円）',
    salary_max          INT                    COMMENT '最高年収（万円）',
    location            VARCHAR(255)           COMMENT '勤務地',
    work_description    TEXT                   COMMENT '職務内容',
    job_change_scope    VARCHAR(255)           COMMENT '職務内容の変更の範囲',
    requirements        TEXT                   COMMENT '必要な経験/資格',
    preferred           TEXT                   COMMENT '歓迎スキル',
    benefits            TEXT                   COMMENT '待遇・福利厚生',
    working_hours       VARCHAR(255)           COMMENT '勤務時間',
    holiday             VARCHAR(255)           COMMENT '休日',
    remote_work         TINYINT(1) DEFAULT 0   COMMENT 'リモートワーク可',
    flex_time           TINYINT(1) DEFAULT 0   COMMENT 'フレックスあり',
    is_new              TINYINT(1) DEFAULT 0   COMMENT '新着フラグ',
    status              ENUM('active','closed') DEFAULT 'active' COMMENT 'ステータス',
    created_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 求人ポイントタグ（多対多）
CREATE TABLE IF NOT EXISTS job_tags (
    id      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    job_id  INT UNSIGNED NOT NULL,
    tag     VARCHAR(100) NOT NULL COMMENT 'ポイントタグ',
    FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ユーザーテーブル（応募者）
CREATE TABLE IF NOT EXISTS users (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100) NOT NULL,
    email      VARCHAR(255) NOT NULL UNIQUE,
    password   VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 応募テーブル
CREATE TABLE IF NOT EXISTS applications (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id    INT UNSIGNED NOT NULL,
    job_id     INT UNSIGNED NOT NULL,
    status     ENUM('applied','reviewing','interview','offered','rejected') DEFAULT 'applied',
    applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_user_job (user_id, job_id),
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (job_id)  REFERENCES jobs(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- サンプルデータ
-- =============================================

INSERT INTO companies (name, description, industry, employees, founded, website, address) VALUES
('株式会社NitroSquare',    'Webアプリ・モバイルアプリの受託開発および自社サービス開発。Laravel・Nuxt.js・Flutterを主軸に、スタートアップから大手まで幅広く支援。', 'IT・ソフトウェア', '50〜100名', 2015, 'https://nitrosquare.example.com', '東京都渋谷区渋谷1-1-1'),
('株式会社Japan Nexus Intelligence', 'AIと機械学習を活用したデータ分析プラットフォームの開発・提供。グローバルな視点で社会課題をテクノロジーで解決する。', 'AI・データ分析', '100〜300名', 2012, 'https://jni.example.com', '東京都新宿区西新宿2-2-2'),
('株式会社エフエム',        '自社クラウドプロダクトの企画・開発・運用。完全フルリモートで働けるモダンな職場環境を提供。', 'SaaS・クラウド', '30〜50名', 2018, 'https://fmcorp.example.com', '東京都東京都港区1-3-3'),
('株式会社テックスタート',  'スタートアップ向けに技術支援と開発内製化を推進。Go・Python・Rustを中心とした高パフォーマンスシステムを構築。', 'Web開発', '10〜30名', 2020, 'https://techstart.example.com', '東京都渋谷区代々木4-4-4'),
('グローバルリンク合同会社','国内外のエンタープライズ向けシステムインテグレーション。React・TypeScriptを用いたフロントエンド開発に強みを持つ。', 'SIer', '300〜500名', 2008, 'https://globallink.example.com', '神奈川県横浜市西区5-5-5');

INSERT INTO jobs (company_id, title, job_type, salary_min, salary_max, location, work_description, job_change_scope, requirements, preferred, benefits, working_hours, holiday, remote_work, flex_time, is_new) VALUES
(1, 'アプリケーションエンジニア', '正社員', 450, 750,
 '東京都渋谷区（リモート可）',
 '【職務内容】\n受託開発、自社サービス又はお客様先での開発業務となります。経験・スキル、本人のキャリアプランに応じて決定致します。\n業務に慣れていただいた後は、ご志向性に応じて、チームリーダー、プロジェクトマネージャーとキャリアアップしていただく事を期待しております。もちろん、プレイヤーとして頑張りたい、別方面で活躍したいという方にはマネジメント以外のキャリアプランもございます。将来的にはサービスを一緒に作り牽引してくれる事を期待しております。\n\n【開発環境】\n・新規開発はLaravelやNuxt.jsなどトレンドや状況に応じて適宜選択します。\n・モバイルアプリ開発は、Flutter/Dartなども選択肢となります。\n・インフラはAWSの利用がほとんどです。\n・受託開発の場合は、お客様の既存環境などもありますので、お客様と相談の上で決定します。\n※GitHub等のツールや技術については多岐にわたるため省略します。',
 '会社の定める業務',
 '【必須】\n・WebまたはモバイルアプリケーションのWebまたはモバイルアプリケーションの開発経験1年以上\n・チームでの開発経験\n・プログラミングスクールなどでの開発経験でも可\n\n【歓迎】\n・Laravel、Vue.js、Nuxt.js等のフレームワーク経験\n・Flutter/Dart経験\n・AWSを用いたインフラ構築経験',
 'Laravel/Vue.js/Nuxt.js経験、Flutter/Dart経験、AWS経験',
 '社会保険完備、交通費支給（月3万円まで）、書籍購入支援、副業可、フレックスタイム制',
 'フレックスタイム制（コアタイム 10:00〜15:00）', '完全週休2日制（土日祝）、年間休日125日', 1, 1, 1),

(2, 'リードエンジニア', '正社員', 700, 1000,
 '東京都新宿区',
 '【職務内容】\n大規模AIプラットフォームのバックエンド設計・実装をリードしていただきます。チームメンバーのコードレビューや技術選定にも関わり、プロダクトの品質向上に貢献いただきます。\n\n【技術スタック】\n・Python（FastAPI / Django）\n・PostgreSQL / Redis\n・AWS（ECS / Lambda / SageMaker）\n・Docker / Kubernetes',
 '会社の定める業務',
 '【必須】\n・Pythonによるサーバーサイド開発経験3年以上\n・チームリード経験\n・AWSの実務経験',
 'Kubernetes運用経験、機械学習モデルのデプロイ経験',
 '各種社会保険、ストックオプション制度、リモートワーク補助月1万円、書籍・学習支援制度',
 '9:00〜18:00（フレックス）', '完全週休2日制、祝日、夏季・年末年始休暇', 0, 1, 1),

(3, '自社クラウド製品の開発エンジニア', '正社員', 400, 600,
 '東京都港区（フルリモート）',
 '【職務内容】\n自社SaaSプロダクトの機能開発・改善を担当します。フロントエンドからバックエンドまで幅広く関わることができます。\n\n【技術スタック】\n・React / TypeScript\n・Node.js（NestJS）\n・MySQL / MongoDB\n・AWS / GCP',
 '会社の定める業務',
 '【必須】\n・Webアプリケーション開発経験2年以上\n・JavaScriptまたはTypeScriptの実務経験',
 'React・NestJS経験、クラウドサービスの実務経験',
 '社会保険完備、フルリモート勤務、機器購入補助（入社時10万円）',
 'フレックスタイム制', '完全週休2日制（土日祝）、年間休日120日以上', 1, 1, 1),

(4, 'バックエンドエンジニア（Go / Python）', '正社員', 600, 900,
 '東京都渋谷区（リモート可）',
 '【職務内容】\nGoまたはPythonを使ったAPIサーバーの設計・実装を担当します。パフォーマンスを意識した高品質なコードでシステムを支えます。\n\n【技術スタック】\n・Go（Gin/Echo）、Python（FastAPI）\n・PostgreSQL、Redis\n・Kubernetes / Docker\n・GCP',
 '会社の定める業務',
 '【必須】\n・GoまたはPythonでのAPI開発経験2年以上\n・RDBMSの設計・運用経験',
 'Kubernetes経験、gRPC経験、パフォーマンスチューニング経験',
 '社会保険完備、書籍支援、カンファレンス参加費支援、副業可',
 '10:00〜19:00（フレックス可）', '完全週休2日制、祝日', 1, 1, 1),

(5, 'フロントエンドエンジニア（React / TypeScript）', '正社員', 500, 750,
 '神奈川県横浜市（一部リモート）',
 '【職務内容】\nエンタープライズ向けWebシステムのフロントエンド開発を担当します。UX改善やパフォーマンス最適化にも積極的に取り組める環境です。\n\n【技術スタック】\n・React / TypeScript / Next.js\n・GraphQL（Apollo）\n・Jest / Storybook\n・AWS Amplify',
 '会社の定める業務',
 '【必須】\n・React + TypeScriptの実務経験2年以上\n・Gitを使ったチーム開発経験',
 'Next.js経験、GraphQL経験、アクセシビリティ対応経験',
 '社会保険完備、交通費全額支給、資格取得支援、フレックス制度',
 'フレックスタイム制（コアタイム 10:00〜16:00）', '週休2日（土日祝）、年間休日122日', 0, 1, 0);

INSERT INTO job_tags (job_id, tag) VALUES
(1, '第二新卒歓迎'), (1, '最新技術に明るい'), (1, 'フルスタックエンジニア候補'),
(2, 'リードポジション'), (2, 'AI・機械学習'), (2, 'ストックオプションあり'),
(3, 'フルリモート'), (3, 'SaaS開発'), (3, 'スタートアップ'),
(4, 'バックエンド特化'), (4, '高年収'), (4, '副業可'),
(5, 'フロントエンド特化'), (5, 'エンタープライズ'), (5, 'Next.js');
