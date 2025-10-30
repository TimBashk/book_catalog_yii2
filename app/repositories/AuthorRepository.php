<?php

namespace app\repositories;

use app\models\Author;
use yii\data\ActiveDataProvider;

class AuthorRepository
{
    public function getDataProvider(): ActiveDataProvider
    {
        return new ActiveDataProvider([
            'query' => Author::find(),
            'pagination' => ['pageSize' => 10],
            'sort' => ['defaultOrder' => ['full_name' => SORT_ASC]],
        ]);
    }
}