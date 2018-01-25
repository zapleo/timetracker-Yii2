function datetime_convert()
{
    $('.to-datetime-convert').each(function () {
        if ($(this).text().length > 1)
            $(this).text(moment(parseInt($(this).text()) * 1000).format("DD-MM-YYYY HH:mm"));
    });
}

$(document).ready(function() {
    datetime_convert();
});