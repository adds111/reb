const $ = require('jquery');

let stuff = $('._products-stuff-info');

if (stuff[0] !== undefined) {
    let switcher = $('._button-switcher');

    $(switcher).on('click', function () {
        let activeElement = $(this).attr('data-fl-active');

        $('._button-switcher._active').removeClass('_active').parent('._products-stuff-info');
        $('._card._active').removeClass('_active').parent('._products-stuff-info');

        $('._card-' + activeElement).addClass('_active').parent('._products-stuff-info');
    });

    $(document).ready(function() {
        ym(5484148, 'reachGoal', 'tovarchik');
    });
}
