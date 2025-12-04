<?php

namespace LetoceilingCoder\Max;

use LetoceilingCoder\Max\Exceptions\MaxValidationException;

/**
 * Валидатор для проверки данных перед отправкой в Max API
 */
class Validator
{
    /**
     * Валидировать текст сообщения
     */
    public static function validateMessageText(string $text): void
    {
        if (empty($text)) {
            throw new MaxValidationException('Message text cannot be empty');
        }

        $length = mb_strlen($text);
        if ($length > Limits::MESSAGE_TEXT_MAX_LENGTH) {
            throw new MaxValidationException(
                "Message text is too long ({$length} characters). Maximum: " . Limits::MESSAGE_TEXT_MAX_LENGTH
            );
        }
    }

    /**
     * Валидировать текст кнопки
     */
    public static function validateButtonText(string $text): void
    {
        if (empty($text)) {
            throw new MaxValidationException('Button text cannot be empty');
        }

        $length = mb_strlen($text);
        if ($length > Limits::BUTTON_TEXT_MAX_LENGTH) {
            throw new MaxValidationException(
                "Button text is too long ({$length} characters). Maximum: " . Limits::BUTTON_TEXT_MAX_LENGTH
            );
        }
    }

    /**
     * Валидировать URL кнопки
     */
    public static function validateButtonUrl(string $url): void
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new MaxValidationException("Invalid URL: {$url}");
        }

        $length = strlen($url);
        if ($length > Limits::BUTTON_URL_MAX_LENGTH) {
            throw new MaxValidationException(
                "Button URL is too long ({$length} characters). Maximum: " . Limits::BUTTON_URL_MAX_LENGTH
            );
        }
    }

    /**
     * Валидировать callback payload
     */
    public static function validateCallbackPayload(string $payload): void
    {
        $length = strlen($payload);
        if ($length > Limits::BUTTON_CALLBACK_PAYLOAD_MAX_LENGTH) {
            throw new MaxValidationException(
                "Callback payload is too long ({$length} bytes). Maximum: " . Limits::BUTTON_CALLBACK_PAYLOAD_MAX_LENGTH
            );
        }
    }

    /**
     * Валидировать название чата
     */
    public static function validateChatTitle(string $title): void
    {
        if (empty($title)) {
            throw new MaxValidationException('Chat title cannot be empty');
        }

        $length = mb_strlen($title);
        if ($length > Limits::CHAT_TITLE_MAX_LENGTH) {
            throw new MaxValidationException(
                "Chat title is too long ({$length} characters). Maximum: " . Limits::CHAT_TITLE_MAX_LENGTH
            );
        }
    }

    /**
     * Валидировать описание чата
     */
    public static function validateChatDescription(string $description): void
    {
        $length = mb_strlen($description);
        if ($length > Limits::CHAT_DESCRIPTION_MAX_LENGTH) {
            throw new MaxValidationException(
                "Chat description is too long ({$length} characters). Maximum: " . Limits::CHAT_DESCRIPTION_MAX_LENGTH
            );
        }
    }

    /**
     * Валидировать клавиатуру
     */
    public static function validateKeyboard(array $buttons): void
    {
        $rowCount = count($buttons);
        if ($rowCount > Limits::KEYBOARD_ROWS_MAX) {
            throw new MaxValidationException(
                "Too many rows in keyboard ({$rowCount}). Maximum: " . Limits::KEYBOARD_ROWS_MAX
            );
        }

        $totalButtons = 0;
        foreach ($buttons as $rowIndex => $row) {
            $buttonsInRow = count($row);
            $totalButtons += $buttonsInRow;

            if ($buttonsInRow > Limits::KEYBOARD_BUTTONS_PER_ROW_MAX) {
                throw new MaxValidationException(
                    "Too many buttons in row {$rowIndex} ({$buttonsInRow}). Maximum: " . Limits::KEYBOARD_BUTTONS_PER_ROW_MAX
                );
            }

            // Проверка специальных кнопок
            $specialButtons = 0;
            foreach ($row as $button) {
                $type = $button['type'] ?? '';
                if (in_array($type, ['link', 'open_app', 'request_geo_location', 'request_contact'])) {
                    $specialButtons++;
                }
            }

            if ($specialButtons > Limits::KEYBOARD_SPECIAL_BUTTONS_PER_ROW_MAX) {
                throw new MaxValidationException(
                    "Too many special buttons (link, open_app, etc.) in row {$rowIndex} ({$specialButtons}). Maximum: " . Limits::KEYBOARD_SPECIAL_BUTTONS_PER_ROW_MAX
                );
            }
        }

        if ($totalButtons > Limits::KEYBOARD_BUTTONS_MAX) {
            throw new MaxValidationException(
                "Too many buttons in keyboard ({$totalButtons}). Maximum: " . Limits::KEYBOARD_BUTTONS_MAX
            );
        }
    }

    /**
     * Валидировать format (markdown или html)
     */
    public static function validateFormat(?string $format): void
    {
        if ($format === null) {
            return;
        }

        $allowedFormats = ['markdown', 'html'];
        if (!in_array(strtolower($format), $allowedFormats)) {
            throw new MaxValidationException(
                "Invalid format: {$format}. Allowed: " . implode(', ', $allowedFormats)
            );
        }
    }

    /**
     * Автоматически обрезать текст до допустимой длины
     */
    public static function truncateText(string $text, int $maxLength, string $suffix = '...'): string
    {
        if (mb_strlen($text) <= $maxLength) {
            return $text;
        }

        $suffixLength = mb_strlen($suffix);
        return mb_substr($text, 0, $maxLength - $suffixLength) . $suffix;
    }

    /**
     * Разбить длинный текст на несколько сообщений
     */
    public static function splitLongText(string $text, int $maxLength = null): array
    {
        $maxLength = $maxLength ?? Limits::MESSAGE_TEXT_MAX_LENGTH;
        
        if (mb_strlen($text) <= $maxLength) {
            return [$text];
        }

        $messages = [];
        $parts = explode("\n", $text);
        $currentMessage = '';

        foreach ($parts as $part) {
            $partLength = mb_strlen($part) + 1;
            
            if (mb_strlen($currentMessage) + $partLength <= $maxLength) {
                $currentMessage .= ($currentMessage ? "\n" : '') . $part;
            } else {
                if ($currentMessage) {
                    $messages[] = $currentMessage;
                }
                
                if (mb_strlen($part) > $maxLength) {
                    $chunks = mb_str_split($part, $maxLength);
                    foreach ($chunks as $chunk) {
                        $messages[] = $chunk;
                    }
                    $currentMessage = '';
                } else {
                    $currentMessage = $part;
                }
            }
        }

        if ($currentMessage) {
            $messages[] = $currentMessage;
        }

        return $messages;
    }
}

