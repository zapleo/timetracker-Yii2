<?php
/**
 * Created by ValekS. TimeTracker. ZapleoSoft.
 * File: navigation.php
 * Date: 11.10.17
 * Time: 8:21
 */

?>
<div class="navbar navbar-inverse second-nav nav-fixed" role="navigation" style="display: none;">
    <div class="container">

        <?php if (!Yii::$app->user->isGuest): ?>
            <div>

                <div class="navbar-form">
                    <div class="row">
                        <div class="col">

                            <form role="form">

                                <select id="months" class="selectpicker" style="outline: none;">
                                    <option>Nothing selected</option>
                                    <option>January</option>
                                    <option>February</option>
                                    <option>March</option>
                                    <option>April</option>
                                    <option>May</option>
                                    <option>June</option>
                                    <option>July</option>
                                    <option>August</option>
                                    <option>September</option>
                                    <option>October</option>
                                    <option>November</option>
                                    <option>December</option>
                                </select>

                                <div class="input-group">
                                    <span class="input-group-addon" style="display:none;">
                                        <input type="radio" name="date" id="date-start" checked="checked">
                                    </span>
                                    <input type="text" class="form-control" id="datepicker-start" />
                                </div><!-- /input-group -->

                                <div class="input-group">
                                    <span class="input-group-addon" style="display:none;">
                                        <input type="radio" id="date-end" name="date">
                                    </span>
                                    <input type="text" class="form-control" id="datepicker-end" />
                                </div><!-- /input-group -->

                                <select id="project" class="selectpicker">
                                </select>
                                <select id="task" class="selectpicker hide">
                                </select>

                                <div class="btn-group navbar-right">

                                    <div class="btn-group-btn">
                                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                                Users List <span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu pull-right" id="users-list">
                                        </ul>
                                    </div><!-- /btn-group -->
                                </div><!-- /input-group -->


                            </form>

                        </div>
                    </div><!-- /.col-lg-6 -->
                </div>

            </div>
        <?php endif; ?>

    </div>
</div>