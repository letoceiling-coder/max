<?php

namespace LetoceilingCoder\Max;

/**
 * Фасад для удобного доступа к Max API
 */
class Max
{
    /**
     * Получить экземпляр Bot
     */
    public static function bot(?string $token = null): Bot
    {
        return new Bot($token);
    }

    /**
     * Получить экземпляр MiniApp
     */
    public static function miniApp(?string $secretKey = null): MiniApp
    {
        return new MiniApp($secretKey);
    }

    /**
     * Создать inline клавиатуру
     */
    public static function keyboard(): Keyboard
    {
        return new Keyboard();
    }

    /**
     * Быстрая отправка сообщения
     */
    public static function send(string|int $chatId, string $text, array $params = []): array
    {
        return static::bot()->sendMessage($chatId, $text, $params);
    }

    /**
     * Валидировать Mini App параметры
     */
    public static function validateMiniApp(string $params): bool
    {
        return static::miniApp()->validateParams($params);
    }

    /**
     * Получить пользователя из Mini App параметров
     */
    public static function getMiniAppUser(string $params): ?array
    {
        return static::miniApp()->getUser($params);
    }
}

