<?php

namespace app\repositories;

use app\models\Book;
use Yii;
use yii\web\UploadedFile;
use yii\web\NotFoundHttpException;

class BookRepository
{
    public function getDataProvider()
    {
        return new \yii\data\ActiveDataProvider([
            'query' => Book::find()->orderBy(['created_at' => SORT_DESC]),
            'pagination' => ['pageSize' => 10],
        ]);
    }

    public function findModel($id): Book
    {
        if (($model = Book::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Книга не найдена.');
    }

    public function create(Book $model): bool
    {
        $model->created_at = time();
        $model->updated_at = time();

        $this->handleUpload($model);

        return $model->save();
    }

    public function update(Book $model): bool
    {
        $model->updated_at = time();

        $this->handleUpload($model);

        return $model->save();
    }

    public function delete($id): bool
    {
        $model = $this->findModel($id);
        return (bool)$model->delete();
    }

    private function handleUpload(Book $model): void
    {
        $file = UploadedFile::getInstance($model, 'cover_path');
        if ($file) {
            $fileName = uniqid() . '.' . $file->extension;
            $uploadDir = Yii::getAlias('@webroot/uploads/');
            $file->saveAs($uploadDir . $fileName);
            $model->cover_path = $fileName;
        }
    }
}