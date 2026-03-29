<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class Url extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%url}}';
    }

    public function rules()
    {
        return [
            [['original_url'], 'required'],
            ['original_url', 'url', 'defaultScheme' => 'http'],
            ['short_code', 'string', 'max' => 10],
            ['clicks', 'integer'],
        ];
    }

    public function getLogs()
    {
        return $this->hasMany(UrlLog::class, ['url_id' => 'id']);
    }

    // Генерация уникального короткого кода
    public static function generateShortCode($length = 6)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        do {
            $code = '';
            for ($i = 0; $i < $length; $i++) {
                $code .= $characters[random_int(0, $charactersLength - 1)];
            }
        } while (self::findOne(['short_code' => $code]));
        return $code;
    }

    // Проверка доступности URL
    public static function isUrlReachable($url)
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

    // Увеличение счётчика и логирование IP
    public function incrementClick($ip)
    {
        $this->updateCounters(['clicks' => 1]);

        $log = new UrlLog();
        $log->url_id = $this->id;
        $log->ip = $ip;
        $log->save();
    }
}