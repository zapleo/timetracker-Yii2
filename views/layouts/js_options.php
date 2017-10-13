<?php
/**
 * @var $this \yii\web\View
 */
?>
<script type="text/javascript">
    var base_url = <?= json_encode(Yii::$app->params['base_url']) ?>;
    var user_id = <?= Yii::$app->user->id ?>;
    var is_admin = <?= Yii::$app->user->identity->isAdmin() ?>;
</script>
