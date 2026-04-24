<?php
/**
 * app/Models/AuthModel.php
 * 認証・ユーザー管理・ログイン試行制限
 *
 * ログイン試行制限:
 *   - メールアドレスとIPアドレスの両方を追跡
 *   - 5回失敗 → 5分ロック
 *   - 成功時に試行カウントをリセット
 */
namespace App\Models;

class AuthModel extends BaseModel {
    private const MAX_ATTEMPTS  = 5;    // 最大試行回数
    private const LOCKOUT_MIN   = 5;   // ロック時間（分）

    public function login(string $email, string $password, string $ipAddress): ?array {
        $identifier = $email . '_' . $ipAddress;

        if ($this->isLocked($identifier)) {
            $_SESSION['login_error'] = 'アカウントがロックされています。' . $this->lockRemainingMinutes($identifier) . '分後に再度お試しください。';
            return null;
        }

        $stmt = $this->db->prepare(
            'SELECT id,name,email,password,role FROM users WHERE email=:e AND is_active=1 LIMIT 1'
        );
        $stmt->execute([':e' => $email]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password'])) {
            $this->recordFailure($identifier);
            $attempts = $this->getAttempts($identifier);
            $remaining = self::MAX_ATTEMPTS - $attempts;
            if ($remaining <= 0) {
                $_SESSION['login_error'] = 'ログイン試行回数が上限に達しました。5分間アカウントをロックします。';
            } else {
                $_SESSION['login_error'] = 'メールアドレスまたはパスワードが正しくありません。あと' . $remaining . '回でロックされます。';
            }
            return null;
        }

        $this->clearAttempts($identifier);
        unset($_SESSION['login_error']);

        $this->db->prepare('UPDATE users SET last_login_at=NOW() WHERE id=?')->execute([$user['id']]);
        unset($user['password']);
        $_SESSION['auth_user'] = [
            'id'    => $user['id'],
            'name'  => $user['name'],
            'role'  => $user['role'],
            'email' => $user['email']
        ];
        session_regenerate_id(true);
        return $user;
    }

    public function logout(): void {
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
    }

    private function isLocked(string $identifier): bool {
        $stmt = $this->db->prepare(
            'SELECT locked_until FROM login_attempts WHERE identifier=? LIMIT 1'
        );
        $stmt->execute([$identifier]);
        $lockedUntil = $stmt->fetchColumn();
        return $lockedUntil && strtotime($lockedUntil) > time();
    }

    private function lockRemainingMinutes(string $identifier): int {
        $stmt = $this->db->prepare(
            'SELECT locked_until FROM login_attempts WHERE identifier=? LIMIT 1'
        );
        $stmt->execute([$identifier]);
        $lockedUntil = $stmt->fetchColumn();
        if ($lockedUntil) {
            return (int)ceil((strtotime($lockedUntil) - time()) / 60);
        }
        return 0;
    }

    private function getAttempts(string $identifier): int {
        $stmt = $this->db->prepare(
            'SELECT attempts FROM login_attempts WHERE identifier=? LIMIT 1'
        );
        $stmt->execute([$identifier]);
        return (int)$stmt->fetchColumn();
    }

    private function recordFailure(string $identifier): void {
        $stmt = $this->db->prepare(
            'SELECT id, attempts FROM login_attempts WHERE identifier=? LIMIT 1'
        );
        $stmt->execute([$identifier]);
        $row = $stmt->fetch();

        if ($row) {
            $newAttempts = $row['attempts'] + 1;
            $lockedUntil = $newAttempts >= self::MAX_ATTEMPTS
                ? date('Y-m-d H:i:s', strtotime('+' . self::LOCKOUT_MIN . ' minutes'))
                : null;
            $this->db->prepare(
                'UPDATE login_attempts SET attempts=?, locked_until=?, last_attempt=NOW() WHERE id=?'
            )->execute([$newAttempts, $lockedUntil, $row['id']]);
        } else {
            $this->db->prepare(
                'INSERT INTO login_attempts (identifier, attempts, last_attempt) VALUES (?,1,NOW())'
            )->execute([$identifier]);
        }
    }

    private function clearAttempts(string $identifier): void {
        $this->db->prepare(
            'DELETE FROM login_attempts WHERE identifier=?'
        )->execute([$identifier]);
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare('SELECT id,name,email,role FROM users WHERE id=? LIMIT 1');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function findPaged(int $limit = 20, int $offset = 0): array {
        $stmt = $this->db->prepare(
            'SELECT id,name,email,role,is_active,last_login_at,created_at 
             FROM users ORDER BY id DESC LIMIT :limit OFFSET :offset'
        );
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function countAll(): int {
        return (int)$this->db->query('SELECT COUNT(*) FROM users')->fetchColumn();
    }

    public function findDetailById(int $id): ?array {
        $stmt = $this->db->prepare(
            'SELECT id,name,email,password,role,is_active,last_login_at,created_at FROM users WHERE id=? LIMIT 1'
        );
        $stmt->execute([$id]);
        $user = $stmt->fetch();
        if (!$user) return null;

        $s2 = $this->db->prepare('SELECT COUNT(*) FROM applications WHERE user_id=?');
        $s2->execute([$id]);
        $user['application_count'] = (int)$s2->fetchColumn();

        $s3 = $this->db->prepare('SELECT id FROM chat_rooms WHERE user_id=? LIMIT 1');
        $s3->execute([$id]);
        $room = $s3->fetch();
        $user['chat_room_id'] = $room ? (int)$room['id'] : null;

        if ($user['chat_room_id']) {
            $s4 = $this->db->prepare('SELECT COUNT(*) FROM chat_messages WHERE room_id=?');
            $s4->execute([$user['chat_room_id']]);
            $user['message_count'] = (int)$s4->fetchColumn();
        } else {
            $user['message_count'] = 0;
        }
        return $user;
    }

    public function toggleActive(int $id): void {
        $this->db->prepare('UPDATE users SET is_active=IF(is_active=1,0,1) WHERE id=?')->execute([$id]);
    }

    public function create(array $userAttributes): int {
        $stmt = $this->db->prepare(
            'INSERT INTO users (name, email, password, role, is_active) VALUES (:name, :email, :password, :role, :is_active)'
        );
        $stmt->execute([
            ':name'      => $userAttributes['name'],
            ':email'     => $userAttributes['email'],
            ':password'  => password_hash($userAttributes['password'], \PASSWORD_BCRYPT),
            ':role'      => in_array($userAttributes['role'] ?? '', ['user','admin']) ? $userAttributes['role'] : 'user',
            ':is_active' => isset($userAttributes['is_active']) ? 1 : 0,
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $userAttributes): void {
        $passwordHash = '';
        if (!empty($userAttributes['password'])) {
            $passwordHash = password_hash($userAttributes['password'], \PASSWORD_BCRYPT);
        } else {
            $stmt = $this->db->prepare('SELECT password FROM users WHERE id = ?');
            $stmt->execute([$id]);
            $passwordHash = $stmt->fetchColumn();
        }

        $this->db->prepare(
            'UPDATE users SET name=:name, email=:email, password=:password, role=:role, is_active=:is_active WHERE id=:id'
        )->execute([
            ':name'      => $userAttributes['name'],
            ':email'     => $userAttributes['email'],
            ':password'  => $passwordHash,
            ':role'      => in_array($userAttributes['role'] ?? '', ['user','admin']) ? $userAttributes['role'] : 'user',
            ':is_active' => isset($userAttributes['is_active']) ? 1 : 0,
            ':id'        => $id,
        ]);
    }

    public function emailExists(string $email, int $excludeId = 0): bool {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM users WHERE email=:e AND id!=:id');
        $stmt->execute([':e' => $email, ':id' => $excludeId]);
        return (int)$stmt->fetchColumn() > 0;
    }

    public function findActiveByEmail(string $email): ?array {
        $stmt = $this->db->prepare('SELECT id, name FROM users WHERE email = ? AND is_active = 1 LIMIT 1');
        $stmt->execute([$email]);
        return $stmt->fetch() ?: null;
    }

    public function createPasswordReset(string $email, string $token, string $expiresAt): void {
        $this->db->prepare('DELETE FROM password_resets WHERE email = ?')->execute([$email]);
        $this->db->prepare('INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)')
            ->execute([$email, $token, $expiresAt]);
    }

    public function validateResetToken(string $token): ?array {
        if ($token === '') return null;
        $stmt = $this->db->prepare('SELECT * FROM password_resets WHERE token = ? AND used = 0 AND expires_at > NOW() LIMIT 1');
        $stmt->execute([$token]);
        return $stmt->fetch() ?: null;
    }

    public function resetPassword(string $email, string $newPassword, string $token): void {
        $this->db->beginTransaction();
        try {
            $this->updatePasswordByEmail($email, $newPassword);
            $this->db->prepare('UPDATE password_resets SET used = 1 WHERE token = ?')->execute([$token]);
            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    private function updatePasswordByEmail(string $email, string $password): void {
        $hash = password_hash($password, \PASSWORD_BCRYPT);
        $this->db->prepare('UPDATE users SET password = ? WHERE email = ?')
            ->execute([$hash, $email]);
    }

    public function verifyPassword(int $userId, string $password): bool {
        $stmt = $this->db->prepare('SELECT password FROM users WHERE id = ? LIMIT 1');
        $stmt->execute([$userId]);
        $hash = $stmt->fetchColumn();
        return $hash && password_verify($password, $hash);
    }

    public function updatePassword(int $userId, string $newPassword): bool {
        $hash = password_hash($newPassword, \PASSWORD_BCRYPT);
        return $this->db->prepare('UPDATE users SET password = ? WHERE id = ?')
            ->execute([$hash, $userId]);
    }
}
