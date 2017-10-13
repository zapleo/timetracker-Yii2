<?php
/**
 * @var $this \yii\web\View
 */
?>
<script type="text/javascript">
    var baseUrl = <?= json_encode(Yii::$app->params['base_url']);?>;
    var userId = <?= Yii::$app->user->id;?>;
</script>
