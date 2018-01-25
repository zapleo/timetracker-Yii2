<?php

use yii\helpers\Html;

?>

<span id="my-profile" class="btn-group navbar-brand">
    <span class="dropdown-toggle" data-toggle="dropdown">
        <img src="<?= $user['photo'] ?>" alt="User photo" width="46px" id="profile-img" class="img-circle">
    </span>
    <ul class="dropdown-menu" id="profile-menu" role="menu">
        <li>Signed in as <b><?= $user['first_name']{0}.'. '.$user['last_name'] ?></b></li>
        <li><a href="/manual-time">Manual time</a></li>
        <li class="divider"></li>
        <li><a href="https://drive.google.com/open?id=1dQkFDGApPDFz42KFzjgi_fQGJX7CkAjs" target="_blank">Download TimeTracker Client</a></li>
<!--    <li class="divider"></li>-->
<!--    <li><a href="/logout">Logout</a></li>-->
    </ul>
</span>