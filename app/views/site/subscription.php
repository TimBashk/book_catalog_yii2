<?php
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var array $subscribedIds */

$this->title = 'Подписки';
$this->params['breadcrumbs'][] = $this->title;
?>

<h1><?= Html::encode($this->title) ?></h1>

<p>Отметьте авторов, на которых вы хотите подписаться.</p>

<?php $form = ActiveForm::begin([
        'id' => 'subscription-form',
        'action' => Url::to(['site/save-subscriptions']),
        'method' => 'post',
]); ?>

<?php if (Yii::$app->user->isGuest): ?>
    <div class="mb-3" style="max-width: 300px;">
        <label for="contact" class="form-label">Контакт (телефон):</label>
        <input type="text" id="contact" name="contact" class="form-control"
               placeholder="+7XXXXXXXXXX или 8XXXXXXXXXX"
               pattern="^(\+7|8)\d{10}$"
               value="<?= Html::encode($contact ?? '') ?>"
               required
               title="Введите номер телефона в формате +7XXXXXXXXXX или 8XXXXXXXXXX">
    </div>
<?php endif; ?>

<?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                [
                        'attribute' => 'full_name',
                        'label' => 'Автор',
                ],
                [
                        'label' => 'Подписка',
                        'format' => 'raw',
                        'value' => function ($model) use ($subscribedIds) {
                            $checked = in_array($model->id, $subscribedIds);
                            return Html::checkbox('subscriptions[]', $checked, [
                                    'value' => $model->id,
                                    'class' => 'author-checkbox',
                            ]);
                        },
                        'contentOptions' => ['style' => 'text-align:center; width: 100px;'],
                ],
        ],
]); ?>

<div style="margin-top: 10px;">
    <?= Html::submitButton('Сохранить подписки', ['class' => 'btn btn-primary']) ?>
</div>

<?php ActiveForm::end(); ?>



