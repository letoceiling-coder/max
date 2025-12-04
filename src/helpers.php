<?php

use LetoceilingCoder\Max\Bot;
use LetoceilingCoder\Max\MiniApp;
use LetoceilingCoder\Max\Keyboard;

if (!function_exists('max')) {
    /**
     * Получить экземпляр Max Bot
     */
    function max(): Bot
    {
        return app('max.bot');
    }
}

if (!function_exists('max_miniapp')) {
    /**
     * Получить экземпляр Max MiniApp
     */
    function max_miniapp(): MiniApp
    {
        return app('max.miniapp');
    }
}

if (!function_exists('max_send')) {
    /**
     * Быстрая отправка сообщения
     * 
     * @param int|string $chatId
     * @param string $text
     * @param array $params
     * @return array
     */
    function max_send(int|string $chatId, string $text, array $params = []): array
    {
        return max()->sendMessage($chatId, $text, $params);
    }
}

if (!function_exists('max_keyboard')) {
    /**
     * Создать inline клавиатуру
     */
    function max_keyboard(): Keyboard
    {
        return new Keyboard();
    }
}

if (!function_exists('max_validate_miniapp')) {
    /**
     * Валидировать Max Mini App параметры
     * 
     * @param string $params
     * @return bool
     */
    function max_validate_miniapp(string $params): bool
    {
        return max_miniapp()->validateParams($params);
    }
}

if (!function_exists('max_get_user')) {
    /**
     * Получить пользователя из Max Mini App параметров
     * 
     * @param string $params
     * @return array|null
     */
    function max_get_user(string $params): ?array
    {
        return max_miniapp()->getUser($params);
    }
}

if (!function_exists('max_is_admin')) {
    /**
     * Проверить является ли пользователь администратором
     * 
     * @param int $userId
     * @return bool
     */
    function max_is_admin(int $userId): bool
    {
        $adminIds = config('max.admin_ids', []);
        return in_array($userId, $adminIds);
    }
}

if (!function_exists('max_format_markdown')) {
    /**
     * Экранировать специальные символы для Markdown
     * 
     * @param string $text
     * @return string
     */
    function max_format_markdown(string $text): string
    {
        // В Max используются стандартные символы Markdown
        return $text;
    }
}

if (!function_exists('max_format_html')) {
    /**
     * Экранировать HTML для Max
     * 
     * @param string $text
     * @return string
     */
    function max_format_html(string $text): string
    {
        return htmlspecialchars($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
}

