<?php
namespace app\repositories;

use app\models\Url;

class UrlRepository
{
    public function findByCode(string $code): ?Url
    {
        return Url::findOne(['short_code' => $code]);
    }

    public function findByOriginal(string $url): ?Url
    {
        return Url::findOne(['original_url' => $url]);
    }

    public function save(Url $url): bool
    {
        return $url->save();
    }

    public function incrementClick(Url $url, string $ip): void
    {
        $url->updateCounters(['clicks' => 1]);
        $log = new \app\models\UrlLog();
        $log->url_id = $url->id;
        $log->ip = $ip;
        $log->save();
    }
}