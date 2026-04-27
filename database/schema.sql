-- database/schema.sql
CREATE DATABASE IF NOT EXISTS SelectJob 
  CHARACTER SET utf8mb4 
  COLLATE utf8mb4_unicode_ci;

USE SelectJob;

ALTER DATABASE SelectJob CHARACTER SET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS companies (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(255) NOT NULL,
    description TEXT,
    industry    VARCHAR(100),
    employees   VARCHAR(50),
    founded     YEAR,
    website     VARCHAR(255),
    address     VARCHAR(255),
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE companies CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS jobs (
    id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id       INT UNSIGNED NOT NULL,
    title            VARCHAR(255) NOT NULL,
    job_type         VARCHAR(100),
    salary_min       INT,
    salary_max       INT,
    location         VARCHAR(255),
    work_description TEXT,
    job_change_scope VARCHAR(255),
    requirements     TEXT,
    preferred        TEXT,
    benefits         TEXT,
    working_hours    VARCHAR(255),
    holiday          VARCHAR(255),
    remote_work      TINYINT(1) DEFAULT 0,
    flex_time        TINYINT(1) DEFAULT 0,
    is_new           TINYINT(1) DEFAULT 0,
    status           ENUM('active','closed') DEFAULT 'active',
    created_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE jobs CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS job_tags (
    id     INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    job_id INT UNSIGNED NOT NULL,
    tag    VARCHAR(100) NOT NULL,
    FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE job_tags CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS users (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name          VARCHAR(100) NOT NULL,
    email         VARCHAR(255) NOT NULL UNIQUE,
    password      VARCHAR(255) NOT NULL,
    role          ENUM('user','admin') NOT NULL DEFAULT 'user',
    is_active     TINYINT(1) NOT NULL DEFAULT 1,
    last_login_at TIMESTAMP NULL,
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE users CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- カラムの動的追加: role
SET @c = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'role');
SET @s = IF(@c = 0, "ALTER TABLE users ADD COLUMN role ENUM('user','admin') NOT NULL DEFAULT 'user'", 'SELECT 1');
PREPARE p FROM @s; EXECUTE p; DEALLOCATE PREPARE p;

-- カラムの動的追加: is_active
SET @c = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'is_active');
SET @s = IF(@c = 0, 'ALTER TABLE users ADD COLUMN is_active TINYINT(1) NOT NULL DEFAULT 1', 'SELECT 1');
PREPARE p FROM @s; EXECUTE p; DEALLOCATE PREPARE p;

-- カラムの動的追加: last_login_at
SET @c = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'last_login_at');
SET @s = IF(@c = 0, 'ALTER TABLE users ADD COLUMN last_login_at TIMESTAMP NULL', 'SELECT 1');
PREPARE p FROM @s; EXECUTE p; DEALLOCATE PREPARE p;

CREATE TABLE IF NOT EXISTS applications (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id    INT UNSIGNED NOT NULL,
    job_id     INT UNSIGNED NOT NULL,
    status     ENUM('applied','reviewing','interview','offered','rejected') DEFAULT 'applied',
    applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_user_job (user_id, job_id),
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (job_id) REFERENCES jobs(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE applications CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;


-- パスワードリセットトークンテーブル
CREATE TABLE IF NOT EXISTS password_resets (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email      VARCHAR(255) NOT NULL,
    token      VARCHAR(64)  NOT NULL UNIQUE,
    expires_at TIMESTAMP    NOT NULL,
    used       TINYINT(1)   NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_token (token)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
ALTER TABLE password_resets CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- ログイン試行回数制限テーブル
CREATE TABLE IF NOT EXISTS login_attempts (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    identifier   VARCHAR(255) NOT NULL COMMENT 'メールアドレスまたはIPアドレス',
    attempts     INT UNSIGNED NOT NULL DEFAULT 0,
    locked_until TIMESTAMP NULL COMMENT 'ロック解除日時',
    last_attempt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_identifier (identifier)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
ALTER TABLE login_attempts CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
