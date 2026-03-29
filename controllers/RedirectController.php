<?php
namespace app\controllers;

use app\services\UrlService;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class RedirectController extends Controller
{
    private UrlService $urlService;

    public function init()
    {
        parent::init();
        $this->urlService = new UrlService();
    }

    public function actionIndex($code)
    {
        $originalUrl = $this->urlService->resolveAndTrack($code, Yii::$app->request->userIP);
        if (!$originalUrl) {
            throw new NotFoundHttpException('Ссылка не найдена');
        }
        return $this->redirect($originalUrl);
    }
}