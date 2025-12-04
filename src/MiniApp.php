<?php

namespace LetoceilingCoder\Max;

use LetoceilingCoder\Max\Exceptions\MaxValidationException;
use Illuminate\Support\Facades\Log;

/**
 * Класс для работы с Max Mini Apps
 * Документация: https://dev.max.ru/docs-miniapps
 */
class MiniApp
{
    protected string $secretKey;

    public function __construct(?string $secretKey = null)
    {
        $this->secretKey = $secretKey ?? config('services.max.secret_key');
        
        if (!$this->secretKey) {
            throw new MaxValidationException('Max secret key not configured');
        }
    }

    /**
     * Валидировать параметры запуска Mini App
     * 
     * @param string $params - Параметры из URL или headers
     * @return bool
     */
    public function validateParams(string $params): bool
    {
        try {
            parse_str($params, $data);
            
            if (!isset($data['hash'])) {
                Log::warning('Max MiniApp: hash not found');
                return false;
            }

            $hash = $data['hash'];
            unset($data['hash']);

            // Сортируем данные
            ksort($data);
            
            // Формируем строку для проверки
            $dataCheckString = [];
            foreach ($data as $key => $value) {
                $dataCheckString[] = $key . '=' . $value;
            }
            $dataCheckString = implode("\n", $dataCheckString);

            // Создаем hash для проверки
            $calculatedHash = hash_hmac('sha256', $dataCheckString, $this->secretKey);

            $isValid = hash_equals($calculatedHash, $hash);

            if (!$isValid) {
                Log::warning('Max MiniApp: invalid hash', [
                    'expected' => $calculatedHash,
                    'received' => $hash,
                ]);
            }

            return $isValid;

        } catch (\Exception $e) {
            Log::error('Max MiniApp validation error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Парсить параметры запуска
     * 
     * @param string $params
     * @return array
     */
    public function parseParams(string $params): array
    {
        parse_str($params, $data);
        
        // Декодируем JSON поля если есть
        if (isset($data['user'])) {
            $data['user'] = json_decode($data['user'], true);
        }
        
        return $data;
    }

    /**
     * Получить ID пользователя из параметров
     * 
     * @param string $params
     * @return int|null
     */
    public function getUserId(string $params): ?int
    {
        $data = $this->parseParams($params);
        return $data['user']['user_id'] ?? $data['user_id'] ?? null;
    }

    /**
     * Получить данные пользователя из параметров
     * 
     * @param string $params
     * @return array|null
     */
    public function getUser(string $params): ?array
    {
        $data = $this->parseParams($params);
        return $data['user'] ?? null;
    }

    /**
     * Валидировать и получить пользователя
     * Бросает исключение если данные невалидны
     * 
     * @param string $params
     * @return array
     * @throws MaxValidationException
     */
    public function validateAndGetUser(string $params): array
    {
        if (!$this->validateParams($params)) {
            throw new MaxValidationException('Invalid MiniApp params signature');
        }

        $user = $this->getUser($params);
        
        if (!$user) {
            throw new MaxValidationException('User data not found in params');
        }

        return $user;
    }

    /**
     * Создать URL для открытия Mini App
     * 
     * @param string $appId - ID приложения
     * @param array $params - Дополнительные параметры
     * @return string
     */
    public function createAppUrl(string $appId, array $params = []): string
    {
        $url = "https://max.ru/app/{$appId}";
        
        if (!empty($params)) {
            $queryString = http_build_query($params);
            $url .= '?' . $queryString;
        }
        
        return $url;
    }
}

