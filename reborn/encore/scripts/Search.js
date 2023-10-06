const $ = require('jquery');

class HeaderSearch {
    eraseAnswers(answers) {
        $('#' + answers).empty();
    }

    setUniqSearch(answers, uniqid) {
        $('#' + answers).attr('data-search-uniqid', uniqid);
    }

    searchTitle(input) {
        let answers = $(input).attr('data-fl-target');

        this.eraseAnswers(answers);

        let uniqid = Date.now();
        this.setUniqSearch(answers, uniqid);

        if ($(input).val() !== "") {
            this.sendRequest(this, input, uniqid);
        }
    }

    sendRequest(search, input, uniqid) {
        $.ajax({
            type: "POST",
            url: "http://inventory.fluid-line.ru/search",
            data: {
                "code" : $(input).val()
            },
            enctype: 'application/json',
            dataType: 'json',
            statusCode: {
                200: function (response) {
                    let value = $(input).val();

                    if (response !== undefined) {
                        if (response['search']) {
                            let answers = $('#' + $(input).attr('data-fl-target') );

                            for (let i = 0; i < response['search'].length; i++) {
                                if (Number($(answers).attr('data-search-uniqid')) !== uniqid) {
                                    return;
                                }

                                let search = response['search'][i];

                                let serial = search['serial'];
                                let code = search['code'];
                                let hint = code.replace(value, "<span class='hint text-light'>" + value + "</span>");

                                $(answers).append(
                                    "<a class=\"_answer-link bg-white text-black\" href=\"/" + serial + "/" + code + "\">" + hint + "</a>"
                                );
                            }
                        }
                    }
                },
            }
        });
    }
}

let search = new HeaderSearch();

$('._search-input').on('input', function () {
    search.searchTitle($(this))
});