<?php

namespace App\Services;

use App\Models\ApiLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TranslationService
{
    protected string $provider;

    protected string $apiKey;

    public function __construct()
    {
        $this->provider = config('services.translation.provider', 'google');
        $this->apiKey = config('services.translation.api_key', '');
    }

    /**
     * 텍스트 번역 (러시아어 -> 한국어/영어)
     */
    public function translate(string $text, string $targetLang = 'ko', string $sourceLang = 'ru'): ?string
    {
        if (empty($text)) {
            return null;
        }

        return match ($this->provider) {
            'google' => $this->translateWithGoogle($text, $targetLang, $sourceLang),
            'deepl' => $this->translateWithDeepL($text, $targetLang, $sourceLang),
            'yandex' => $this->translateWithYandex($text, $targetLang, $sourceLang),
            default => $this->translateWithGoogle($text, $targetLang, $sourceLang),
        };
    }

    /**
     * Google Translate API 사용
     */
    protected function translateWithGoogle(string $text, string $targetLang, string $sourceLang): ?string
    {
        $startTime = microtime(true);
        $endpoint = 'https://translation.googleapis.com/language/translate/v2';

        try {
            $response = Http::get($endpoint, [
                'key' => $this->apiKey,
                'q' => $text,
                'source' => $sourceLang,
                'target' => $targetLang,
                'format' => 'text',
            ]);

            $duration = (int) ((microtime(true) - $startTime) * 1000);

            if ($response->successful()) {
                $data = $response->json();
                $translated = $data['data']['translations'][0]['translatedText'] ?? null;

                $this->logApiCall(
                    'GET',
                    $endpoint,
                    compact('text', 'targetLang', 'sourceLang'),
                    $data,
                    $response->status(),
                    'success',
                    null,
                    $duration
                );

                return $translated;
            }

            $this->logApiCall(
                'GET',
                $endpoint,
                compact('text', 'targetLang', 'sourceLang'),
                $response->body(),
                $response->status(),
                'failed',
                'Translation failed',
                $duration
            );

            return null;
        } catch (\Exception $e) {
            $duration = (int) ((microtime(true) - $startTime) * 1000);

            $this->logApiCall(
                'GET',
                $endpoint,
                compact('text', 'targetLang', 'sourceLang'),
                null,
                null,
                'error',
                $e->getMessage(),
                $duration
            );

            Log::error('Google Translation Error: '.$e->getMessage());

            return null;
        }
    }

    /**
     * DeepL API 사용
     */
    protected function translateWithDeepL(string $text, string $targetLang, string $sourceLang): ?string
    {
        $startTime = microtime(true);
        $endpoint = 'https://api-free.deepl.com/v2/translate';

        try {
            $response = Http::asForm()->post($endpoint, [
                'auth_key' => $this->apiKey,
                'text' => $text,
                'source_lang' => strtoupper($sourceLang),
                'target_lang' => strtoupper($targetLang),
            ]);

            $duration = (int) ((microtime(true) - $startTime) * 1000);

            if ($response->successful()) {
                $data = $response->json();
                $translated = $data['translations'][0]['text'] ?? null;

                $this->logApiCall(
                    'POST',
                    $endpoint,
                    compact('text', 'targetLang', 'sourceLang'),
                    $data,
                    $response->status(),
                    'success',
                    null,
                    $duration
                );

                return $translated;
            }

            $this->logApiCall(
                'POST',
                $endpoint,
                compact('text', 'targetLang', 'sourceLang'),
                $response->body(),
                $response->status(),
                'failed',
                'Translation failed',
                $duration
            );

            return null;
        } catch (\Exception $e) {
            $duration = (int) ((microtime(true) - $startTime) * 1000);

            $this->logApiCall(
                'POST',
                $endpoint,
                compact('text', 'targetLang', 'sourceLang'),
                null,
                null,
                'error',
                $e->getMessage(),
                $duration
            );

            Log::error('DeepL Translation Error: '.$e->getMessage());

            return null;
        }
    }

    /**
     * Yandex Translate API 사용
     */
    protected function translateWithYandex(string $text, string $targetLang, string $sourceLang): ?string
    {
        $startTime = microtime(true);
        $endpoint = 'https://translate.api.cloud.yandex.net/translate/v2/translate';

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Api-Key '.$this->apiKey,
            ])->post($endpoint, [
                'texts' => [$text],
                'targetLanguageCode' => $targetLang,
                'sourceLanguageCode' => $sourceLang,
            ]);

            $duration = (int) ((microtime(true) - $startTime) * 1000);

            if ($response->successful()) {
                $data = $response->json();
                $translated = $data['translations'][0]['text'] ?? null;

                $this->logApiCall(
                    'POST',
                    $endpoint,
                    compact('text', 'targetLang', 'sourceLang'),
                    $data,
                    $response->status(),
                    'success',
                    null,
                    $duration
                );

                return $translated;
            }

            $this->logApiCall(
                'POST',
                $endpoint,
                compact('text', 'targetLang', 'sourceLang'),
                $response->body(),
                $response->status(),
                'failed',
                'Translation failed',
                $duration
            );

            return null;
        } catch (\Exception $e) {
            $duration = (int) ((microtime(true) - $startTime) * 1000);

            $this->logApiCall(
                'POST',
                $endpoint,
                compact('text', 'targetLang', 'sourceLang'),
                null,
                null,
                'error',
                $e->getMessage(),
                $duration
            );

            Log::error('Yandex Translation Error: '.$e->getMessage());

            return null;
        }
    }

    /**
     * API 호출 로그 기록
     */
    protected function logApiCall(
        string $method,
        string $endpoint,
        ?array $requestData,
        $responseData,
        ?int $statusCode,
        string $status,
        ?string $errorMessage,
        int $duration
    ): void {
        ApiLog::create([
            'service' => 'translation',
            'method' => $method,
            'endpoint' => $endpoint,
            'request_data' => $requestData ? json_encode($requestData) : null,
            'response_data' => is_array($responseData) ? json_encode($responseData) : $responseData,
            'status_code' => $statusCode,
            'status' => $status,
            'error_message' => $errorMessage,
            'duration_ms' => $duration,
        ]);
    }
}
