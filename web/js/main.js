var USER_ID = 1;
var RIGHTS = 1;
var DOMAIN = 'http://tracker.com/';


/*var heroArray = [];

function preCacheHeros(){
    $.each(heroArray, function(){
        var img = new Image();
        img.src = this;
    });
};*/

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

$(document).ready(function(){

    var today = new Date();
    var months = $('select#months option');

    $('#months').selectpicker('val', $(months[today.getMonth()+1]).val());

    $('button#filters').on('click', function (e) {
        if ($('.second-nav').is(':visible')) {
            $('.second-nav').hide();

            //$('.second-nav').css('margin-top', '35px');
        } else {
            $('.second-nav').show();

            //$('.second-nav').css('margin-top', '50px');
        }

        if ($('nav.navbar .collapse').attr('aria-expanded') == 'true')
            $('.navbar-toggle').trigger('click');

        $('button#filters').blur();
    });

    $('#project, #task, #months').on('hidden.bs.select', function (e) {
        $('button[data-id="project"], button[data-id="task"], button[data-id="months"]').blur();
    });

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

    $('#project').on('changed.bs.select', function (e) {
        var project = e.target.value;
        var month = $('button[data-id=months]').attr('title');

        $('ul#users-list li input#user_all').prop('checked', false);

        $('button[data-id="task"]').closest('div').removeClass('hide');

        $('#task').empty();

        if (project == 'All project')
            $('button[data-id="task"]').closest('div').addClass('hide');
    });

    $('#task').on('changed.bs.select', function (e) {
        var task = e.target.value;
        var project = $('button[data-id="project"]').attr('title');
        var month = $('button[data-id=months]').attr('title');

        $('ul#users-list li input#user_all').prop('checked', false);

        if (project == 'All project' || !project)
            project = 0;
    });

    $('.dropdown-menu#users-list').click(function (e) {
        var target = $( e.target );

        if ( target.is('li') || target.is('label') )
            e.stopPropagation();
    });

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



});