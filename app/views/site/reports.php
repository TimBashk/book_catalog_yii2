<?php
use yii\grid\GridView;
use yii\helpers\Html;

/** @var yii\data\ArrayDataProvider $dataProvider */
/** @var int $year */

$this->title = 'Отчет — ТОП 10 авторов';
?>

<h1><?= Html::encode($this->title) ?></h1>

<div class="mb-3">
    <form method="get" action="">
        <label>Выберите год:</label>
        <input type="number" name="year" value="<?= Html::encode($year) ?>" min="900" max="<?= date('Y') ?>" class="form-control" style="width: 200px; display:inline-block;">
        <button type="submit" class="btn btn-primary">Показать</button>
    </form>
</div>

<?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                [
                        'attribute' => 'full_name',
                        'label' => 'Автор',
                        'format' => 'text',
                ],
                [
                        'attribute' => 'book_count',
                        'label' => 'Количество книг',
                        'contentOptions' => ['style' => 'text-align:center;'],
                ],
        ],
]); ?>

