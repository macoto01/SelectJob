-- database/chat_schema.sql
USE SelectJob;

CREATE TABLE IF NOT EXISTS chat_rooms (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id     INT UNSIGNED NOT NULL UNIQUE COMMENT '対象ユーザーID',
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE chat_rooms CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS chat_messages (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    room_id     INT UNSIGNED NOT NULL,
    sender_id   INT UNSIGNED NOT NULL,
    body        TEXT NOT NULL,
    is_read     TINYINT(1) NOT NULL DEFAULT 0,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (room_id) REFERENCES chat_rooms(id) ON DELETE CASCADE,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_room_created (room_id, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE chat_messages CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
SELECT 'チャットテーブルのセットアップが完了しました。' AS result;
