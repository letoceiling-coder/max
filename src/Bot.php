<?php

namespace LetoceilingCoder\Max;

use LetoceilingCoder\Max\MaxClient;

/**
 * Класс для работы с Max Bot API
 * Документация: https://dev.max.ru/docs-api
 */
class Bot extends MaxClient
{
    // ==========================================
    // Bots (Информация о боте)
    // ==========================================

    /**
     * Получить информацию о текущем боте
     * GET /bots/me
     */
    public function getBotInfo(): array
    {
        return $this->get('bots/me');
    }

    // ==========================================
    // Messages (Сообщения)
    // ==========================================

    /**
     * Получить список сообщений
     * GET /messages
     */
    public function getMessages(array $params = []): array
    {
        return $this->get('messages', $params);
    }

    /**
     * Отправить сообщение
     * POST /messages
     * 
     * @param string|int $chatId - ID чата или user_id
     * @param string $text - Текст сообщения
     * @param array $params - Дополнительные параметры (attachments, format и т.д.)
     */
    public function sendMessage(string|int $chatId, string $text, array $params = []): array
    {
        // Валидация
        Validator::validateMessageText($text);
        
        if (isset($params['format'])) {
            Validator::validateFormat($params['format']);
        }
        
        $data = array_merge([
            'chat_id' => $chatId,
            'text' => $text,
        ], $params);

        return $this->post('messages', $data);
    }

    /**
     * Редактировать сообщение
     * PUT /messages
     */
    public function editMessage(string $messageId, string $text, array $params = []): array
    {
        $data = array_merge([
            'message_id' => $messageId,
            'text' => $text,
        ], $params);

        return $this->put('messages', $data);
    }

    /**
     * Удалить сообщение
     * DELETE /messages
     */
    public function deleteMessage(string $messageId): array
    {
        return $this->delete('messages', ['message_id' => $messageId]);
    }

    /**
     * Получить конкретное сообщение
     * GET /messages/{messageId}
     */
    public function getMessage(string $messageId): array
    {
        return $this->get("messages/{$messageId}");
    }

    /**
     * Получить информацию о видео
     * GET /messages/video
     */
    public function getVideoInfo(string $videoUrl): array
    {
        return $this->get('messages/video', ['url' => $videoUrl]);
    }

    /**
     * Ответить на callback
     * POST /messages/callback
     */
    public function answerCallback(string $callbackId, array $params = []): array
    {
        $data = array_merge([
            'callback_id' => $callbackId,
        ], $params);

        return $this->post('messages/callback', $data);
    }

    // ==========================================
    // Chats (Групповые чаты)
    // ==========================================

    /**
     * Получить список всех групповых чатов
     * GET /chats
     */
    public function getChats(array $params = []): array
    {
        return $this->get('chats', $params);
    }

    /**
     * Получить групповой чат по ссылке
     * GET /chats/byLink
     */
    public function getChatByLink(string $link): array
    {
        return $this->get('chats/byLink', ['link' => $link]);
    }

    /**
     * Получить информацию о групповом чате
     * GET /chats/{chatId}
     */
    public function getChat(string $chatId): array
    {
        return $this->get("chats/{$chatId}");
    }

    /**
     * Изменить информацию о групповом чате
     * PATCH /chats/{chatId}
     */
    public function updateChat(string $chatId, array $data): array
    {
        // Валидация
        if (isset($data['title'])) {
            Validator::validateChatTitle($data['title']);
        }
        
        if (isset($data['description'])) {
            Validator::validateChatDescription($data['description']);
        }
        
        return $this->patch("chats/{$chatId}", $data);
    }

    /**
     * Удалить групповой чат
     * DELETE /chats/{chatId}
     */
    public function deleteChat(string $chatId): array
    {
        return $this->delete("chats/{$chatId}");
    }

    /**
     * Отправить действие бота в групповой чат (typing, etc.)
     * POST /chats/{chatId}/actions
     */
    public function sendChatAction(string $chatId, string $action): array
    {
        return $this->post("chats/{$chatId}/actions", ['action' => $action]);
    }

    /**
     * Получить закреплённое сообщение в групповом чате
     * GET /chats/{chatId}/pinned
     */
    public function getPinnedMessage(string $chatId): array
    {
        return $this->get("chats/{$chatId}/pinned");
    }

    /**
     * Закрепить сообщение в групповом чате
     * PUT /chats/{chatId}/pinned
     */
    public function pinMessage(string $chatId, string $messageId): array
    {
        return $this->put("chats/{$chatId}/pinned", ['message_id' => $messageId]);
    }

    /**
     * Удалить закреплённое сообщение
     * DELETE /chats/{chatId}/pinned
     */
    public function unpinMessage(string $chatId): array
    {
        return $this->delete("chats/{$chatId}/pinned");
    }

    /**
     * Получить информацию о членстве бота в групповом чате
     * GET /chats/{chatId}/members/me
     */
    public function getBotMembership(string $chatId): array
    {
        return $this->get("chats/{$chatId}/members/me");
    }

    /**
     * Удалить бота из группового чата (покинуть чат)
     * DELETE /chats/{chatId}/members/me
     */
    public function leaveChat(string $chatId): array
    {
        return $this->delete("chats/{$chatId}/members/me");
    }

    /**
     * Получить список администраторов группового чата
     * GET /chats/{chatId}/admins
     */
    public function getChatAdmins(string $chatId): array
    {
        return $this->get("chats/{$chatId}/admins");
    }

    /**
     * Назначить администратора группового чата
     * POST /chats/{chatId}/admins
     */
    public function promoteChatAdmin(string $chatId, int $userId): array
    {
        return $this->post("chats/{$chatId}/admins", ['user_id' => $userId]);
    }

    /**
     * Отменить права администратора
     * DELETE /chats/{chatId}/admins
     */
    public function demoteChatAdmin(string $chatId, int $userId): array
    {
        return $this->delete("chats/{$chatId}/admins", ['user_id' => $userId]);
    }

    /**
     * Получить участников группового чата
     * GET /chats/{chatId}/members
     */
    public function getChatMembers(string $chatId, array $params = []): array
    {
        return $this->get("chats/{$chatId}/members", $params);
    }

    /**
     * Добавить участников в групповой чат
     * POST /chats/{chatId}/members
     */
    public function addChatMembers(string $chatId, array $userIds): array
    {
        return $this->post("chats/{$chatId}/members", ['user_ids' => $userIds]);
    }

    /**
     * Удалить участника из группового чата
     * DELETE /chats/{chatId}/members
     */
    public function removeChatMember(string $chatId, int $userId): array
    {
        return $this->delete("chats/{$chatId}/members", ['user_id' => $userId]);
    }

    // ==========================================
    // Subscriptions (Подписки на обновления)
    // ==========================================

    /**
     * Получить подписки (webhook/long polling)
     * GET /subscriptions
     */
    public function getSubscriptions(): array
    {
        return $this->get('subscriptions');
    }

    /**
     * Подписаться на обновления (установить webhook)
     * POST /subscriptions
     * 
     * @param string $url - URL для webhook
     * @param array $params - Дополнительные параметры
     */
    public function subscribe(string $url, array $params = []): array
    {
        $data = array_merge([
            'url' => $url,
        ], $params);

        return $this->post('subscriptions', $data);
    }

    /**
     * Отписаться от обновлений (удалить webhook)
     * DELETE /subscriptions
     */
    public function unsubscribe(): array
    {
        return $this->delete('subscriptions');
    }

    /**
     * Получить обновления (long polling)
     * GET /subscriptions/updates
     */
    public function getUpdates(array $params = []): array
    {
        return $this->get('subscriptions/updates', $params);
    }

    // ==========================================
    // Upload (Загрузка файлов)
    // ==========================================

    /**
     * Загрузить файлы
     * POST /upload
     * 
     * @param array $files - Массив файлов для загрузки
     * @return array
     */
    public function upload(array $files): array
    {
        return $this->uploadFile($files);
    }
}

