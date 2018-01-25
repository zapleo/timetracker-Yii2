<?php
/* @var $this yii\web\View */

$this->registerCssFile('@web/css/lib/bootstrap-datetimepicker.css', ['depends' => 'app\assets\AppAsset']);
$this->registerCssFile('@web/css/manual_time.css?t='.time(), ['depends' => 'app\assets\AppAsset']);

$this->registerJsFile('@web/js/lib/moment-with-locales.min.js', ['depends' => 'app\assets\AppAsset']);
$this->registerJsFile('@web/js/lib/bootstrap-datetimepicker.min.js', ['depends' => 'app\assets\AppAsset']);
$this->registerJsFile('@web/js/manual_time.js?t='.time(), ['depends' => 'app\assets\AppAsset']);

$this->title = 'Edit manual time';
?>

<div class="wl">
    <div class="jumbotron">
        <h1>Edit manual time</h1>
        <p>
            Выберите диапазон часов Manual Time.<?= (Yii::$app->user->identity->isAdmin() ? '' : ' После одобрения Администратором время появится на сайте.') ?>
        </p>
    </div>
    <div class="row well">
        <?= $this->render('_form', [
            'model' => $model,
            'users' => $users
        ]) ?>
    </div>
</div>