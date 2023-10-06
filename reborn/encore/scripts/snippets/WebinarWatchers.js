const $ = require('jquery');



function fetchfunc(
    action, callback, json, headers = false, method = 'post', before = () => { }
) {
    before();

    const init = {
        method: method,
    };
    if (headers)
        init.headers = new Headers(headers);

    if (json)
        init.body = JSON.stringify((data = json));
console.log(init);
    fetch(action, init)
        .then(response => response.json())
        .then(result => callback(result))
}



$(document).ready(function () {
    console.log('loaded.');

    $('.vebinar-grid-item.passed.hidden').hide();

    let vebinarsIds = [];

    $('.look-vebinar-details').on('click', function () {
        const details = $(this).closest('tr').next();
        if (details.css('display') === 'none') {
            details.css({ display: 'table-row' });
            window.scrollTo({ top: details.offset().top - 50, behavior: 'smooth' });
        } else
            details.hide();
    });

    $('input[name="vebinar-id"]').on('change', function () {
        let index = vebinarsIds.indexOf(this.value);
        $('#choose-vebinar').removeClass('alerted');
        $('.registration-result .fa.fa-square-o').removeClass('select-vebinar-animation');
        if (this.checked) {
            $(this).closest('label').find('.fa').attr('class', 'fa fa-check-square-o');
            if (index === -1)
                vebinarsIds.push(this.value);
        } else {
            $(this).closest('label').find('.fa').attr('class', 'fa fa-square-o');
            if (index !== -1)
                vebinarsIds.splice(index, 1);
        }
    });

    $('input[name="sms_reminder"], input[name="whatsApp_reminder"]')
        .on('change',
            e => $(e.target).closest('label')
                .find('.fa')
                .attr('class', 'fa fa-' + (e.target.checked ? 'check-' : '') + 'square-o')
        );


    const calendarData = {};

    function validate(){
        var $dataval = {};
        $('#registration-form').find('input, textearea, select').each(function(){
            $dataval[this.name] = $(this).val();
        })
        if(($dataval['phone'].length == 11 || $dataval['phone'].length == 12) && ($dataval['name'].includes(' ') && $dataval['name'] != ' ') && (!$dataval['phone'].includes(' ') && !$dataval['name'].startsWith(' '))){
            return true;
        }else{
            return false;
        }
    
    }
    //Запись на вебинар
    $('#registration-form').on('submit', function (e) {

        e.preventDefault();

        if (!validate()){
        alert("Данные не корректны");    
        return false;}else if (vebinarsIds.length === 0) {
            alert('Вы не выбрали ни одного вебинара');
            return false;
        }
var $dataval = {};
$('#registration-form').find('input, textearea, select').each(function(){
    $dataval[this.name] = $(this).val();
})
console.log($dataval);
         const data = {
         text : $dataval,
         ids : vebinarsIds,
         method : 'registration'
         }
         console.log(data);
        const submitter = $(this).find('[type="submit"]');

        
        const callback = response => {
            console.log(response);

            if (response.result) {
                const modal = $('.modal-result');
                modal.show();
                modal.find('.close, .close-btn').on('click', () => modal.hide());

                // const data = {
                //     webinar_name: response.calendar.title,
                //     webinar_description: response.calendar.content,
                //     webinar_time_start: response.calendar.date,
                //     method: 'calendar_creator'
                // };
                // calendar(data);
            } else {
                if (response.note)
                    submitter.html(response.note);
            }

        };

        fetchfunc('/assets/reborn/snippets/webinars/Class/vebinar.class.php', callback, data);
    });


    $('.select-vebinar-rr').on('click', function () {
        //const action = modal.find('form').attr('action');



        const data = {
            id: $(this).attr('data-id'),
            method: 'get_vebinar_details'
        };
        const callback = response => {
            console.log(response);
            if (response.result) {

                const modal = response.data.status ? $('.modal-form') : $('.modal-watch');

                modal.modal('show');
                modal.find('.close, .close-btn').on('click', () => modal.modal('hide'));

                const title = response.data.title;
                const description = response.data.description;

                const form = modal.find('#registration-form');
                const submitter = form.find('[type="submit"]');
                submitter.html('Участвовать');

                modal.find('.modal-title b').html(title);
                modal.find('#vebinar-description').html(description);
                modal.find('#vebinar-content').html(response.data.content);
                modal.find('#vebinar-img-preview').html(response.data.image);
                modal.find('input[name="vebinar_id"]').val(response.data.id);

                if (response.data.video) {
                    const video = response.data.video.split('/');
                    const videoId = video[video.length - 1].replace(/\?.*/, '');
                    const tag = document.createElement('script');
                    tag.src = "https://www.youtube.com/player_api";
                    const firstScriptTag = document.getElementsByTagName('script')[0];
                    firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

                    new YT.Player('ytplayer', {
                        height: 360,
                        width: 720,
                        videoId: videoId
                    });
                }

                calendarData.title = modal.find('.modal-title b').text();
                calendarData.description = modal.find('#vebinar-description').text();
                if (calendarData.description.length > 500)
                    calendarData.description = calendarData.description.slice(0, 500) + '...';
                calendarData.dateTime = response.data.date;


            }
        };
        fetchfunc('/assets/reborn/snippets/webinars/Class/vebinar.class.php', callback, data);
    });


    $('#show-all-vebinars').on('click', function () {
        const hiddenVebinars = $('.vebinar-grid-item.passed.hidden');
        var count = 0;
        var elem = true;
        var arr = [];
        for (let i = 0; i < hiddenVebinars.length; i++) {
            elem = getComputedStyle($('.vebinar-grid-item.passed.hidden')[i]).display == "none";
            if (elem) {


                $('.vebinar-grid-item.passed.hidden')[i].setAttribute("style", "display: ");
                count = count + 1;
                console.log(count);
                if (count == 6) {
                    break;
                }

            }
            arr[i] = elem;
        }

        if (arr[arr.length - 6] == false && arr.length == hiddenVebinars.length) {
            $(this).closest('center').hide();
        } else if (arr.length == 3) {
            $(this).closest('center').hide();
        }



        const top = $(this).offset().top - 800;
        console.log(top);
        if (Math.sign(top) != -1) {
            window.scrollTo({
                top: top,
                behavior: 'smooth'
            });
        }
    });

    function getDetails(id) {
        const description = $('#vebinar-details-description');
        const data = {
            id: id,
            method: 'get_vebinar_details'
        };

        const callback = response => {
            console.log(response);
            if (response.result) {
                description.show().html(response.data.content);
                window.scrollTo({
                    top: description.offset().top - description.outerHeight(),
                    behavior: 'smooth'
                });

            }

        };
        fetchfunc('/assets/reborn/snippets/webinars/Class/vebinar.class.php', callback, data);
    }


    $('.vebinar-grid-item.upcoming').on('click', function (e) {

        if ($(e.target).closest('.webinar-card-link').length) {
            window.open($(e.target).closest('.webinar-card-link').attr('href'));
            return false;
        }


        if ($(e.target).hasClass('veninar-details'))
            return getDetails($(e.target).attr('data-id'));

        const checkbox = $(this).find('input[type="checkbox"]')[0];
        const form = $('#registration-form');
        const fa = $(this).find('.vebinar-is-selected');
        const banner = $('.banner');

        checkbox.checked = !checkbox.checked;
        if (checkbox.checked) {
            vebinarsIds.push(checkbox.value);
            $(this).addClass('checked');
            fa.removeClass('fa-square-o').addClass('fa-check-square-o');
        } else {
            $(this).removeClass('checked');
            const index = vebinarsIds.indexOf(checkbox.value);
            fa.addClass('fa-square-o').removeClass('fa-check-square-o');
            vebinarsIds.splice(index, 1);
        }

        if (vebinarsIds.length) {
            form.show();
            banner.show();
        } else {
            banner.hide();
            form.hide();
        }

    }).hover(
        function () {
            if (!$(this).hasClass('checked'))
                $(this).find(' .vebinar-is-selected ').removeClass('fa-square-o').addClass('fa-check-square-o')
        },
        function () {
            if (!$(this).hasClass('checked'))
                $(this).find(' .vebinar-is-selected ').removeClass('fa-check-square-o').addClass('fa-square-o')
        }
    );


    $('input[name="check-all"]').on('change', function () {
        const vebinars = $('.vebinar-grid-item.upcoming');
        const checkboxes = vebinars.find('input[type="checkbox"]');
        const fa = $(this).closest('label').find('.fa');
        const form = $('#registration-form');
        const banner = $('.banner');
        vebinarsIds = [];
        if (this.checked) {
            vebinars.addClass('checked');
            checkboxes.prop('checked', 1);
            const ids = getElemetsAttributes(checkboxes, 'value');
            vebinarsIds = ids['value'];
            fa.addClass('fa-check-square-o').removeClass('fa-square-o');
            form.show();
            banner.show();
            vebinars.find(' .vebinar-is-selected ').removeClass('fa-square-o').addClass('fa-check-square-o')
        } else {
            vebinars.removeClass('checked');
            checkboxes.prop('checked', 0);
            fa.removeClass('fa-check-square-o').addClass('fa-square-o');
            form.hide();
            banner.hide();
            vebinars.find(' .vebinar-is-selected ').removeClass('fa-check-square-o').addClass('fa-square-o')
        }
        console.log(vebinarsIds);
    });

    $('.banner').on('click', function () {

        let form = $('#registration-form');
        form.show();
        console.log("mem");
        $(this).hide();
        const top = $('#registration-form').offset().top - 50;


        window.scrollTo({
            top: top,
            behavior: 'smooth'
        });
    });


    $('.vebinar-filter').on('click', function () {


        if ($(this).hasClass('tag-primary'))
            return false;

        const data = {
            id: this.parentNode.getAttribute('data-modx-identifier'),
            tag: this.getAttribute('data-filter-value'),
            method: 'vebinar_filter'
        };

        const callback = response => {
            console.log(response);
            if (response.result) {
                const btn = $('#show-all-vebinars').closest('center');
                $('.vebinar-filter').removeClass('tag-primary').addClass('tag-default');
                $(this).removeClass('tag-default').addClass('tag-primary');
                $('.passed-vebinars').html(response.html);

                let newHref = '';

                if (window.location.href.match(/\?tag=[a-z]/gi))
                    newHref = data.tag !== 'default' ?
                        window.location.href.replace(/\?tag=[a-z]+/gi, '?tag=' + data.tag) :
                        window.location.href.replace(/\?tag=[a-z]+/gi, '');
                else
                    newHref = window.location.href + '?tag=' + data.tag;
                console.log(newHref);
                history.pushState(null, null, newHref);

                if (response.data.length > 6)
                    btn.show();
                else
                    btn.hide();
            }
        };

        fetchfunc('/assets/reborn/snippets/webinars/Class/vebinar.class.php', callback, data);

    });



    $('input[name="company"]').on('keyup', function () {

        if (this.value.length < 3)
            return false;

        const data = {
            method: 'company_list',
            text: this.value
        };
        
        
        const callback = response => {
            
         
            console.log(response);

            let companyListContainer = $('#company-list');
            
            if (response.length) {
                

                var arr= [];    
              for(let i= 0 ; i<response.length;i++){
                arr[i] = response[i]['companyName'];

              }
          
              var companyList = arr.map(item => `<li class="company-list-item" >${item}</li>`);
              companyListContainer.show().html(companyList.join(''));
                $('body').on('click', e => {
                    if (!$(e.target).closest('#company-list').length)
                        companyListContainer.hide().html('');
                });
            } else {
                companyListContainer.hide().html('');
            }
        };

       


fetchfunc('/assets/reborn/snippets/webinars/Class/vebinar.class.php', callback, data);



    });
});
$('#company-list').on('click',function(event){
    let ev = event.target;
   
  $("input[name='company']").val(ev.textContent);
    $('#company-list').hide(); 
})

// function setCompany(event) {
//     // $("input[name='company']").val(item.innerText);
//     // // item.parentElement.style.display = "";
//     // // item.parentElement.innerHTML = "";
// let text = event.target.text();
//     // $('.company-list-item')
//     console.log(text);
//     $("input[name='company']").val(text);
    
// }