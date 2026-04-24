<?php
namespace App\Controllers;
use App\Models\ChatModel;
use App\Models\AuthModel;

class ChatController extends BaseController {
    private ChatModel $chat;
    private AuthModel $auth;

    public function __construct() {
        parent::__construct();
        $this->requireLogin();
        $this->chat = new ChatModel();
        $this->auth = new AuthModel();
    }

    public function userRoom(): void {
        $me = auth_user();
        $room = $this->chat->findOrCreateRoom($me['id']);
        $this->chat->markAsRead($room['id'], $me['id']);

        $messages = $this->chat->getMessages($room['id']);
        $latestId = $this->chat->getLatestMessageId($room['id']);

        $this->render('chat/room', compact('room', 'messages', 'latestId'));
    }

    public function userSend(): void {
        $this->verifyCsrf();
        $me = auth_user();
        $body = trim($this->post('body'));

        if ($body === '') {
            $this->redirect('/chat');
        }

        $room = $this->chat->findOrCreateRoom($me['id']);
        $this->chat->sendMessage($room['id'], $me['id'], $body);

        $this->redirect('/chat');
    }

    public function userPoll(): void {
        $me = auth_user();
        $afterId = (int)$this->get('after', 0);
        $room = $this->chat->findOrCreateRoom($me['id']);
        $this->chat->markAsRead($room['id'], $me['id']);
        $this->jsonResponse($this->chat->getMessages($room['id'], $afterId));
    }

    public function adminList(): void {
        $this->requireAdmin();

        $pager = $this->paginate($this->chat->countRooms());
        $rooms = $this->chat->getRoomsPaged($pager['per_page'], $pager['offset']);

        $totalUnread = $this->chat->getUnreadCountForAdmin();
        $this->render('admin/chat/list', compact('rooms', 'totalUnread', 'pager'));
    }

    public function adminRoom(int $roomId): void {
        $this->requireAdmin();
        $me = auth_user();
        $room = $this->chat->findRoom($roomId);

        if (!$room) {
            $this->abort404();
        }

        $this->chat->markAsRead($roomId, $me['id']);
        $messages = $this->chat->getMessages($roomId);
        $latestId = $this->chat->getLatestMessageId($roomId);
        $roomUser = $this->auth->findById($room['user_id']);

        $this->render('admin/chat/room', compact('room', 'roomUser', 'messages', 'latestId'));
    }

    public function adminSend(int $roomId): void {
        $this->requireAdmin();
        $this->verifyCsrf();

        $me   = auth_user();
        $body = trim($this->post('body'));
        $room = $this->chat->findRoom($roomId);

        if (!$room) {
            $this->abort404();
        }

        if ($body !== '') {
            $this->chat->sendMessage($roomId, $me['id'], $body);
        }

        $this->jsonResponse(['success' => true]);
    }

    public function adminPoll(int $roomId): void {
        $this->requireAdmin();
        $me = auth_user();
        $afterId = (int)$this->get('after', 0);
        $room = $this->chat->findRoom($roomId);

        if (!$room) {
            $this->jsonResponse([]);
        }

        $this->chat->markAsRead($roomId, $me['id']);
        $this->jsonResponse($this->chat->getMessages($roomId, $afterId));
    }
}
