<?php

namespace LetoceilingCoder\Max\Types;

/**
 * Представляет пользователя Max
 */
class User
{
    public int $userId;
    public string $name;
    public ?string $username = null;
    public bool $isBot = false;
    public ?int $lastActivityTime = null;

    public static function fromArray(array $data): self
    {
        $user = new self();
        $user->userId = $data['user_id'];
        $user->name = $data['name'];
        $user->username = $data['username'] ?? null;
        $user->isBot = $data['is_bot'] ?? false;
        $user->lastActivityTime = $data['last_activity_time'] ?? null;
        return $user;
    }

    public function toArray(): array
    {
        return array_filter([
            'user_id' => $this->userId,
            'name' => $this->name,
            'username' => $this->username,
            'is_bot' => $this->isBot,
            'last_activity_time' => $this->lastActivityTime,
        ], fn($value) => $value !== null);
    }
}

