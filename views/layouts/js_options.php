<?php
/**
 * @var $this \yii\web\View
 */

$user_id = is_null(Yii::$app->user->id) ? 0 : Yii::$app->user->id;
$email = is_null(Yii::$app->user->id) ? 0 : Yii::$app->user->identity->email;
$admin = is_null(Yii::$app->user->identity) ? 0 : (Yii::$app->user->identity->isAdmin() ? 1 : 0);
?>
<script type="text/javascript">
    var base_url = <?= json_encode(Yii::$app->params['base_url']) ?>;
    var user_id = <?= $user_id ?>;
    var email = <?= json_encode($email) ?>;
    var is_admin = <?= $admin ?>;
</script>
