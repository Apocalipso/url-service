<?php
namespace app\services;

use yii\validators\UrlValidator;

class UrlCheckService
{
    /**
     * Валидация формата URL
     */
    public function validateFormat(string $url): bool
    {
        $validator = new UrlValidator();
        return $validator->validate($url);
    }

    /**
     * Проверка доступности (HTTP 2xx/3xx)
     */
    public function isReachable(string $url): bool
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $httpCode >= 200 && $httpCode < 400;
    }
}