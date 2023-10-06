const $ = require('jquery');

$(document).ready(function () {
    let scrollButton = document.getElementById('flClimb');

    function scrollToTop () {
        $('html, body').animate({ scrollTop: 0 }, 250);
    }

    scrollButton.addEventListener('click', scrollToTop);
});

$(document).scroll(function () {
    let scroll = $(this).scrollTop();
    let scrollButton = $('#flClimb');

    if (scroll > 500) {
        if (scrollButton.hasClass('_hidden')) {
            scrollButton.removeClass('_none');
            setTimeout(() => {
                scrollButton.removeClass('_hidden');
            }, 500);
        }
    } else {
        if (!scrollButton.hasClass('_hidden')) {
            scrollButton.addClass('_hidden');

            setTimeout(() => {
                scrollButton.addClass('_none');
            }, 500);
        }
    }
});