<?php

namespace app\controllers;

use yii\rest\Controller;
use yii\web\Response;

class LinkController extends Controller
{
    public function actionHealth(): array
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return [
            'status' => 'ok',
            'service' => 'link-service',
            'timestamp' => time(),
            'version' => '1.0'
        ];
    }
}