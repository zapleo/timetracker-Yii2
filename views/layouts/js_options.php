<?php
/**
 * @var $this \yii\web\View
 */
$admin = is_null(Yii::$app->user->identity)?false:Yii::$app->user->identity->isAdmin();
?>
<script type="text/javascript">
    var base_url = <?= json_encode(Yii::$app->params['base_url']) ?>;
    var user_id = <?= Yii::$app->user->id ?>;
    var is_admin = <?=$admin?>;
</script>
