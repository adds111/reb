const $ = require('jquery');

class ModelView {
    constructor(model) {
        this.model = model;
        this.select = this.model.find('._model-select');
        this.download = this.model.find('._model-select-download');
    }

    hide() {
        this.model.removeClass('_show');
    }

    setData(modelView) {
        let view = JSON.parse(atob(modelView));
        this.setOption(view['attachments']);
    }

    setOption = function (attach) {
        this.select.empty();

        for (let key in attach) {
            let selected = "";

            if (key === "0") {
                selected = "selected";
                this.download.attr(
                    'href', 'https://visitourmodel.ru/downloader.php?file=' + attach[key]['id'] + '&u=undefined'
                );
            }

            this.select.append(
                '<option ' + selected +
                    ' value="https://visitourmodel.ru/downloader.php?file=' + attach[key]['id'] +
                    '&u=undefined">' + attach[key]['title'] +
                '</option>'
            );
        }
    }

    getModel() {
        return this.model;
    }

    getSelect() {
        return this.select;
    }

    getDownload() {
        return this.download;
    }
}

let modelView = $('#modelView');

if (modelView[0] !== undefined) {
    let model = new ModelView(modelView);

    $(model.getSelect()).on('change', function () {
        model.getDownload().attr('href', $(this).val());
    });

    $('body').on('click', '._btn', function () {
        if ($(this).attr('data-fl-model') !== undefined) {
            model.setData($(this).attr('data-fl-model'));
        }
    });

    $(window).scroll(function () {
        model.hide();
    });
}

let stuffModel = $('#stuffModel');

if (stuffModel[0] !== undefined) {
    let stuff = new ModelView(stuffModel);

        $(stuff.getSelect()).on('change', function () {
            stuff.getDownload().attr('href', $(this).val());
    });

    $(document).ready(function () {
        let resourceModel = stuff.getModel();
        stuff.setData(resourceModel.find('._model-select')[0].getAttribute('data-fl-model'));
    });
}