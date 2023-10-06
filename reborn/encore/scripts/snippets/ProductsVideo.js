const $ = require('jquery');

$(document).ready(e => {
    let video = $('.video');

    if (video[0] !== undefined) {
        $(video).on('click', function () {
            const videoId = this.getAttribute('data-video-id');
            const time = this.dataset.time;
            const title = $(this).closest('.video').find('.video-title').text();
            const modal = $('.modal-video');

            modal.addClass('show');

            //Метрика
            ym(5484148, 'reachGoal', 'viewvideo', {
                URL: document.location.href
            });

            //Закрыть модальное окно и удалить iframe
            modal.find('.close').on('click', e => {
                const modal = $(e.target).closest('.modal');
                modal.modal('hide');
                modal.find('.modal-body').html('<div id="ytplayer"></div>');
            });

            //удалить iframe при закрытии модального окна по умолчанию
            modal.on('click', function (e) {
                if (e.target.closest('.modal-dialog') === null)
                    modal.find('.modal-body').html('<div id="ytplayer"></div>');
            });

            //указать название видео
            modal.find('.modal-title').html(title);

            //Добавить iframe
            const tag = document.createElement('script');
            tag.src = "https://www.youtube.com/player_api";
            const firstScriptTag = document.getElementsByTagName('script')[0];
            firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
            let width = screen.width < 600 ? $('.modal-content').outerWidth() - 50 : 640;
            let height = screen.width < 600 ? width / 1.77 : 360;

            new YT.Player('ytplayer', {
                height: height,
                width: width,
                videoId: videoId,
                playerVars: {
                    autoplay: 1,
                    start: time
                }
            });
        });
    }
});