<?php

namespace app\controllers;

use app\models\forms\LinkForm;
use app\models\Link;
use app\repositories\LinkRepository;
use Yii;
use yii\base\Exception;
use yii\rest\Controller;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class LinkController extends Controller
{
    public function __construct($id, $module, private readonly LinkRepository $linkRepository, $config = [])
    {
        parent::__construct($id, $module, $config);
    }

    public function actionHealth(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return [
            'status' => 'ok',
            'service' => 'link-service',
            'timestamp' => time(),
            'version' => '1.0'
        ];
    }

    /**
     * POST /shorten
     * @throws Exception
     */
    public function actionShorten(): array
    {
        $data =  json_decode(Yii::$app->request->getRawBody(), true);
        $form = new LinkForm();

        if (!$form->load($data, '') || !$form->validate()) {
            throw new BadRequestHttpException(implode('; ', $form->getErrorSummary(true)));
        }

        if (!$form->short_code) {
            $form->short_code = Yii::$app->getSecurity()->generateRandomString(6);
        }

        $model = Link::create($form->toDto());
        $this->linkRepository->save($model);

        return [
            'short_url' => Yii::$app->request->hostInfo . '/s/' . $form->short_code,
            'short_code' => $model->short_code,
            'original_url' => $model->original_url,
            'clicks' => $model->clicks,
        ];
    }

    /**
     * GET /s/<code>
     * @throws NotFoundHttpException
     */
    public function actionRedirect(string $code): Response
    {
        $link = Link::findOne(['short_code' => $code]);

        if (!$link) {
            throw new NotFoundHttpException('Url not found');
        }

        $link->updateCounters(['clicks' => 1]);

        return $this->redirect($link->original_url);
    }
}