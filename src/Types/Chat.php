<?php

namespace LetoceilingCoder\Max\Types;

/**
 * Представляет чат Max
 */
class Chat
{
    public string $chatId;
    public string $type; // "dialog", "group", "channel"
    public ?string $title = null;
    public ?string $description = null;
    public ?array $participants = null;

    public static function fromArray(array $data): self
    {
        $chat = new self();
        $chat->chatId = $data['chat_id'];
        $chat->type = $data['type'];
        $chat->title = $data['title'] ?? null;
        $chat->description = $data['description'] ?? null;
        $chat->participants = $data['participants'] ?? null;
        return $chat;
    }

    public function toArray(): array
    {
        return array_filter([
            'chat_id' => $this->chatId,
            'type' => $this->type,
            'title' => $this->title,
            'description' => $this->description,
            'participants' => $this->participants,
        ], fn($value) => $value !== null);
    }

    public function isDialog(): bool
    {
        return $this->type === 'dialog';
    }

    public function isGroup(): bool
    {
        return $this->type === 'group';
    }

    public function isChannel(): bool
    {
        return $this->type === 'channel';
    }
}

