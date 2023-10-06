const $ = require('jquery');

$('body').on('click', '._btn', function () {
    if ($(this).attr('data-fl-show') !== undefined) {
        let elem = $('#' + $(this).attr('data-fl-show'));

        if (elem.hasClass('_show')) {
            elem.removeClass('_show');
        } else {
            elem.addClass('_show');
        }
    }

    if ($(this).attr('data-fl-close') !== undefined) {
        let elem = $('#' + $(this).attr('data-fl-close'));

        elem.removeClass('_show');
    }

    if ($(this).attr('data-fl-text-next') !== undefined) {
        let current = $(this).html();
        let next = $(this).attr('data-fl-text-next');

        $(this).attr('data-fl-text-next', current);
        $(this).html(next);
    }
})