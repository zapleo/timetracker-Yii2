<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\SignUpForm */
/* @var $login_model app\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Sign up';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="site-login">
    <h1>Hi, <?= $model->user_jira['displayName'] ?>!</h1>

    <p>Please fill out the following fields to sign up:</p>

    <?php $form = ActiveForm::begin([
        'id' => 'signup-form',
        'action' => '/site/sign-up',
        'layout' => 'horizontal',
        'fieldConfig' => [
            'template' => "{label}\n<div class=\"col-lg-3\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
            'labelOptions' => ['class' => 'col-lg-1 control-label'],
        ],
    ]); ?>

        <p style="text-align: center;">
            <?= Html::img($model->user_photo, ['class' => 'img-rounded', 'alt' => 'User photo']) ?>
        </p>

        <?= $form->field($model, 'email')->input('email', ['value' => $model->user_jira['emailAddress']]) ?>
        <?= $form->field($model, 'password')->passwordInput(['value' => $model->password]) ?>
        <?= $form->field($model, 'first_name')->input('text', ['value' => $model->first_name, 'disabled' => 'disabled']) ?>
        <?= $form->field($model, 'last_name')->input('text', ['value' => $model->last_name, 'disabled' => 'disabled']) ?>
        <?= $form->field($model, 'phone')->input('text', ['placeholder' => 'Your phone']) ?>
        <?= $form->field($model, 'skype')->input('text', ['placeholder' => 'Your skype']) ?>

        <div class="form-group">
            <div class="col-lg-offset-1 col-lg-11">
                <?= Html::submitButton('Sign up', ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
            </div>
        </div>

    <?php ActiveForm::end(); ?>

</div>
