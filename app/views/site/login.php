<?php
use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

/** @var $model app\models\LoginForm */

$this->title = 'Вход';
?>
<h1><?= Html::encode($this->title) ?></h1>

<?php $form = ActiveForm::begin(['id' => 'login-form']); ?>
<?= $form->field($model, 'username')->textInput(['autofocus' => true]) ?>
<?= $form->field($model, 'password')->passwordInput() ?>
<div class="form-group">
    <?= Html::submitButton('Войти', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
</div>
<?php ActiveForm::end(); ?>
