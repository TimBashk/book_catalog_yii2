<?php
use yii\grid\GridView;
use yii\helpers\Html;

/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Список книг';
?>

<h1><?= Html::encode($this->title) ?></h1>

<p>
    <?php if (!Yii::$app->user->isGuest): ?>
        <?= Html::a('Добавить книгу', ['book/create'], ['class' => 'btn btn-success']) ?>
    <?php endif; ?>
</p>

<?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => array_filter([
                ['class' => 'yii\grid\SerialColumn'],

                [
                        'attribute' => 'cover_path',
                        'label' => 'Обложка',
                        'format' => 'html',
                        'value' => function ($model) {
                            $file = $model->cover_path
                                    ? Yii::getAlias('@web/uploads/' . $model->cover_path)
                                    : Yii::getAlias('@web/uploads/no-cover.png');

                            return Html::img($file, [
                                    'style' => 'width:80px; height:80px; object-fit:cover; border:1px solid #ccc;',
                            ]);
                        },
                        'contentOptions' => ['style' => 'width: 100px; text-align: center;'],
                ],
                'title',
                'year',
                'isbn',
                [
                        'attribute' => 'description',
                        'value' => function ($model) {
                            return mb_substr($model->description, 0, 80) . (mb_strlen($model->description) > 80 ? '...' : '');
                        },
                ],

                // Кнопки действий (просмотр, редактирование, удаление)
                !Yii::$app->user->isGuest ? [
                        'class' => 'yii\grid\ActionColumn',
                        'controller' => 'book',
                        'header' => 'Действия',
                        'template' => '{view} {update} {delete}',
                        'buttons' => [
                                'view' => function ($url, $model) {
                                    return Html::a('<i class="fas fa-eye"></i>', $url, [
                                            'title' => 'Просмотр',
                                            'class' => 'btn btn-sm btn-outline-primary',
                                    ]);
                                },
                                'update' => function ($url, $model) {
                                    return Html::a('<i class="fas fa-edit"></i>', $url, [
                                            'title' => 'Редактировать',
                                            'class' => 'btn btn-sm btn-outline-success',
                                    ]);
                                },
                                'delete' => function ($url, $model) {
                                    return Html::a('<i class="fas fa-trash"></i>', $url, [
                                            'title' => 'Удалить',
                                            'class' => 'btn btn-sm btn-outline-danger',
                                            'data-confirm' => 'Вы уверены, что хотите удалить эту книгу?',
                                            'data-method' => 'post',
                                    ]);
                                },
                        ],
                ] : [
                        'class' => 'yii\grid\ActionColumn',
                        'controller' => 'book',
                        'header' => 'Действия',
                        'template' => '{view}', // только просмотр
                ],
        ]),
]); ?>
