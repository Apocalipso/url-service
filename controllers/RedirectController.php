<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use app\models\Url;

class RedirectController extends Controller
{
    public function actionIndex($code)
    {
        $model = Url::findOne(['short_code' => $code]);
        if (!$model) {
            throw new NotFoundHttpException('Ссылка не найдена');
        }

        // Логируем переход
        $ip = Yii::$app->request->userIP;
        $model->incrementClick($ip);

        return $this->redirect($model->original_url);
    }
}