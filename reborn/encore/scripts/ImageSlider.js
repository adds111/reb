const $ = require('jquery');

class ImageSlider {
    constructor(slider) {
        this.slider = slider;

        this.view = slider.find('#imagePreview');
        this.storage = slider.find('#imageStorage');
    }

    getStorage() {
        return this.storage;
    }

    setView(src) {
        let imageView = this.view.find('img')[0];
        imageView.setAttribute('src', src);
    }

    clearActive() {
        let storageChildrens = this.storage.children();

        storageChildrens.each(function (index) {
            let storageChild = storageChildrens.eq(index);

            if ($(storageChild).hasClass('_active')) {
                $(this).removeClass('_active');
            }
        });
    }
}

let info = $('._products-info');

if (info[0] !== undefined) {
    let slider = new ImageSlider($('#imageSlider'));
    let storage = slider.getStorage();

    storage.children().each(function (index) {
        let storageChild = storage.children().eq(index);

        if ($(storageChild).hasClass('_active')) {
            slider.setView($(this).find('img')[0].getAttribute('src'));
        }

        $(storageChild).on('click', function () {
            let imageSRC = $(this).find('img')[0].getAttribute('src');
            slider.setView(imageSRC);
            slider.clearActive();
            $(this).addClass('_active');
        });
    });
}