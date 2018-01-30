var months_obj = {
    'January': 0,
    'February': 1,
    'March': 2,
    'April': 3,
    'May': 4,
    'June': 5,
    'July': 6,
    'August': 7,
    'September': 8,
    'October': 9,
    'November': 10,
    'December': 11
};

var project_ready = false;
var modal_count = 0;

/**
 *
 * @param user_id
 */
function getWorkLogsTemplate(user_id) {
    var row = '';

    row += '<div class="wl" id="work-logs' + user_id + '" user="' + user_id + '" style="display: none"><div class="row well well-sm"><div class="col-md-2 user_info" id="user' + user_id + '">';
    row += '</div><div class="col-md-10 info">';
    row += '<div id="logs' + user_id + '" class="user_logs"></div>';
    row += '</div></div></div>';

    if ($('div#main').is(':visible'))
        $('div#main').css({ display: 'none' });

    $('div.work-logs').append(row);
}

/**
 *
 */
function getUsersList()
{
    $.ajax({
        url: base_url + '/system/get-users-list',
        dataType: 'json',
        data: {},
        success: function (users) {

            var users_list = '';

            for(i = 0; i < users.length; i++)
            {
                users_list += '<li id="user'+users[i].id+'" class="user">';
                users_list += '<div class="checkbox"><label>';
                users_list += '<input type="checkbox" ' + (localStorage['user'+users[i].id] == users[i].id ? 'checked="checked"' : '') + ' id="user" value="' + users[i].id + '"> ' +
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

            if ( $('#users-list li.user :input:checkbox:checked').length == $('#users-list li.user :input').length ) {
                $('ul#users-list').find('li#user_all_empty input').prop('checked', true);
            }

        },
        error: function () {
            alert('Неудалось получить данные!');
        }
    });

}

/**
 *
 * @param id
 */
function getUserInfo(id)
{
    $.ajax({
        url: base_url + '/system/get-user-info?id=' + id,
        dataType: 'json',
        data: {},
        success: function (user) {

            var user_info = '';

            var total_time = $('div.work-logs div#work-logs' + id).find('div.info').attr('total_time');
            //var count_time = $('div.work-logs div#work-logs' + id).find('div.info').attr('count_time');
            var ai = $('div.work-logs div#work-logs' + id).find('div.info').attr('ai');

            user_info += '<div class="text-center">';
            user_info += '<img src="' + user.photo + '" alt="..." width="100px" height="100px" class="img-rounded" id="myPopover' + id + '" data-toggle="popover">';
            user_info += '</div>';
            user_info += '<div class="text-center" id="uname"><h4>' + user.first_name + ' ' + user.last_name + '</h4><div><span id="total" class="label label-default">Total time: ' + total_time + '</span> <span id="ai" class="label label-primary" title="Activity index">AI ≈ ' + ai + '</span></div><div id="count"></div></div>';

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

/**
 *
 * @param dt_start
 * @param dt_end
 * @param project
 */
function getProjects(dt_start, dt_end, project)
{
    if (project == undefined)
        project = null;

    var uid_arr = [];

    if (is_admin) {

        for (i = 0; i < localStorage.length; i++) {

            if (localStorage.key(i).startsWith('user')) {
                var uid = localStorage[localStorage.key(i)];

                uid_arr.push(uid);
            }

        }

    }

    $.ajax({
        url: base_url + '/system/get-projects',
        dataType: 'json',
        type: 'POST',
        data: {
            'dt_start': dt_start,
            'dt_end': dt_end,
            'uid': (is_admin ? uid_arr : null)
        },
        success: function (res) {

            $('#project').empty();
            $('#project').append('<option>All project</option>');

            $.each( res, function( key, value ) {
                $('#project').append('<option value="' + value.project + '">' + value.project + '</option>');
            });

            if (project != null && project != 'All project') {
                $('#project').val(project);
            }

            $('#project').selectpicker('refresh');

            project_ready = true;

        },
        error: function () {
            alert('Неудалось получить данные!');
        }
    });

}

/**
 *
 * @param dt_start
 * @param dt_end
 * @param project
 * @param task
 */
function getTasks(dt_start, dt_end, project, task)
{
    if (task == undefined)
        task = null;

    var uid_arr = [];

    if (is_admin) {

        for (i = 0; i < localStorage.length; i++) {

            if (localStorage.key(i).startsWith('user')) {
                var uid = localStorage[localStorage.key(i)];

                uid_arr.push(uid);
            }

        }

    }

    if (project && project != 'All project') {

        $.ajax({
            url: base_url + '/system/get-tasks',
            dataType: 'json',
            type: 'POST',
            data: {
                'dt_start': dt_start,
                'dt_end': dt_end,
                'project': project,
                'uid': (is_admin ? uid_arr : null)
            },
            success: function (res) {

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
                alert('Неудалось получить данные!');
            }
        });

    } else {
        $('#task').empty();
        $('#task').selectpicker('refresh');
    }

}

/**
 *
 * @param log
 * @param type
 * @param project
 * @param task
 * @returns {string}
 */
function screenshot_block_render(log, type, project, task)
{
    var screenshot = '';

    var index = Math.round(log.ai / log.count);
    var time = '';

    if (log.tstart == log.tend) {
        time = moment(log.tstart, 'X').format('HH:mm:ss');
    } else {
        time = moment(log.tstart, 'X').format('Do HH:mm:ss') + ' - ' + moment(log.tend, 'X').format('HH:mm:ss');
    }

    if (type == 'month') {
        time = moment(log.tstart, 'X').format('MMMM YYYY');
    }

    if (type == 'day') {
        time = moment(log.tstart, 'X').format('MMM Do YY') + ' ' + moment(log.tstart, 'X').format('HH:mm:ss') + ' - ' + moment(log.tend, 'X').format('HH:mm:ss');
    }

    //var count_time = parseInt(log.work_count * 10 / 60) + 'h ' + (log.work_count * 10)%60 + 'm';
    //count_time += ' (' + parseInt(log.no_work_count * 10 / 60) + 'h ' + (log.no_work_count * 10)%60 + 'm)';
    var total_time = log.work_count + log.no_work_count + log.manual_time_count;
    total_time = parseInt(total_time * 10 / 60) + 'h ' + (total_time * 10)%60 + 'm';

    screenshot += '<div class="screen">';
    screenshot += '<div class="screen-text-top">' + time + '</div>';
    screenshot += '<img src="'+(log.screenshot_preview != null ? '/preview_screenshots/'+log.screenshot_preview : (log.screenshot.indexOf('.jpg') > 0 ? '/img/default.png' : '/img/default_mt_preview.png'))+'" alt="..." height="156px" width="280px" class="img-rounded item" data-url="'+(log.screenshot.indexOf('.jpg') > 0 ? '/screenshots/' + log.screenshot : '/img/default_manual_time.jpg')+'" data-type="'+
        type+'" data-tstart="'+log.tstart+'" data-tend="'+log.tend+'" data-log-task="'+log.task+'" data-log-ai="'+log.ai+'" data-project="'+project+'" data-task="'+task+'" data-user-id="'+log.user_id+'" data-comment="'+log.comment+'">';
    screenshot += '<div class="screen-text">';
    screenshot += 'AI' + (log.count > 1 ? ' ≈ ' : ' ') + '<span>' + index + '% - '+total_time+'</span>';
    screenshot += '</div></div>&nbsp;&nbsp;';

    return screenshot;
}

/**
 *
 * @param el
 */
function work_logs_carousel_render(el)
{
    var logs = el.closest('.modal-body').find('img.item');
    var modal = $('#screen-modal');
    var indicators = '';
    var items = '';

    logs.each(function (index) {
        var time = moment($(this).data('tstart'), 'X').format('Y-MM-DD HH:mm:ss');

        indicators += '<li data-target="#carousel-example-generic" data-slide-to="'+index+'"';
        items += '<div class="item';

        if (el.data('tstart') == $(this).data('tstart')) {
            indicators += ' class="active" ';
            items += ' active';
        }

        indicators += '></li>';
        items += '">' + '<a href="'+$(this).data('url')+'" target="_blank">' +
            '<img src="'+$(this).data('url')+'" alt="Screenshot"></a><div class="carousel-caption">' +
            '<h3>'+time+'</h3><h4><a href="https://zapleo.atlassian.net/browse/'+$(this).data('log-task')+'" target="_blank">#'+$(this).data('log-task')+'</a> - AI '+$(this).data('log-ai')+'%</h4>' +
            '</div></div>';
    });

    modal.find('h4.modal-title').empty().append($('#logs-modal-0 .modal-title').html());
    modal.find('ol.carousel-indicators').empty().append(indicators);
    modal.find('div.carousel-inner').empty().append(items);

    $('img').one('error', function() {
        this.src = base_url + '/img/default_full.jpg';
    });

    modal.modal();
    modal_count++;
    $('.carousel').carousel('pause');
}

/**
 *
 * @param uid
 * @param logs
 * @param tstart
 * @param tend
 * @param type
 * @param project
 * @param task
 */
function work_logs_modal_render(uid, logs, tstart, tend, type, project, task)
{
    var count_ai = 0, count = 0, work_count = 0, no_work_count = 0, manual_time_count = 0;
    var total_time = '0h 00m';
    //var count_time = '<span class="label label-success" title="Work time">0h 00m</span> <span class="label label-danger" title="No work time">0h 00m</span> <span class="label label-warning" title="Manual time">0h 00m</span>';
    var ai = '0';

    var modal = $('#logs-modal-'+type);
    var time = '';
    var work_logs = '';

    if (tstart == tend) {
        time = moment(tstart, 'X').format('HH:mm:ss');
    } else {
        time = moment(tstart, 'X').format('Do HH:mm:ss') + ' - ' + moment(tend, 'X').format('HH:mm:ss');
    }

    if (type == 'day') {
        time = moment(tstart, 'X').format('MMMM YYYY');
    }

    if (type == 'hour') {
        time = moment(tstart, 'X').format('MMM Do YY') + ' ' + moment(tstart, 'X').format('HH:mm:ss') + ' - ' + moment(tend, 'X').format('HH:mm:ss');
    }

    if (logs.length > 0) {

        $.each(logs, function( index, log ) {
            count_ai += parseInt(log.ai);
            count += parseInt(log.count);
            work_count += parseInt(log.work_count);
            no_work_count += parseInt(log.no_work_count);
            manual_time_count += parseInt(log.manual_time_count);

            work_logs += screenshot_block_render(log, type, project, task);
        });

        ai = Math.round(count_ai / count);
        total_time = work_count + no_work_count + manual_time_count;
        total_time = parseInt(total_time * 10 / 60) + 'h ' + (total_time * 10)%60 + 'm';
        //count_time = '<span class="label label-success" title="Work time">' + parseInt(work_count * 10 / 60) + 'h ' + (work_count * 10)%60 + 'm</span>';
        //count_time += ' <span class="label label-danger" title="No work time">' + parseInt(no_work_count * 10 / 60) + 'h ' + (no_work_count * 10)%60 + 'm</span>';
        //count_time += ' <span class="label label-warning" title="Manual time">' + parseInt(manual_time_count * 10 / 60) + 'h ' + (manual_time_count * 10)%60 + 'm</span>';

    }

    work_logs += '';

    modal.find('div.modal-body').empty().append(work_logs);
    modal.find('h4.modal-title').empty().append('<span class="glyphicon glyphicon-calendar"></span> ' + time);
    modal.find('div.modal-footer #time').empty().append('<span class="glyphicon glyphicon-time"></span> ' + total_time);
    modal.find('div.modal-footer #index').empty().append('<span class="glyphicon glyphicon-signal"></span> ' + ai + '%');

    $('img').one('error', function() {
        this.src = base_url + '/img/default.png';
    });

    modal.modal();
    modal_count++;
}

/**
 *
 * @param uid
 * @param logs
 * @param type
 * @param project
 * @param task
 */
function work_logs_render(uid, logs, type, project, task)
{
    var count_ai = 0, count = 0, work_count = 0, no_work_count = 0, manual_time_count = 0;
    var total_time = '0h 00m';
    var count_time = '<span class="label label-success" title="Work time">0h 00m</span> <span class="label label-danger" title="No work time">0h 00m</span> <span class="label label-warning" title="Manual time">0h 00m</span>';
    var ai = '0';

    var work_logs = '<div class="mcs-horizontal">';

    if (logs.length > 0) {

        $.each(logs, function( index, log ) {
            count_ai += parseInt(log.ai);
            count += parseInt(log.count);
            work_count += parseInt(log.work_count);
            no_work_count += parseInt(log.no_work_count);
            manual_time_count += parseInt(log.manual_time_count);

            work_logs += screenshot_block_render(log, type, project, task);
        });

        ai = Math.round(count_ai / count);
        total_time = work_count + no_work_count + manual_time_count;
        total_time = parseInt(total_time * 10 / 60) + 'h ' + (total_time * 10)%60 + 'm';
        count_time = '<span class="label label-success" title="Work time">' + parseInt(work_count * 10 / 60) + 'h ' + (work_count * 10)%60 + 'm</span>';
        count_time += ' <span class="label label-danger" title="No work time">' + parseInt(no_work_count * 10 / 60) + 'h ' + (no_work_count * 10)%60 + 'm</span>';
        count_time += ' <span class="label label-warning" title="Manual time">' + parseInt(manual_time_count * 10 / 60) + 'h ' + (manual_time_count * 10)%60 + 'm</span>';

    } else {

        work_logs += '<div class="screen">';
        work_logs += '<img src="/img/default.png" alt="..." height="156px" class="img-rounded" data-log-id="none">';
        work_logs += '</div>';

    }

    work_logs += '</div>';

    $('div#logs' + uid).empty().append(work_logs);

    $('img').one('error', function() {
        this.src = base_url + '/img/default.png';
    });

    $('.mcs-horizontal').mCustomScrollbar({
        axis:'x',
        theme:'dark-3',
        mouseWheelPixels: 250
    });

    var wl = $('div.work-logs div#work-logs' + uid);

    wl.find('div.info').attr('total_time', total_time).attr('ai', ai);
    wl.find('div#uname span#total').empty().append('Total time: ' + total_time);
    wl.find('div#uname span#ai').empty().append('AI ≈ ' + ai + '%');

    if (logs.length > 0)
        wl.find('div#uname div#count').empty().append(count_time);

    wl.show();
}

/**
 *
 * @param uid
 * @param dt_start
 * @param dt_end
 * @param type
 * @param render_type
 * @param project
 * @param task
 */
function getWorkLogs(uid, dt_start, dt_end, type, render_type, project, task)
{
    // vars initialize
    if (project == undefined)
        project = 0;
    if (task == undefined)
        task = 0;

    if (is_admin) {
        var all = $('ul#users-list li#user_all input').prop('checked');
        var empty = $('ul#users-list li#user_all_empty input').prop('checked');
    }

    var diff = dt_end - dt_start;
    diff = diff / 60 / 60 / 24;

    if (diff > 1)
        type = 'day';
    if (diff > 31)
        type = 'month';

    $.ajax({
        url: base_url + '/system/get-work-logs?user_id=' + uid,
        dataType: 'json',
        type: 'POST',
        data: {
            'dt_start': dt_start,
            'dt_end': dt_end,
            'project': project,
            'task': task,
            'type': type
        },
        success: function (logs) {

            if (is_admin && all == true && empty == false && logs.length == 0) {
                $('ul#users-list li.user input[value="' + uid + '"]').prop('checked', false);
                $('div#work-logs' + uid).remove();
                delete localStorage['user'+uid];

                return;
            }

            if (render_type) {
                work_logs_modal_render(uid, logs, dt_start, dt_end, type, project, task);
            } else {
                work_logs_render(uid, logs, type, project, task);
            }

            $('img.item').off('click');
            $('img.item').on('click', function() {
                var img = $(this);

                var type = img.data('type');
                var u_id = img.data('user-id');
                var tstart = img.data('tstart');
                var tend = img.data('tend');
                var project = img.data('project');
                var task = img.data('task');

                if (type == 'month') {
                    type = 'day';

                    getWorkLogs(u_id, tstart, tend, type, 1, project, task);
                } else if (type == 'day') {
                    type = 'hour';

                    getWorkLogs(u_id, tstart, tend, type, 1, project, task);
                } else if (type == 'hour') {
                    type = 0;

                    getWorkLogs(u_id, tstart, tend, type, 1, project, task);
                } else {
                    work_logs_carousel_render(img);
                }
            });
        },
        error: function () {
            alert('Неудалось получить данные!');
        }
    });

}

/**
 *
 * @param date_start
 * @param date_end
 * @param type
 * @param project
 * @param task
 */
function load_logs()
{
    var i = 0;

    for (i = 0; i < localStorage.length; i++) {
        if (localStorage.key(i).startsWith('user')) {
            var id = localStorage[localStorage.key(i)];
            load_user(id);
        }
    }
}

/**
 *
 * @param input
 * @param date_start
 * @param date_end
 * @param type
 */
function load_user(uid) {
    var project = $('select#project').val();
    var task = $('select#task').val();

    if (project == 'All project' || project == 'Nothing selected' || project == undefined)
        project = 0;
    if (task == 'All task' || task == 'Nothing selected' || task == undefined)
        task = 0;

    var datetime_start = moment($('input#datepicker-start').val(), 'D/M/Y').hours(0).minutes(0).seconds(0).format('X');
    var datetime_end = moment($('input#datepicker-end').val(), 'D/M/Y').hours(23).minutes(59).seconds(59).format('X');

    if ($('input#datepicker-start').val() == '')
        datetime_start = moment().hours(0).minutes(0).seconds(0).format('X');
    if ($('input#datepicker-end').val() == '')
        datetime_end = moment().hours(23).minutes(59).seconds(59).format('X');

    getProjects(datetime_start, datetime_end, project);

    if (project != 0)
        getTasks(datetime_start, datetime_end, project, task);

    if ($('.wl[user="'+uid+'"]').length == 0) {
        getWorkLogsTemplate(uid);
        getUserInfo(uid);
    }

    getWorkLogs(uid, datetime_start, datetime_end, 'hour', 0, project, task);
}

/**
 *
 */
function init()
{
    if (is_admin)
        getUsersList();

    load_logs();

}

$(document).ready(function(){

    if (!is_admin && !localStorage['user'+user_id]) {
        localStorage.clear();
        localStorage['user' + user_id] = user_id;
    }

    if (is_admin && localStorage.length == 0) {
        localStorage['user' + user_id] = user_id;
    }

    // Start initialization
    init();

    $('#screen-modal').off('hide.bs.modal');
    $('#screen-modal').on('hide.bs.modal', function (e) {
        $('body').css('overflow', 'hidden');
        $('#logs-modal-0').css('overflow', 'auto');
        $('#logs-modal-0').show();

        modal_count--;
    });

    $('#logs-modal-0').off('hide.bs.modal');
    $('#logs-modal-0').on('hide.bs.modal', function (e) {
        if (modal_count > 1) {
            $('body').css('overflow', 'hidden');
            $('#logs-modal-hour').css('overflow', 'auto');
            $('#logs-modal-hour').show();
        } else {
            $('body').css('overflow', 'auto');
        }

        modal_count--;
    });

    $('#logs-modal-hour').off('hide.bs.modal');
    $('#logs-modal-hour').on('hide.bs.modal', function (e) {
        if (modal_count >= 2) {
            $('body').css('overflow', 'hidden');
            $('#logs-modal-day').css('overflow', 'auto');
            $('#logs-modal-day').show();
        } else {
            $('body').css('overflow', 'auto');
        }

        modal_count--;
    });

    $('#logs-modal-day').off('hide.bs.modal');
    $('#logs-modal-day').on('hide.bs.modal', function (e) {
        $('body').css('overflow', 'auto');
        modal_count--;
    });

    $('#screen-modal').off('show.bs.modal');
    $('#screen-modal').on('show.bs.modal', function (e) {
        $('#logs-modal-0').hide();
        $('body').css('overflow', 'hidden');
    });

    $('#logs-modal-0').off('show.bs.modal');
    $('#logs-modal-0').on('show.bs.modal', function (e) {
        $('#logs-modal-hour').hide();
        $('body').css('overflow', 'hidden');
    });

    $('#logs-modal-hour').off('show.bs.modal');
    $('#logs-modal-hour').on('show.bs.modal', function (e) {
        $('#logs-modal-day').hide();
        $('body').css('overflow', 'hidden');
    });

    $('#logs-modal-day').off('show.bs.modal');
    $('#logs-modal-day').on('show.bs.modal', function (e) {
        $('body').css('overflow', 'hidden');
    });

    //var today = new Date();
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
        if (e.target.value == 'Nothing selected')
            return;

        var month = months_obj[e.target.value];
        var date_start = moment($('input#datepicker-start').val(), 'D/M/Y').month(month);

        $('#datepicker-start').val(date_start.date(1).format('DD/MM/Y'));
        $('#datepicker-end').val(date_start.endOf('month').format('DD/MM/Y'));

        load_logs();
    });
    // end

    // datepicker start
    $('#datepicker-start').datetimepicker({
        locale: 'ru',
        format: 'DD/MM/YYYY',
        defaultDate: new Date()
    });

    $('#datepicker-start').on('dp.hide', function(e){
        var date_start = $('input#datepicker-start').val();

        $('#datepicker-end').val(date_start);

        $('#months').selectpicker('val', 'Nothing selected');
        load_logs();
        
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
        $('#months').selectpicker('val', 'Nothing selected');
        load_logs();
        
        $('#task').empty();
        $('#task').selectpicker('refresh');
    });
    // end

    //select project
    $('#project').on('changed.bs.select', function (e) {
        load_logs();

        $('button[data-id="task"]').closest('div').removeClass('hide');

        $('#task').empty();

        if (e.target.value == 'All project')
            $('button[data-id="task"]').closest('div').addClass('hide');
    });
    // end

    // select task
    $('#task').on('changed.bs.select', function (e) {
        load_logs();
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

    $(this).on('click', '#users-list li.user', function() {
        var uid = $(this).find('input').attr('value');

        if ( $('#users-list').find('li#user_all input').prop('checked') == true ) {
            $('ul#users-list').find('li#user_all input').prop('checked', false);
        }

        if ( $('#users-list li.user :input:checkbox:checked').length == $('#users-list li.user :input').length ) {
            $('ul#users-list').find('li#user_all_empty input').prop('checked', true);
        }

        if ( $(this).find('input').prop('checked') ) {
            $('div#main').css({ display: 'none' });

            localStorage['user'+uid] = uid;

            load_user(uid);

        } else {

            var count_log = 0;

            if (localStorage.length >= 1) {
                delete localStorage['user'+uid];
            }

            $('div#work-logs' + uid).remove();

            count_log = $('.wl').find('.info .screen .screen-text').length;

            if (count_log == 0) {
                $('select#project').val('All project');
                $('select#task').val('All task');
                $('button[data-id="task"]').closest('div').addClass('hide');
            }

            load_logs();

            if ($('input:checkbox:checked').length == 0)
                $('div#main').css({ display: 'block' });

        }
    });

    $(this).on('click', '#users-list li#user_all, #users-list li#user_all_empty', function(){
        var checkbox = $(this);

        if ($(this).attr('id') == 'user_all') {
            $('ul#users-list li#user_all_empty input').prop('checked', false);
        } else if ($(this).attr('id') == 'user_all_empty') {
            $('ul#users-list li#user_all input').prop('checked', false);
        }

        if ($(this).find('input').prop('checked')) {
            $.each($('#users-list li.user :input'), function (key, value) {
                var uid = $(value).attr('value');

                if (!$(value).prop('checked')) {
                    $(value).prop('checked', true);

                    localStorage['user'+uid] = uid;

                    load_user(uid);
                } else {
                    if ($(checkbox).attr('id') == 'user_all') {
                        var user = $('div.work-logs div#work-logs' + uid);

                        if ($(user).find('div.info').attr('ai') == '0') {
                            $('ul#users-list li.user input[value="' + uid + '"]').prop('checked', false);
                            $('div#work-logs' + uid).remove();
                            delete localStorage['user'+uid];
                        }
                    }
                }
            });
        } else {
            $('#users-list li.user :input').prop('checked', false);
            localStorage.clear();
            $('.work-logs').empty();
            $('#main').show();
        }
    });

});