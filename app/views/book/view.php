<?php
use yii\helpers\Html;

/** @var app\models\Book $model */

$this->title = $model->title;
?>

<h1><?= Html::encode($model->title) ?></h1>

<div style="display:flex; gap:20px; align-items:flex-start;">
    <div>
        <?php
        $cover = $model->cover_path
                ? Yii::getAlias('@web') . '/uploads/' . $model->cover_path
                : Yii::getAlias('@web') . '/uploads/no-cover.png';
        ?>
        <img src="<?= $cover ?>" alt="Обложка" style="max-width:200px; border:1px solid #ccc; border-radius:8px;">
    </div>

    <div>
        <p><strong>Год:</strong> <?= Html::encode($model->year) ?></p>
        <p><strong>ISBN:</strong> <?= Html::encode($model->isbn) ?></p>
        <p><strong>Описание:</strong> <?= nl2br(Html::encode($model->description)) ?></p>
    </div>
</div>

<hr>

<p>
    <?= Html::a('← Назад', Yii::$app->request->referrer ?: ['/book/index'], ['class' => 'btn btn-secondary']) ?>
    <?php if (!Yii::$app->user->isGuest): ?>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data-confirm' => 'Удалить книгу?',
                'data-method' => 'post',
        ]) ?>
    <?php endif; ?>
</p>
