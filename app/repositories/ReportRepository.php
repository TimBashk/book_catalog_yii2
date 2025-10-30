<?php

namespace app\repositories;

use app\models\Author;
use yii\data\ArrayDataProvider;
use Yii;
class ReportRepository
{
    public function getTopAuthorsByYear(int $year): ArrayDataProvider
    {
        $rows = Yii::$app->db->createCommand("
            SELECT a.id, a.full_name, COUNT(b.id) AS book_count
            FROM {{%author}} a
            JOIN {{%book_author}} ba ON ba.author_id = a.id
            JOIN {{%book}} b ON b.id = ba.book_id
            WHERE b.year = :year
            GROUP BY a.id, a.full_name
            ORDER BY book_count DESC
            LIMIT 10
        ")->bindValue(':year', $year)->queryAll();

        return new ArrayDataProvider([
            'allModels' => $rows,
            'pagination' => false,
            'sort' => [
                'attributes' => ['full_name', 'book_count'],
                'defaultOrder' => ['book_count' => SORT_DESC],
            ],
        ]);
    }
}