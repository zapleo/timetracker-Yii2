<?php

/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = 'About';
$this->params['breadcrumbs'][] = $this->title;
$this->registerJs('$(\'#test\').on(\'click\',function () {
        $.ajax({
            url:\'/system/get-full-logs\'+"?user_id=1",
            method:"post",
            data:{
                "uid":[1],
                "month":10,
                "timeStart":"10/10/2017 13:00:00",
                "timeEnd":"10/10/2017  13:50:00",
         
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