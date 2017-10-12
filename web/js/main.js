var USER_ID = 1;
var RIGHTS = 0;
var DOMAIN = 'http://tracker.com/';

var months_obj = {
    'Nothing selected': 0,
    'January': '01',
    'February': '02',
    'March': '03',
    'April': '04',
    'May': '05',
    'June': '06',
    'July': '07',
    'August': '08',
    'September': '09',
    'October': '10',
    'November': '11',
    'December': '12'
};

function getUsersList()
{
    $.ajax({
        url: '/system/get-users-list',
        dataType: 'json',
        data: {},
        success: function (users) {

            var users_list = '';

            for(i = 0; i < users.length; i++)
            {
                users_list += '<li id="user'+users[i].id+'">';
                users_list += '<div class="checkbox"><label>';
                users_list += '<input type="checkbox" ' + (!RIGHTS ? 'checked="checked"' : '') + ' id="user" value="' + users[i].id + '"> ' +
                    users[i].first_name + ' ' + users[i].last_name;
                users_list += '</label></div>';
                users_list += '</li>';
            }

            users_list += '<li class="divider"></li>';
            users_list += '<li id="user_all"><div class="checkbox"><label>';
            users_list += '<input type="checkbox"> Select all';
            users_list += '</label></div></li>';
            users_list += '<li id="user_all_empty"><div class="checkbox"><label>';
            users_list += '<input type="checkbox"> Select all(empty)';
            users_list += '</label></div></li>';

            $('#users-list').append(users_list);

        },
        error: function () {
            alert('Неудалось получить данные!');
        }
    });

}

function getUserInfo(id)
{
    $.ajax({
        url: DOMAIN + 'ajax/ajax.php?action=getUserInfo&id=' + id,
        dataType: 'json',
        data: {},
        success: function (user) {

            var user = user[0];
            var user_info = '';

            var count_time = $('div.work-logs div#work-logs' + id).find('div.info').attr('count_time');
            var ai = $('div.work-logs div#work-logs' + id).find('div.info').attr('ai');

            user_info += '<div class="text-center">';
            user_info += '<img src="' + user.photo + '" alt="..." width="100px" height="100px" class="img-rounded" id="myPopover' + id + '" data-toggle="popover">';
            user_info += '</div>';
            user_info += '<div class="text-center" id="uname">' + user.first_name + ' ' + user.last_name + '<div id="count">Time: ' + count_time + '</div><div id="ai">AI ≈ ' + ai + '</div></div>';

            $('div#user' + id).empty().append(user_info);

            var ww = $(window).width();

            $(window).resize(function() {
                ww = $(window).width();
            });

            $('#myPopover' + id).popover({
                content : '<div><b>Team:</b> ' + user.team + '</div><div><b>eMail:</b> ' + user.email + '</div><div><b>Phone:</b> ' + user.phone + '</div><div><b>Skype:</b> ' + user.skype + '</div>',
                html: true,
                placement: (ww > 640 ? 'right' : 'bottom'),
                trigger: 'hover',
                template: '<div class="popover"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>'
            });

        },
        error: function () {
            console.log('Неудалось получить данные!');
        }
    });

}

function getProjects(timeStart, project, month)
{
    var timeEnd = 0;
    var uid_arr = [];

    if (RIGHTS) {

        for (i = 0; i < localStorage.length; i++) {

            if (localStorage.key(i).startsWith('user')) {
                var uid = parseInt(localStorage.key(i).substring(4));

                uid_arr.push(uid);
            }

        }

    }

    //console.log(uid_arr);

    if ($('#date-end').prop('checked') == true) {
        timeEnd = $('input#datepicker-end').val();
    }

    $.ajax({
        url: DOMAIN + 'ajax/ajax.php?action=getProjects',
        dataType: 'json',
        type: 'POST',
        data: {
            'timeStart': timeStart,
            'timeEnd': timeEnd,
            'uid': (RIGHTS ? uid_arr : 0),
            'month': month
        },
        success: function (res) {

            //console.log(res);

            $('#project').empty();
            $('#project').append('<option>All project</option>');

            $.each( res, function( key, value ) {
                $('#project').append('<option value="' + value.project + '">' + value.project + '</option>');
            });

            if (project != 0 && project != 'All project') {
                $('#project').val(project);
            }

            $('#project').selectpicker('refresh');

        },
        error: function () {
            console.log('Неудалось получить данные!');
        }
    });

}

function getTasks(timeStart, project, task, month)
{
    var timeEnd = 0;
    var uid_arr = [];

    if (RIGHTS) {

        for (i = 0; i < localStorage.length; i++) {

            if (localStorage.key(i).startsWith('user')) {
                var uid = parseInt(localStorage.key(i).substring(4));

                uid_arr.push(uid);
            }

        }

    }

    if ($('#date-end').prop('checked') == true) {
        timeEnd = $('input#datepicker-end').val();
    }

    if (project && project != 'All project') {

        $.ajax({
            url: DOMAIN + 'ajax/ajax.php?action=getTasks',
            dataType: 'json',
            type: 'POST',
            data: {
                'timeStart': timeStart,
                'timeEnd': timeEnd,
                'project': project,
                'uid': (RIGHTS ? uid_arr : 0),
                'month': month
            },
            success: function (res) {

                //console.log(res);

                if (res.length > 0) {

                    $('#task').empty();
                    $('#task').append('<option>All task</option>');

                    $.each( res, function( key, value ) {
                        $('#task').append('<option value="' + value.task + '">' + value.task + '</option>');
                    });

                    if (task != 0 && task != 'All task') {
                        $('#task').val(task);
                    }

                    $('#task').selectpicker('refresh');
                    $('button[data-id="task"]').closest('div').removeClass('hide');

                } else {
                    $('#task').empty();
                    $('#task').selectpicker('refresh');
                }

            },
            error: function () {
                console.log('Неудалось получить данные!');
            }
        });

    } else {
        $('#task').empty();
        $('#task').selectpicker('refresh');
    }

}

function getWorkLogs(user_id, timeStart, timeEnd, project, task, type, month)
{
    // vars initialize
    if (project == 'All project' || project == 'Nothing selected' || project == undefined)
        project = null;
    if (task == 'All task' || task == 'Nothing selected' || task == undefined)
        task = null;
    if (type == undefined)
        type = null;
    if (month == undefined)
        month = null;

    var all = $('ul#users-list li input#user_all').prop('checked');
    var empty = $('ul#users-list li input#user_all_empty').prop('checked');

    $.ajax({
        url: '/system/get-full-logs?user_id=' + user_id,
        dataType: 'json',
        type: 'POST',
        data: {
            'timeStart': timeStart,
            'timeEnd': timeEnd,
            'project': project,
            'task': task,
            'type': type,
            'month': month
        },
        success: function (res) {

            // if (all == true && empty == false && res.data.length == 0) {
            //     $('ul#users-list li input#user[value="' + user_id + '"]').prop('checked', false);
            //     $('div#work-logs' + user_id).remove();
            //     delete localStorage['user'+user_id];
            //
            //     return;
            // }

            //console.log(res);

            // var count_time = res.count_time;
            // var ai = res.ai;
            // var logs = res.data;

            var work_logs = '<div class="mcs-horizontal">';

            if (res.length > 0) {

                for(i = 0; i < logs.length; i++) {
                    var index = Math.round(logs[i].ai / logs[i].count);
                    var time = '';

                    if (logs[i].tstart == logs[i].tend) {
                        time = logs[i].tstart;
                    } else {
                        time = logs[i].tstart + ' - ' + logs[i].tend.split(' ')[1];
                    }

                    work_logs += '<div class="screen">';
                    work_logs += '<div class="screen-text-top">' + time + '</div>';
                    work_logs += '<img src="' + getPreview(logs[i].screen_small, logs[i].screen) + '" alt="..." height="156px" width="280px" class="img-rounded item" time="' + time + '" date="' + logs[i].tstart + '" type="' + (month == 0 ? 'hour' : 'day') + '" log-id="' +
                        logs[i].id + '" user-id="' + logs[i].user_id +'" data-img="' + logs[i].screen + '">';
                    work_logs += '<div class="screen-text">';
                    work_logs += 'Activity index' + (logs[i].count > 1 ? ' ≈ ' : ' ') + '<span>' + index + '%</span>';
                    work_logs += '</div></div>&nbsp;&nbsp;';
                }

            } else {

                work_logs += '<div class="screen">';
                work_logs += '<img src="' + DOMAIN + 'img/default.png" alt="..." height="156px" class="img-rounded" log-id="none">';
                work_logs += '</div>';

            }

            work_logs += '</div>';

            $('div#logs' + user_id).empty().append(work_logs);

            $('img').one('error', function() {
                this.src = DOMAIN + 'img/default.png';
            });

            $('.mcs-horizontal').mCustomScrollbar({
                axis:'x',
                theme:'dark-3',
                mouseWheelPixels: 250
            });

            var wl = $('div.work-logs div#work-logs' + user_id);

            wl.find('div.info').attr('count_time', count_time).attr('ai', ai);
            wl.find('div#uname div#count').empty().append('Time: ' + count_time);
            wl.find('div#uname div#ai').empty().append('AI ≈ ' + ai);

        },
        error: function () {
            console.log('Неудалось получить данные!');
        }
    });

}

function init()
{
    if (RIGHTS) {
        getUsersList();
    } else {
        var datetime_start = moment().format('Y-M-D 00:00:00');
        var datetime_end = moment().add(1, 'days').format('Y-M-D 00:00:00');

        getWorkLogs(USER_ID, datetime_start, datetime_end);
    }
}

$(document).ready(function(){
    // Start initialization
    init();

    var today = new Date();
    //var months = $('select#months option');

    // Filters menu
    $('button.filters').on('click', function (e) {
        if ($('.second-nav').is(':visible')) {
            $('.second-nav').hide();
            $('button.filters').removeClass('open');
            //$('.second-nav').css('margin-top', '35px');
        } else {
            $('.second-nav').show();
            $('button.filters').addClass('open');
            //$('.second-nav').css('margin-top', '50px');
        }

        if ($('nav.navbar .collapse').attr('aria-expanded') == 'true') {
            $('.navbar-toggle').trigger('click');
        }

        $('button.filters').blur();
    });
    // end Filters menu

    $('#project, #task, #months').on('hidden.bs.select', function (e) {
        $('button[data-id="project"], button[data-id="task"], button[data-id="months"]').blur();
    });

    // Months selectpicker
    //$('#months').selectpicker('val', $(months[today.getMonth()+1]).val());
    $('#months').selectpicker('val', 'Nothing selected');

    // select months
    $('#months').on('changed.bs.select', function (e) {
        // TODO: month
        var month = e.target.value;

        if (months_obj[month] == (today.getMonth() + 1)) {
            var day = (today.getDate() < 10 ? '0' + today.getDate() : today.getDate());
            $('#datepicker-start, #datepicker-end').val(day + '/' + months_obj[month] + '/' + today.getFullYear());
        } else {
            $('#datepicker-start, #datepicker-end').val('01/' + months_obj[month] + '/' + today.getFullYear());
        }
    });
    // end

    // datepicker start
    $('#datepicker-start').datetimepicker({
        locale: 'ru',
        format: 'DD/MM/YYYY',
        defaultDate: new Date()
    });

    $('#datepicker-start').on('dp.hide', function(e){
        $('#datepicker-end').val($('input#datepicker-start').val());
        $('ul#users-list li input#user_all').prop('checked', false);

        $('#months').selectpicker('val', 'Nothing selected');
        
        $('#task').empty();
        $('#task').selectpicker('refresh');
    });
    // end

    // datepicker end
    $('#datepicker-end').datetimepicker({
        locale: 'ru',
        format: 'DD/MM/YYYY',
        defaultDate: new Date()
    });

    $('#datepicker-end').on('dp.hide', function(e){
        $('ul#users-list li input#user_all').prop('checked', false);

        $('#months').selectpicker('val', 'Nothing selected');
        
        $('#task').empty();
        $('#task').selectpicker('refresh');
    });
    // end

    //select project
    $('#project').on('changed.bs.select', function (e) {
        var project = e.target.value;
        var month = $('button[data-id=months]').attr('title');

        $('ul#users-list li input#user_all').prop('checked', false);

        $('button[data-id="task"]').closest('div').removeClass('hide');

        $('#task').empty();

        if (project == 'All project') {
            $('button[data-id="task"]').closest('div').addClass('hide');
        }
    });
    // end

    // select task
    $('#task').on('changed.bs.select', function (e) {
        var task = e.target.value;
        var project = $('button[data-id="project"]').attr('title');
        var month = $('button[data-id=months]').attr('title');

        $('ul#users-list li input#user_all').prop('checked', false);

        if (project == 'All project' || !project) {
            project = null;
        }
    });
    // end

    // stop propagation users-list
    $('.dropdown-menu#users-list').click(function (e) {
        var target = $( e.target );

        if (target.is('li') || target.is('label')) {
            e.stopPropagation();
        }
    });
    // end

});