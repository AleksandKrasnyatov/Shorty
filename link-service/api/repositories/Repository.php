<?php

namespace app\repositories;

use DomainException;
use Throwable;
use yii\db\ActiveRecord;
use yii\db\Exception;
use yii\web\NotFoundHttpException;

abstract class Repository
{
    public ActiveRecord $model;

    abstract public function __construct();

    public function get(int $id, array $with = []): ?ActiveRecord
    {
        return $this->model::find()
            ->where(['id' => $id])
            ->with($with)
            ->one();
    }

    /**
     * @throws NotFoundHttpException
     */
    public function find(int $id, array $with = []): ActiveRecord
    {
        $model = $this->model::find()
            ->where(['id' => $id])
            ->with($with)
            ->one();

        if (!$model) {
            throw new NotFoundHttpException('Model is not found.');
        }

        return $model;
    }


    /**
     * @throws Exception
     */
    public function save(ActiveRecord $record): void
    {
        if (!$record->save()) {
            throw new DomainException('Saving error.');
        }
    }

    /**
     * @throws Throwable
     */
    public function delete(ActiveRecord $record): void
    {
        if (!$record->delete()) {
            throw new DomainException('Deleting error.');
        }
    }
}