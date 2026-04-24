<?php
namespace App\Models;

class ChatModel extends BaseModel {
    public function findOrCreateRoom(int $userId): array {
        $stmt=$this->db->prepare('SELECT * FROM chat_rooms WHERE user_id=? LIMIT 1'); $stmt->execute([$userId]);
        $room=$stmt->fetch();
        if(!$room){ $this->db->prepare('INSERT INTO chat_rooms (user_id) VALUES (?)')->execute([$userId]); $stmt->execute([$userId]); $room=$stmt->fetch(); }
        return $room;
    }
    public function findRoom(int $roomId): ?array {
        $stmt=$this->db->prepare('SELECT * FROM chat_rooms WHERE id=? LIMIT 1'); $stmt->execute([$roomId]); return $stmt->fetch()?:null;
    }
    public function getRoomsPaged(int $limit = 20, int $offset = 0): array {
        $sql='SELECT r.id AS room_id,u.id AS user_id,u.name AS user_name,u.email AS user_email,(SELECT body FROM chat_messages WHERE room_id=r.id ORDER BY created_at DESC LIMIT 1) AS last_message,(SELECT created_at FROM chat_messages WHERE room_id=r.id ORDER BY created_at DESC LIMIT 1) AS last_message_at,(SELECT COUNT(*) FROM chat_messages WHERE room_id=r.id AND sender_id=u.id AND is_read=0) AS unread_count FROM chat_rooms r JOIN users u ON u.id=r.user_id ORDER BY last_message_at DESC,r.created_at DESC LIMIT :limit OFFSET :offset';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    public function countRooms(): int {
        return (int)$this->db->query('SELECT COUNT(*) FROM chat_rooms')->fetchColumn();
    }
    public function getMessages(int $roomId, int $afterId=0): array {
        $stmt=$this->db->prepare('SELECT m.*,u.name AS sender_name,u.role AS sender_role FROM chat_messages m JOIN users u ON u.id=m.sender_id WHERE m.room_id=:rid AND m.id>:aid ORDER BY m.created_at ASC');
        $stmt->execute([':rid'=>$roomId,':aid'=>$afterId]); return $stmt->fetchAll();
    }
    public function sendMessage(int $roomId, int $senderId, string $body): int {
        $this->db->prepare('INSERT INTO chat_messages (room_id,sender_id,body) VALUES (?,?,?)')->execute([$roomId,$senderId,$body]);
        return (int)$this->db->lastInsertId();
    }
    public function markAsRead(int $roomId, int $readerId): void {
        $this->db->prepare('UPDATE chat_messages SET is_read=1 WHERE room_id=? AND sender_id!=? AND is_read=0')->execute([$roomId,$readerId]);
    }
    public function getUnreadCountForUser(int $userId): int {
        $stmt=$this->db->prepare('SELECT COUNT(*) FROM chat_messages m JOIN chat_rooms r ON r.id=m.room_id WHERE r.user_id=:uid AND m.sender_id!=:uid2 AND m.is_read=0');
        $stmt->execute([':uid'=>$userId, ':uid2'=>$userId]); return (int)$stmt->fetchColumn();
    }
    public function getUnreadCountForAdmin(): int {
        return (int)$this->db->query('SELECT COUNT(*) FROM chat_messages m JOIN chat_rooms r ON r.id=m.room_id JOIN users u ON u.id=r.user_id WHERE m.sender_id=u.id AND m.is_read=0')->fetchColumn();
    }
    public function getLatestMessageId(int $roomId): int {
        $stmt=$this->db->prepare('SELECT COALESCE(MAX(id),0) FROM chat_messages WHERE room_id=?'); $stmt->execute([$roomId]); return (int)$stmt->fetchColumn();
    }
}
