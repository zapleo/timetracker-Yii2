<?php

/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = 'About';
$this->params['breadcrumbs'][] = $this->title;
$this->registerJs('$(\'#test\').on(\'click\',function () {
        $.ajax({
            url:\'/system/get-full-logs\'+"?user_id=8",
            method:"post",
            data:{
                "uid":[1],
                "dt_start":"1507626000",
                "dt_end":  "1507629000",
                "type":"day"
            }
        })
    });',\yii\web\View::POS_LOAD);
?>
<div class="site-about">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        This is the About page. You may modify the following file to customize its content:
    </p>

    <code><?= __FILE__ ?></code>
    <button id="test">Test</button>
</div>
<script>


</script>