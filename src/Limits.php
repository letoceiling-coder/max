<?php

namespace LetoceilingCoder\Max;

/**
 * Константы лимитов и ограничений Max Bot API
 * Документация: https://dev.max.ru/docs-api
 */
class Limits
{
    // ==========================================
    // Клавиатура
    // ==========================================
    
    /** Максимальное количество кнопок в клавиатуре */
    public const KEYBOARD_BUTTONS_MAX = 210;
    
    /** Максимальное количество рядов в клавиатуре */
    public const KEYBOARD_ROWS_MAX = 30;
    
    /** Максимальное количество кнопок в одном ряду */
    public const KEYBOARD_BUTTONS_PER_ROW_MAX = 7;
    
    /** Максимальное количество кнопок link, open_app, request_geo_location, request_contact в ряду */
    public const KEYBOARD_SPECIAL_BUTTONS_PER_ROW_MAX = 3;
    
    /** Максимальная длина ссылки в кнопке link */
    public const BUTTON_URL_MAX_LENGTH = 2048;
    
    /** Максимальная длина текста на кнопке */
    public const BUTTON_TEXT_MAX_LENGTH = 256;
    
    /** Максимальная длина payload в callback кнопке */
    public const BUTTON_CALLBACK_PAYLOAD_MAX_LENGTH = 4096;
    
    // ==========================================
    // Сообщения
    // ==========================================
    
    /** Максимальная длина текста сообщения */
    public const MESSAGE_TEXT_MAX_LENGTH = 4096;
    
    /** Максимальная длина названия чата */
    public const CHAT_TITLE_MAX_LENGTH = 255;
    
    /** Максимальная длина описания чата */
    public const CHAT_DESCRIPTION_MAX_LENGTH = 1000;
    
    // ==========================================
    // Файлы
    // ==========================================
    
    /** Максимальный размер файла для загрузки (20 MB) */
    public const FILE_MAX_SIZE = 20 * 1024 * 1024;
    
    // ==========================================
    // HTTP коды
    // ==========================================
    
    /** Успешная операция */
    public const HTTP_OK = 200;
    
    /** Недействительный запрос */
    public const HTTP_BAD_REQUEST = 400;
    
    /** Ошибка аутентификации */
    public const HTTP_UNAUTHORIZED = 401;
    
    /** Ресурс не найден */
    public const HTTP_NOT_FOUND = 404;
    
    /** Метод не допускается */
    public const HTTP_METHOD_NOT_ALLOWED = 405;
    
    /** Превышено количество запросов */
    public const HTTP_TOO_MANY_REQUESTS = 429;
    
    /** Сервис недоступен */
    public const HTTP_SERVICE_UNAVAILABLE = 503;
    
    // ==========================================
    // Rate Limits
    // ==========================================
    
    /** Максимум запросов в секунду (примерно) */
    public const API_REQUESTS_PER_SECOND = 20;
    
    /** Максимум сообщений в секунду для одного чата */
    public const MESSAGES_PER_SECOND_PER_CHAT = 1;
}

