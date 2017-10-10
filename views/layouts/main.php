<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\components\ProfileWidget;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <link rel="icon" type="image/png" href="/favicon.png">
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => 'TimeTracker',
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
    ?>

    <button id="filters" type="button" class="btn btn-link" style="font-size: medium; outline: none;">
        Filters
    </button>

    <?php
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => [
//            ['label' => 'Home', 'url' => ['/site/index']],
//            ['label' => 'About', 'url' => ['/site/about']],
//            ['label' => 'Contact', 'url' => ['/site/contact']],
            Yii::$app->user->isGuest ? (
                ['label' => 'Login', 'url' => ['/login']]
            ) : (
                '<li>'
                . Html::beginForm(['/logout'], 'post')
                . ProfileWidget::widget()
                . Html::submitButton(
                    'Logout (' . Yii::$app->user->identity->email . ')',
                    ['class' => 'btn btn-link logout']
                )
                . Html::endForm()
                . '</li>'
            )
        ],
    ]);
    NavBar::end();
    ?>

    <div class="navbar navbar-inverse second-nav nav-fixed" role="navigation" style="display: none;">
        <div class="container">

            <?php if (!Yii::$app->user->isGuest): ?>
                <div>

                    <div class="navbar-form">
                        <div class="row">
                            <div class="col">

                                <form role="form">

                                    <select id="months" class="selectpicker" style="outline: none;">
                                        <option>Nothing selected</option>
                                        <option>January</option>
                                        <option>February</option>
                                        <option>March</option>
                                        <option>April</option>
                                        <option>May</option>
                                        <option>June</option>
                                        <option>July</option>
                                        <option>August</option>
                                        <option>September</option>
                                        <option>October</option>
                                        <option>November</option>
                                        <option>December</option>
                                    </select>

                                    <div class="input-group">
                                            <span class="input-group-addon" style="display:none;">
                                                <input type="radio" name="date" id="date-start" checked="checked">
                                            </span>
                                        <input type="text" class="form-control" id="datepicker-start" />
                                    </div><!-- /input-group -->

                                    <div class="input-group">
                                            <span class="input-group-addon" style="display:none;">
                                                <input type="radio" id="date-end" name="date">
                                            </span>
                                        <input type="text" class="form-control" id="datepicker-end" />
                                    </div><!-- /input-group -->

                                    <select id="project" class="selectpicker">
                                    </select>
                                    <select id="task" class="selectpicker hide">
                                    </select>

                                    <div class="btn-group navbar-right">

                                        <div class="btn-group-btn">
                                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                                Users List <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu pull-right" id="users-list">
                                            </ul>
                                        </div><!-- /btn-group -->
                                    </div><!-- /input-group -->


                                </form>

                            </div>
                        </div><!-- /.col-lg-6 -->
                    </div>

                </div>
            <?php endif; ?>

        </div>
    </div>

    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; ZapleoSoft <?= date('Y') ?></p>

        <p class="pull-right"><?= Yii::powered() ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
