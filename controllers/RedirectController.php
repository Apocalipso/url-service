<?php
namespace app\controllers;

use app\services\UrlService;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class RedirectController extends Controller
{
    private UrlService $urlService;

    public function __construct($id, $module, UrlService $urlService, $config = [])
    {
        $this->urlService = $urlService;
        parent::__construct($id, $module, $config);
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