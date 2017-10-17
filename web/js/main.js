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

function getWorkLogsTemplate(user_id) {
    var row = '';

    row += '<div class="wl" id="work-logs' + user_id + '" user="' + user_id + '"><div class="row well well-sm"><div class="col-md-2 user_info" id="user' + user_id + '">';
    row += '</div><div class="col-md-10 info">';
    row += '<div id="logs' + user_id + '" class="user_logs"></div>';
    row += '</div></div></div>';

    if ($('div#main').is(':visible'))
        $('div#main').css({ display: 'none' });

    $('div.work-logs').append(row);
}

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
                users_list += '<li id="user'+users[i].id+'">';
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

        },
        error: function () {
            alert('Неудалось получить данные!');
        }
    });

}

function getUserInfo(id)
{
    $.ajax({
        url: base_url + '/system/get-user-info?id=' + id,
        dataType: 'json',
        data: {},
        success: function (user) {

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

        },
        error: function () {
            alert('Неудалось получить данные!');
        }
    });

}

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

function screenshot_block_render(log, type, project, task)
{
    var screenshot = '';

    var index = Math.round(log.ai / log.count);
    var time = '';

    if (log.tstart == log.tend) {
        time = moment(log.tstart, 'X').format('Y-MM-DD HH:mm:ss');
    } else {
        time = moment(log.tstart, 'X').format('Y-MM-DD HH:mm:ss') + ' - ' + moment(log.tend, 'X').format('HH:mm:ss');
    }

    screenshot += '<div class="screen">';
    screenshot += '<div class="screen-text-top">' + time + '</div>';
    screenshot += '<img src="/preview_screenshots/'+log.screenshot_preview+'" alt="..." height="156px" width="280px" class="img-rounded item" data-url="'+log.screenshot+'" data-type="'+
        type+'" data-tstart="'+log.tstart+'" data-tend="'+log.tend+'" data-log-task="'+log.task+'" data-log-ai="'+log.ai+'" data-project="'+project+'" data-task="'+task+'" data-user-id="'+log.user_id+'">';
    screenshot += '<div class="screen-text">';
    screenshot += 'Activity index' + (log.count > 1 ? ' ≈ ' : ' ') + '<span>' + index + '%</span>';
    screenshot += '</div></div>&nbsp;&nbsp;';

    return screenshot;
}

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
        items += '">' + '<a href="/screenshots/'+$(this).data('url')+'" target="_blank">' +
            '<img src="/screenshots/'+$(this).data('url')+'" alt="Screenshot"></a><div class="carousel-caption">' +
            '<h3>'+time+'</h3><h4><a href="https://zapleo.atlassian.net/browse/'+$(this).data('log-task')+'" target="_blank">#'+$(this).data('log-task')+'</a> - AI '+$(this).data('log-ai')+'%</h4>' +
            '</div></div>';
    });

    modal.find('h4.modal-title').empty().append($('#logs-modal .modal-title').html());
    modal.find('ol.carousel-indicators').empty().append(indicators);
    modal.find('div.carousel-inner').empty().append(items);

    modal.modal();
    $('.carousel').carousel('pause');
}

function work_logs_modal_render(logs, tstart, tend, type, project, task)
{
    var count_ai = 0, count = 0, work_count = 0, no_work_count = 0;
    var count_time = '0h 00m (0h 00m)';
    var ai = '0';

    var modal = $('#logs-modal');
    var time = '';
    var work_logs = '';

    if (tstart == tend) {
        time = moment(tstart, 'X').format('Y-MM-DD HH:mm:ss');
    } else {
        time = moment(tstart, 'X').format('Y-MM-DD HH:mm:ss') + ' - ' + moment(tend, 'X').format('HH:mm:ss');
    }

    if (logs.length > 0) {

        $.each(logs, function( index, log ) {
            count_ai += parseInt(log.ai);
            count += parseInt(log.count);
            work_count += parseInt(log.work_count);
            no_work_count += parseInt(log.no_work_count);

            work_logs += screenshot_block_render(log, type, project, task);
        });

        ai = Math.round(count_ai / count);
        count_time = parseInt(work_count * 10 / 60) + 'h ' + (work_count * 10)%60 + 'm';
        count_time += ' (' + parseInt(no_work_count * 10 / 60) + 'h ' + (no_work_count * 10)%60 + 'm)';

    }

    work_logs += '';

    modal.find('div.modal-body').empty().append(work_logs);
    modal.find('h4.modal-title').empty().append('<span class="glyphicon glyphicon-calendar"></span> ' + time);
    modal.find('div.modal-footer #time').empty().append('<span class="glyphicon glyphicon-time"></span> ' + count_time);
    modal.find('div.modal-footer #index').empty().append('<span class="glyphicon glyphicon-signal"></span> ' + ai + '%');

    modal.modal();
}

function work_logs_render(logs, type, project, task)
{
    var count_ai = 0, count = 0, work_count = 0, no_work_count = 0;
    var count_time = '0h 00m (0h 00m)';
    var ai = '0';

    var work_logs = '<div class="mcs-horizontal">';

    if (logs.length > 0) {

        $.each(logs, function( index, log ) {
            count_ai += parseInt(log.ai);
            count += parseInt(log.count);
            work_count += parseInt(log.work_count);
            no_work_count += parseInt(log.no_work_count);

            work_logs += screenshot_block_render(log, type, project, task);
        });

        ai = Math.round(count_ai / count);
        count_time = parseInt(work_count * 10 / 60) + 'h ' + (work_count * 10)%60 + 'm';
        count_time += ' (' + parseInt(no_work_count * 10 / 60) + 'h ' + (no_work_count * 10)%60 + 'm)';

    } else {

        work_logs += '<div class="screen">';
        work_logs += '<img src="/img/default.png" alt="..." height="156px" class="img-rounded" data-log-id="none">';
        work_logs += '</div>';

    }

    work_logs += '</div>';

    $('div#logs' + user_id).empty().append(work_logs);

    // $('img').one('error', function() {
    //     this.src = base_url + 'img/default.png';
    // });

    $('.mcs-horizontal').mCustomScrollbar({
        axis:'x',
        theme:'dark-3',
        mouseWheelPixels: 250
    });

    var wl = $('div.work-logs div#work-logs' + user_id);

    wl.find('div.info').attr('count_time', count_time).attr('ai', ai);
    wl.find('div#uname div#count').empty().append('Time: ' + count_time);
    wl.find('div#uname div#ai').empty().append('AI ≈ ' + ai + '%');
}

function getWorkLogs(user_id, dt_start, dt_end, type, render_type, project, task)
{
    // vars initialize
    if (project == undefined)
        project = 0;
    if (task == undefined)
        task = 0;

    //var all = $('ul#users-list li input#user_all').prop('checked');
    //var empty = $('ul#users-list li input#user_all_empty').prop('checked');

    $.ajax({
        url: base_url + '/system/get-work-logs?user_id=' + user_id,
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

            // if (all == true && empty == false && res.data.length == 0) {
            //     $('ul#users-list li input#user[value="' + user_id + '"]').prop('checked', false);
            //     $('div#work-logs' + user_id).remove();
            //     delete localStorage['user'+user_id];
            //
            //     return;
            // }

            if (render_type) {
                work_logs_modal_render(logs, dt_start, dt_end, type, project, task);
            } else {
                work_logs_render(logs, type, project, task);
            }

            $('img.item').off('click');
            $('img.item').on('click', function() {
                var img = $(this);

                var type = img.data('type');
                var user_id = img.data('user-id');
                var tstart = img.data('tstart');
                var tend = img.data('tend');
                var project = img.data('project');
                var task = img.data('task');

                //console.log(type + ' - ' + user_id + ' - ' + tstart + ' - ' + tend + ' - ' + project + ' - ' + task);

                if (type == 'month') {
                    type = 'day';

                    getWorkLogs(user_id, tstart, tend, type, 1, project, task);
                } else if (type == 'day') {
                    type = 'hour';

                    getWorkLogs(user_id, tstart, tend, type, 1, project, task);
                } else if (type == 'hour') {
                    type = 0;

                    getWorkLogs(user_id, tstart, tend, type, 1, project, task);
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

function load_logs(date_start, date_end, type, project, task)
{
    if (date_end == undefined)
        date_end = 0;
    if (project == 'All project' || project == 'Nothing selected' || project == undefined)
        project = 0;
    if (task == 'All task' || task == 'Nothing selected' || task == undefined)
        task = 0;

    var datetime_start = moment(date_start, 'D/M/Y').hours(0).minutes(0).seconds(0).format('X');
    var datetime_end = moment((date_end == false ? date_start : date_end), 'D/M/Y').hours(23).minutes(59).seconds(59).format('X');

    getProjects(datetime_start, datetime_end, project);

    for (i = 0; i < localStorage.length; i++) {
        if (localStorage.key(i).startsWith('user')) {
            var id = localStorage[localStorage.key(i)];

            getWorkLogs(id, datetime_start, datetime_end, type, 0, project, task);
        }
    }
}

function init()
{
    if (is_admin)
        getUsersList();

    var datetime_start = moment().hours(0).minutes(0).seconds(0).format('X');
    var datetime_end = moment().hours(23).minutes(59).seconds(59).format('X');

    getProjects(datetime_start, datetime_end);

    for (i = 0; i < localStorage.length; i++) {
        if (localStorage.key(i).startsWith('user')) {
            var id = localStorage[localStorage.key(i)];

            getWorkLogsTemplate(id);
            getUserInfo(id);
            getWorkLogs(id, datetime_start, datetime_end, 'hour', 0);
        }
    }

}

$(document).ready(function(){
    if (!is_admin && !localStorage['user'+user_id])
        localStorage['user'+user_id] = user_id;

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
        if (e.target.value == 'Nothing selected')
            return;

        var month = months_obj[e.target.value];
        var date_start = moment($('input#datepicker-start').val(), 'D/M/Y').month(month);

        $('#datepicker-start').val(date_start.date(1).format('DD/MM/Y'));
        $('#datepicker-end').val(date_start.endOf('month').format('DD/MM/Y'));

        load_logs($('input#datepicker-start').val(), $('input#datepicker-end').val(), 'day');
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
        $('ul#users-list li input#user_all').prop('checked', false);

        $('#months').selectpicker('val', 'Nothing selected');
        load_logs(date_start, 0, 'hour');
        
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
        var date_start = $('input#datepicker-start').val();
        var date_end = $('input#datepicker-end').val();

        $('ul#users-list li input#user_all').prop('checked', false);

        $('#months').selectpicker('val', 'Nothing selected');
        load_logs(date_start, date_end, 'hour');
        
        $('#task').empty();
        $('#task').selectpicker('refresh');
    });
    // end

    //select project
    $('#project').on('changed.bs.select', function (e) {
        var project = e.target.value;
        var datetime_start = moment($('input#datepicker-start').val(), 'D/M/Y').hours(0).minutes(0).seconds(0).format('X');
        var datetime_end = moment($('input#datepicker-end').val(), 'D/M/Y').hours(23).minutes(59).seconds(59).format('X');
        var type = 'hour';

        if ($('button[data-id="months"]').attr('title') != 'Nothing selected')
            type = 'day';

        load_logs($('input#datepicker-start').val(), $('input#datepicker-end').val(), type, project);
        getTasks(datetime_start, datetime_end, project);

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
        var type = 'hour';

        if ($('button[data-id="months"]').attr('title') != 'Nothing selected')
            type = 'day';

        $('ul#users-list li input#user_all').prop('checked', false);

        if (project == 'All project' || !project) {
            project = 0;
        }

        load_logs($('input#datepicker-start').val(), $('input#datepicker-end').val(), type, project, task);
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