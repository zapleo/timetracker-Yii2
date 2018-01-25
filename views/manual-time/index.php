<?php
/* @var $this yii\web\View */

use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\widgets\LinkPager;

$this->title = 'Manual time';

$this->registerCssFile('@web/css/callout.css', ['depends' => 'app\assets\AppAsset']);
$this->registerCssFile('@web/css/manual_time.css?t='.time(), ['depends' => 'app\assets\AppAsset']);

$this->registerJsFile('@web/js/lib/moment-with-locales.min.js', ['depends' => 'app\assets\AppAsset']);
$this->registerJsFile('@web/js/convert_time.js?t='.time(), ['depends' => 'app\assets\AppAsset']);

$status = [
    \app\models\ManualTime::STATUS_PENDING => 'warning',
    \app\models\ManualTime::STATUS_REJECTED => 'danger',
    \app\models\ManualTime::STATUS_ADDED => 'success'
];
?>
<div class="wl">
    <div class="jumbotron">
        <h1>Manual time - <?=\yii\helpers\Html::a('Add', ['create'])?></h1>
    </div>

    <div class="row">
        <div class="col-xs-8">
            <div class="btn-group">
                <button id="all" type="button" class="btn btn-default <?= (\Yii::$app->request->get('status', false) === 'all' || !\Yii::$app->request->get('status', false) ? 'disabled' : '') ?>">
                    All <span class="label label-default"><?= $count['all'] ?></span>
                </button>
                <button id="pending" type="button" class="btn btn-default <?= (\Yii::$app->request->get('status', false) === 'pending' ? 'disabled' : '') ?>">
                    Penging <span class="label label-warning"><?= $count['pending'] ?></span>
                </button>
                <button id="rejected" type="button" class="btn btn-default <?= (\Yii::$app->request->get('status', false) === 'rejected' ? 'disabled' : '') ?>">
                    Rejected <span class="label label-danger"><?= $count['rejected'] ?></span>
                </button>
                <button id="added" type="button" class="btn btn-default <?= (\Yii::$app->request->get('status', false) === 'added' ? 'disabled' : '') ?>">
                    Added <span class="label label-success"><?= $count['added'] ?></span>
                </button>
            </div>
        </div>
        <div class="col-xs-4">
            <?= Select2::widget([
                'id' => 'users',
                'name' => 'user',
                'data' => ArrayHelper::map($users, 'id', 'email'),
                'value' => \Yii::$app->request->get('user', false),
                'options' => ['placeholder' => 'Select a user...', 'data-status' => \Yii::$app->request->get('status', 'all')],
                'pluginOptions' => [
                    'allowClear' => false
                ],
            ]) ?>
        </div>
    </div>

    <?php foreach ($models as $model): ?>
        <div class="bs-callout bs-callout-<?= $status[$model->status] ?>">
            <div class="row">
                <div class="col-xs-10">
                    <h4>
                        <b><?= $model->user->first_name ?> <?= $model->user->last_name ?></b>
                        <b><?= \yii\helpers\Html::a($model->issue_key,'https://zapleo.atlassian.net/browse/'.$model->issue_key, ['target' => '_blank']) ?></b>
                        <span class="to-datetime-convert"><?= $model->start_timestamp ?></span> - <span class="to-datetime-convert"><?= $model->end_timestamp ?></span>
                    </h4>
                    <small>
                        Added by <?= $model->createdBy->first_name ?> <?= $model->createdBy->last_name ?> (<span class="to-datetime-convert"><?= $model->created_at ?></span>) |
                        Updated by <?= $model->updatedBy->first_name ?> <?= $model->updatedBy->last_name ?> (<span class="to-datetime-convert"><?= $model->updated_at ?></span>)
                    </small>
                </div>
                <div class="col-xs-2">
                    <h4 class="text-right">
                        <?= ($model->status !== \app\models\ManualTime::STATUS_ADDED ? \yii\helpers\Html::a('<span class="glyphicon glyphicon-pencil"></span>', ['update', 'id' => $model->id]) : '') ?>
                        <?= \yii\helpers\Html::a('<span class="glyphicon glyphicon-trash"></span>', ['delete', 'id' => $model->id], [
                            'data' => [
                                'confirm' => 'Are you sure you want to delete this manual time?',
                                'method' => 'post',
                            ],
                        ]) ?>
                    </h4>
                </div>
            </div>
            <p class="comment">
                <?= $model->comment ?>
            </p>
            <div>
                <?= (Yii::$app->user->identity->isAdmin() && $model->status !== \app\models\ManualTime::STATUS_ADDED ? \yii\helpers\Html::a('Accept', ['accept', 'id' => $model->id], [
                    'class' => 'btn btn-success',
                    'data' => [
                        'confirm' => 'Are you sure you want to accept this manual time?',
                        'method' => 'post',
                    ],
                ]) : '') ?>
                <?= (Yii::$app->user->identity->isAdmin() && $model->status !== \app\models\ManualTime::STATUS_REJECTED ? \yii\helpers\Html::a('Decline', ['decline', 'id' => $model->id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => 'Are you sure you want to decline this manual time?',
                        'method' => 'post',
                    ],
                ]) : '') ?>
            </div>
        </div>
    <?php endforeach; ?>

    <?php if (empty($models)): ?>
        <div class="bs-callout bs-callout-default">
            <p class="lead">Empty!</p>
            Manual time not found!
        </div>
    <?php endif; ?>

    <?php
        // отображаем ссылки на страницы
        echo LinkPager::widget([
            'pagination' => $pages,
        ]);
    ?>
</div>