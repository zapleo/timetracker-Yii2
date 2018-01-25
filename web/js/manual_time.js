function getProjects(user, value) {
    if (value == undefined)
        value = '';

    $.ajax({
        url: base_url + '/manual-time/get-projects',
        dataType: 'json',
        type: 'POST',
        data: {
            'user': user
        },
        success: function (res) {

            $.each( res, function( key, value ) {
                $('#projects').append($('<option></option>').attr('value', key).text(value));
            });

            $('#projects').val(value).trigger('change');
            $('#projects').removeAttr('disabled');

        },
        error: function () {
            alert('Неудалось получить данные!');
        }
    });
}

function getIssues(user, project, value) {
    if (value == undefined)
        value = '';

    $.ajax({
        url: base_url + '/manual-time/get-issues',
        dataType: 'json',
        type: 'POST',
        data: {
            'user': user,
            'project': project
        },
        success: function (res) {

            $.each( res, function( key, value ) {
                $('#issues').append($('<option></option>').attr('value', value.key).text(value.key + ' - ' + value.fields.summary));
            });

            $('#issues').val(value).trigger('change');
            $('#issues').removeAttr('disabled');

        },
        error: function () {
            alert('Неудалось получить данные!');
        }
    });
}

$(document).ready(function(){
    if (update == 1) {
        var user = $('#manualtime-user_id :selected').text();

        getProjects(user, project);
        getIssues(user, project, issue);
    } else if (!is_admin) {
        getProjects(email);
    }

    // datepicker start
    $('#start_timestamp').datetimepicker({
        locale: 'ru',
        format: 'DD/MM/YYYY HH:mm',
        stepping: 10,
        //sideBySide: true,
        defaultDate: new Date()
    });

    $('#start_timestamp').on('dp.hide', function(e){
        var datetime_start = $('input#start_timestamp').val();
        var timestamp_start = moment(datetime_start, 'D/M/Y HH:mm').seconds(0).format('X');

        $('#end_timestamp').val(datetime_start);
        $('#manualtime-start_timestamp').val(timestamp_start);
        $('#manualtime-end_timestamp').val(timestamp_start);
    });
    // end

    // datepicker end
    $('#end_timestamp').datetimepicker({
        locale: 'ru',
        format: 'DD/MM/YYYY HH:mm',
        stepping: 10,
        defaultDate: new Date()
    });

    $('#end_timestamp').on('dp.hide', function(e){
        var datetime_end = $('input#end_timestamp').val();
        var timestamp_end = moment(datetime_end, 'D/M/Y HH:mm').seconds(0).format('X');

        $('#manualtime-end_timestamp').val(timestamp_end);
    });
    // end

    $('#add_time').click(function (e) {
        if ($('#manualtime-end_timestamp').val() == $('#manualtime-start_timestamp').val()) {
            alert('Промежуток времени не выбран!');
            return false;
        }

        if ($('#manualtime-start_timestamp').val() > $('#manualtime-end_timestamp').val()) {
            alert('Промежуток времени выбран не верно!');
            return false;
        }
    });

    if ($('#manualtime-start_timestamp').val().length > 0 && $('#manualtime-end_timestamp').val().length > 0) {
        $('input#start_timestamp').val(moment(parseInt($('#manualtime-start_timestamp').val()) * 1000).format("DD-MM-YYYY HH:mm"));
        $('input#end_timestamp').val(moment(parseInt($('#manualtime-end_timestamp').val()) * 1000).format("DD-MM-YYYY HH:mm"));
    } else {
        var default_timestamp = moment($('input#start_timestamp').val(), 'D/M/Y HH:mm').seconds(0).format('X');
        $('#manualtime-start_timestamp').val(default_timestamp);
        $('#manualtime-end_timestamp').val(default_timestamp);
    }

    $('#manualtime-user_id').on('select2:select', function (e) {
        var data = e.params.data;
        var user = data.text;

        $('#projects, #issues').empty();

        getProjects(user);
    });

    $('#projects').on('select2:select', function (e) {
        var data = e.params.data;
        var user = $('#manualtime-user_id :selected').text();

        $('#issues').empty();

        getIssues(user, data.id);
    });

    $('#issues').on('select2:select', function (e) {
        var data = e.params.data;

        $('#manualtime-issue_key').val(data.id);
    });

});