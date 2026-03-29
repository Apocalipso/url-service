<?php
namespace app\services;

use app\repositories\UrlRepository;
use app\models\Url;

class UrlService
{
    private UrlRepository $repository;
    private UrlCheckService $checker;
    private QrService $qrService;

    public function __construct()
    {
        $this->repository = new UrlRepository();
        $this->checker = new UrlCheckService();
        $this->qrService = new QrService();
    }

    /**
     * Создаёт короткую ссылку (или возвращает существующую)
     * @return array ['short_url' => string, 'qr_data_uri' => string]
     * @throws \Exception если URL невалидный или недоступен
     */
    public function shorten(string $originalUrl): array
    {
        if (!$this->checker->validateFormat($originalUrl)) {
            throw new \Exception('Неверный формат URL');
        }
        if (!$this->checker->isReachable($originalUrl)) {
            throw new \Exception('Данный URL не доступен');
        }

        $urlModel = $this->repository->findByOriginal($originalUrl);
        if (!$urlModel) {
            $urlModel = new Url();
            $urlModel->original_url = $originalUrl;
            $urlModel->short_code = $this->generateUniqueCode();
            $this->repository->save($urlModel);
        }

        $shortUrl = \Yii::$app->urlManager->createAbsoluteUrl([$urlModel->short_code]);
        $qrDataUri = $this->qrService->generateDataUri($shortUrl);

        return [
            'short_url' => $shortUrl,
            'qr_data_uri' => $qrDataUri,
        ];
    }

    /**
     * Обработка перехода по короткой ссылке
     * @return string|null оригинальный URL или null, если не найден
     */
    public function resolveAndTrack(string $code, string $ip): ?string
    {
        $urlModel = $this->repository->findByCode($code);
        if (!$urlModel) {
            return null;
        }
        $this->repository->incrementClick($urlModel, $ip);
        return $urlModel->original_url;
    }

    private function generateUniqueCode(int $length = 6): string
    {
        $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        do {
            $code = '';
            for ($i = 0; $i < $length; $i++) {
                $code .= $chars[random_int(0, strlen($chars) - 1)];
            }
        } while ($this->repository->findByCode($code));
        return $code;
    }
}