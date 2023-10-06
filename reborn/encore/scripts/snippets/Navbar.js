const $ = require('jquery');

$('._node-transparent').on('mouseenter', function () {
    let node = $(this).parent();

    if (!node.hasClass('_active')) {
        node.addClass('_active');
        node.parent().addClass('_hovered');
    }
});

$('._root').on('mouseenter', function () {
    if (!$(this).hasClass('_hovered')) {
        $('._link-node._active').removeClass('_active');
        $('._root').removeClass('_hovered');
    }
});

$(window).scroll(function () {
    $('._root').removeClass('_hovered');
    $('._link-node._active').removeClass('_active');
});