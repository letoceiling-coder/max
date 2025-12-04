<?php

namespace LetoceilingCoder\Max\Types;

/**
 * Представляет сообщение Max
 */
class Message
{
    public string $messageId;
    public string $chatId;
    public int $userId;
    public string $text;
    public ?int $timestamp = null;
    public ?array $attachments = null;

    public static function fromArray(array $data): self
    {
        $message = new self();
        $message->messageId = $data['message_id'];
        $message->chatId = $data['chat_id'];
        $message->userId = $data['user_id'];
        $message->text = $data['text'] ?? '';
        $message->timestamp = $data['timestamp'] ?? null;
        $message->attachments = $data['attachments'] ?? null;
        return $message;
    }

    public function toArray(): array
    {
        return array_filter([
            'message_id' => $this->messageId,
            'chat_id' => $this->chatId,
            'user_id' => $this->userId,
            'text' => $this->text,
            'timestamp' => $this->timestamp,
            'attachments' => $this->attachments,
        ], fn($value) => $value !== null);
    }
}

