<?php

/* @var $this yii\web\View */

$this->title = 'Time Tracker';

$this->registerCssFile('@web/css/lib/bootstrap-datetimepicker.css', ['depends' => 'app\assets\AppAsset']);
$this->registerCssFile('@web/css/lib/jquery.mCustomScrollbar.min.css', ['depends' => 'app\assets\AppAsset']);
$this->registerCssFile('@web/css/lib/bootstrap-select.min.css', ['depends' => 'app\assets\AppAsset']);
$this->registerCssFile('@web/css/main.css', ['depends' => 'app\assets\AppAsset']);

$this->registerJsFile('@web/js/lib/moment-with-locales.min.js', ['depends' => 'app\assets\AppAsset']);
$this->registerJsFile('@web/js/lib/bootstrap-datetimepicker.min.js', ['depends' => 'app\assets\AppAsset']);
$this->registerJsFile('@web/js/lib/jquery.mCustomScrollbar.concat.min.js', ['depends' => 'app\assets\AppAsset']);
$this->registerJsFile('@web/js/lib/bootstrap-select.min.js', ['depends' => 'app\assets\AppAsset']);
$this->registerJsFile('@web/js/main.js?t='.time(), ['depends' => 'app\assets\AppAsset']);
?>
<div class="site-index">

    <!-- Jumbotron -->
    <div class="jumbotron" id="main">

        <h1>Time Tracker!</h1>
        <p class="lead">
            <?= !Yii::$app->user->isGuest ? 'Hello <b>'.Yii::$app->user->identity->first_name.' '.Yii::$app->user->identity->last_name.'</b>!' : '' ?>
            This is Time Tracker. Select users at the top right.
        </p>
        <?= Yii::$app->user->isGuest ? '<p><a class="btn btn-lg btn-success" href="/login" role="button">Get started today</a></p>' : '' ?>
    </div>
</div>

<div class="work-logs"></div>

<?= $this->render('modal') ?>