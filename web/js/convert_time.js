function datetime_convert()
{
    $('.to-datetime-convert').each(function () {
        if ($(this).text().length > 1)
            $(this).text(moment(parseInt($(this).text()) * 1000).format("DD-MM-YYYY HH:mm"));
    });
}

$(document).ready(function() {
    datetime_convert();

    $('button#all:not(.disabled), button#pending:not(.disabled), button#rejected:not(.disabled), button#added:not(.disabled)').click(function (e) {
        var user = $('#users').val();

        location.href = '/manual-time/?status=' + $(this).attr('id') + (user.length > 0 ? '&user=' + user : '');
    });

    $('#users').on('select2:select', function (e) {
        var user_id = $(this).val();

        location.href = '/manual-time/?status=' + $(this).data('status') + '&user=' + user_id;
    });
});