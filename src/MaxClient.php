<?php

namespace LetoceilingCoder\Max;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use LetoceilingCoder\Max\Exceptions\MaxException;

/**
 * Базовый класс для работы с Max API
 * Документация: https://dev.max.ru/docs-api
 */
class MaxClient
{
    protected string $token;
    protected string $baseUrl = 'https://platform-api.max.ru/';

    public function __construct(?string $token = null)
    {
        $this->token = $token ?? config('max.token');
        
        if (!$this->token) {
            throw new MaxException('Max bot token not configured');
        }
    }

    /**
     * Выполнить GET запрос к Max API
     */
    protected function get(string $endpoint, array $parameters = []): array
    {
        return $this->request('GET', $endpoint, $parameters);
    }

    /**
     * Выполнить POST запрос к Max API
     */
    protected function post(string $endpoint, array $data = []): array
    {
        return $this->request('POST', $endpoint, $data);
    }

    /**
     * Выполнить PUT запрос к Max API
     */
    protected function put(string $endpoint, array $data = []): array
    {
        return $this->request('PUT', $endpoint, $data);
    }

    /**
     * Выполнить PATCH запрос к Max API
     */
    protected function patch(string $endpoint, array $data = []): array
    {
        return $this->request('PATCH', $endpoint, $data);
    }

    /**
     * Выполнить DELETE запрос к Max API
     */
    protected function delete(string $endpoint, array $parameters = []): array
    {
        return $this->request('DELETE', $endpoint, $parameters);
    }

    /**
     * Выполнить запрос к Max API
     */
    protected function request(string $method, string $endpoint, array $data = []): array
    {
        $url = $this->baseUrl . ltrim($endpoint, '/');

        try {
            $request = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => $this->token,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ]);

            $response = match(strtoupper($method)) {
                'GET' => $request->get($url, $data),
                'POST' => $request->post($url, $data),
                'PUT' => $request->put($url, $data),
                'PATCH' => $request->patch($url, $data),
                'DELETE' => $request->delete($url, $data),
                default => throw new MaxException("Unsupported HTTP method: {$method}"),
            };

            $statusCode = $response->status();

            // Проверка HTTP статуса
            if ($statusCode >= 400) {
                $error = $response->json();
                $errorMessage = $error['message'] ?? $error['error'] ?? 'Unknown error';
                
                Log::error('Max API error', [
                    'method' => $method,
                    'endpoint' => $endpoint,
                    'status' => $statusCode,
                    'error' => $errorMessage,
                ]);

                throw new MaxException("[{$statusCode}] {$errorMessage}");
            }

            return $response->json() ?? [];

        } catch (MaxException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Max API request failed', [
                'method' => $method,
                'endpoint' => $endpoint,
                'error' => $e->getMessage(),
            ]);
            throw new MaxException('Failed to execute Max API request: ' . $e->getMessage());
        }
    }

    /**
     * Загрузить файл
     */
    protected function uploadFile(array $files = []): array
    {
        $url = $this->baseUrl . 'upload';

        try {
            $request = Http::timeout(60)
                ->withHeaders([
                    'Authorization' => $this->token,
                ])
                ->asMultipart();

            foreach ($files as $key => $file) {
                if (isset($file['content'])) {
                    $request->attach($key, $file['content'], $file['filename'] ?? 'file');
                } elseif (isset($file['path'])) {
                    $request->attach($key, fopen($file['path'], 'r'), $file['filename'] ?? basename($file['path']));
                }
            }

            $response = $request->post($url);

            if ($response->failed()) {
                throw new MaxException('File upload failed: ' . ($response->json()['error'] ?? 'Unknown error'));
            }

            return $response->json() ?? [];

        } catch (\Exception $e) {
            throw new MaxException('Failed to upload file: ' . $e->getMessage());
        }
    }

    /**
     * Получить информацию о текущем боте
     */
    public function getMe(): array
    {
        return $this->get('bots/me');
    }

    /**
     * Логирование для отладки
     */
    protected function log(string $message, array $context = []): void
    {
        Log::info('[Max] ' . $message, $context);
    }
}

