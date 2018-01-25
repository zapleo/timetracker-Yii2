<?php

use yii\helpers\Html;

?>

<span id="my-profile" class="btn-group navbar-brand">
    <span class="dropdown-toggle profile-avatar" data-toggle="dropdown">
        <img src="<?= $user['photo'] ?>" alt="User photo" width="46px" id="profile-img" class="img-circle">
        <?= ($manual_time ? '<span class="badge badge-info">'.$manual_time.'</span>' : '') ?>
    </span>
    <ul class="dropdown-menu" id="profile-menu" role="menu">
        <li>Signed in as <b><?= (!empty($user['last_name']) ? $user['first_name']{0}.'. '.$user['last_name'] : $user['first_name']) ?></b></li>
        <li><a href="/manual-time">Manual time <?= ($manual_time ? '<span class="badge badge-info">'.$manual_time.'</span>' : '') ?></a></li>
        <li class="divider"></li>
        <li><a href="https://drive.google.com/open?id=1dQkFDGApPDFz42KFzjgi_fQGJX7CkAjs" target="_blank">Download TimeTracker Client</a></li>
<!--    <li class="divider"></li>-->
<!--    <li><a href="/logout">Logout</a></li>-->
    </ul>
</span>