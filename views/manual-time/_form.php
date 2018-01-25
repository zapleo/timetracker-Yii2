<?php
/**
 * Created by ValekS. TimeTracker. ZapleoSoft.
 * File: _form.php
 * Date: 22.01.18
 * Time: 13:59
 */

use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->registerJs("
var update = ".($model->isNewRecord ? 0 : 1).";
var project = ".(!$model->isNewRecord ? json_encode(stristr($model->issue_key, '-', true)) : 0).";
var issue = ".(!$model->isNewRecord ? json_encode($model->issue_key) : 0).";
", \yii\web\View::POS_HEAD);

?>

<div class="manual_time-form">

    <?php $form = ActiveForm::begin(); ?>

    <p>
        <?= $form->errorSummary($model); ?>
    </p>

    <?= $form->field($model, 'id', ['template' => '{input}'])->textInput(['style' => 'display:none']); ?>
    <?= $form->field($model, 'issue_key', ['template' => '{input}'])->textInput(['style' => 'display:none']); ?>
    <?= $form->field($model, 'start_timestamp', ['template' => '{input}'])->textInput(['style' => 'display:none']); ?>
    <?= $form->field($model, 'end_timestamp', ['template' => '{input}'])->textInput(['style' => 'display:none']); ?>

    <?= (Yii::$app->user->identity->isAdmin() ? $form->field($model, 'user_id')->widget(Select2::classname(), [
        'data' => ArrayHelper::map($users, 'id', 'email'),
        'options' => ['placeholder' => 'Select a user...'],
        'pluginOptions' => [
            'allowClear' => false
        ],
    ]) : $form->field($model, 'user_id', ['template' => '{input}'])->textInput(['style' => 'display:none', 'value' => Yii::$app->user->id])) ?>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group field-start_timestamp">
                <label class="control-label" for="start_timestamp">Project</label>
                <?= Select2::widget([
                    'name' => 'projects',
                    'id' => 'projects',
                    'data' => [],
                    'options' => ['placeholder' => 'Select projects...', 'disabled' => 'readonly']
                ]) ?>

                <div class="help-block"></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group field-start_timestamp">
                <label class="control-label" for="start_timestamp">Issue</label>
                <?= Select2::widget([
                    'name' => 'issues',
                    'id' => 'issues',
                    'data' => [],
                    'options' => ['placeholder' => 'Select issues...', 'disabled' => 'readonly']
                ]) ?>

                <div class="help-block"></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group field-start_timestamp">
                <label class="control-label" for="start_timestamp">Start DateTime</label>
                <input type="text" id="start_timestamp" class="form-control" name="start_timestamp">

                <div class="help-block"></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group field-end_timestamp">
                <label class="control-label" for="end_timestamp">End DateTime</label>
                <input type="text" id="end_timestamp" class="form-control" name="end_timestamp">

                <div class="help-block"></div>
            </div>
        </div>
    </div>

    <?= $form->field($model, 'comment')->textarea(['placeholder' => 'Your comment...']); ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Add' : 'Update', ['id' => 'add_time', 'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        <?= Html::a('Back', ['index'], ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
