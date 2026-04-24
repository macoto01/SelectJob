-- database/screening_schema.sql
USE jobnext;

CREATE TABLE IF NOT EXISTS screenings (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id         INT UNSIGNED NOT NULL,
    job_id          INT UNSIGNED NOT NULL,
    current_step    TINYINT NOT NULL DEFAULT 0,
    overall_status  ENUM('in_progress','offered','rejected','withdrawn') DEFAULT 'in_progress',
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_user_job (user_id, job_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE screenings CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS screening_steps (
    id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    screening_id   INT UNSIGNED NOT NULL,
    step           TINYINT NOT NULL,
    step_status    ENUM('pending','scheduled','passed','failed','cancelled') DEFAULT 'pending',
    scheduled_at   DATETIME NULL,
    meet_url       VARCHAR(512) NULL,
    location_note  VARCHAR(255) NULL,
    result_at      TIMESTAMP NULL,
    created_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_screening_step (screening_id, step),
    FOREIGN KEY (screening_id) REFERENCES screenings(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE screening_steps CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS screening_feedbacks (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    screening_id  INT UNSIGNED NOT NULL,
    step          TINYINT NULL,
    author_id     INT UNSIGNED NOT NULL,
    author_role   ENUM('user','admin') NOT NULL,
    body          TEXT NOT NULL,
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (screening_id) REFERENCES screenings(id) ON DELETE CASCADE,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE screening_feedbacks CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
SELECT '選考管理テーブルのセットアップが完了しました。' AS result;
