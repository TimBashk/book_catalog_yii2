<?php
use yii\helpers\Html;

/** @var app\models\Book $model */

$this->title = $model->title;
?>

<h1><?= Html::encode($model->title) ?></h1>

<p><strong>Год:</strong> <?= Html::encode($model->year) ?></p>
<p><strong>ISBN:</strong> <?= Html::encode($model->isbn) ?></p>
<p><strong>Описание:</strong> <?= nl2br(Html::encode($model->description)) ?></p>

<?php if ($model->cover_path): ?>
    <p><img src="<?= Yii::getAlias('@web/uploads/' . $model->cover_path) ?>" style="max-width:200px;"></p>
<?php endif; ?>

<p>
    <?= Html::a('Назад', ['index'], ['class' => 'btn btn-secondary']) ?>
    <?php if (!Yii::$app->user->isGuest): ?>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data-confirm' => 'Удалить книгу?',
            'data-method' => 'post',
        ]) ?>
    <?php endif; ?>
</p>
