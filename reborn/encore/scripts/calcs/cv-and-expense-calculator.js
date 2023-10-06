const $ = require('jquery');

var jN9j12dsID = 0;


/*
	statenotnull="1"
	Только известные среды
*/

let default_enviroment_data = {};
let searching = [];
let searching_steps = [];


let default_render_id = [];
let can_load_default = false;

let fluid_select_doners = [];
var RefNotPlease = [];


class fluid_select {
    constructor(select_id, param = {}, callBack = false) {
        this.myId = `jN9j12dsID-${++jN9j12dsID}`;
        this.select_id = select_id;
        let doner = $(`#${select_id}`);


        fluid_select_doners[this.myId] = select_id;

        doner.css("display", "none");
        const search = doner[0].getAttribute('search');


        let weight = doner[0].getAttribute('weight');
        if (weight == null) weight = '';
        let state = doner[0].getAttribute('state');
        if (state == null) state = '';

        let statenotnull = doner[0].getAttribute('statenotnull');
        if (statenotnull=="1")  statenotnull = 1;
        else statenotnull = 0;

        let select = doner[0].getAttribute('select');
        if (select == null | select == ''){
            select = '';
        }

        let task = "load";
        let params = {task: 'load', offset: 0, limit: 50, select: select, statenotnull: statenotnull};
        this.lastSearch = "";
        searching[this.myId] = '';


        //console.log(search);
        if (search != null & search != ''){
            task = 'search';
            params = {text: search, task: 'search', offset: 0, limit: 50, select: select, statenotnull: statenotnull};
        }



        const {title, classNames, styles, notRefreshOnStart} = param;
        RefNotPlease[this.myId] = notRefreshOnStart == undefined?false:notRefreshOnStart;

        doner[0].insertAdjacentHTML('afterEnd',
            `<div class="fluid-select ${classNames!=undefined?classNames:''}"
			${styles!=undefined?`style="${styles}"`:''} tabindex="1" id="${this.myId}">
				<div class="fluid-select-title">${title!=undefined?title:'выбор'}</div>
				
				<div class="fluid-select-option_group">
					<input class="fluid-select-search-input" type="text" id="${this.myId}_search" value="${task=='search'?search:''}">

					<input name="sreda_id" type="hidden" id="${this.myId}_sreda_id" value="${select}">
					<input name="weight" type="hidden" id="${this.myId}_weight" value="${weight}">
					<input name="state" type="hidden" id="${this.myId}_state" value="${state}">


					<ol class="fluid-select-list">
						
					</ol>
					<div class="fluid-select-else">
					</div>
				</div>
			</div>`);

        let opts = "";

        this.myOpt = 0;
        let myIdthis = this.myId;
        let select_idthis = this.select_id;
        fluid_select.createElem(doner, myIdthis, this.myOpt, myIdthis, select_idthis, params, callBack);

        const element = document.getElementById(this.myId);
        const search_element = document.getElementById(this.myId+'_search');

        element.onfocus 			 = function(){ fluid_select.focusIt(this);};
        search_element.onfocus		 = function(){ fluid_select.focusIt(document.getElementById(myIdthis)); }.bind(0, myIdthis);
        search_element.onkeydown	 = function(){
            setTimeout(() => fluid_select.loadFromServer(myIdthis, {text: search_element.value.trim(), task: 'search', offset: 0, limit: 50, statenotnull: statenotnull}, callBack), 1000);
            if (event.key == 'Enter') return false;
        }.bind(myIdthis, search_element);

        search_element.onchange 	 = () => {setTimeout(() => {
            fluid_select.loadFromServer(myIdthis, {
                text: search_element.value.trim(),
                task: 'search',
                offset: 0,
                limit: 50
            }, callBack);
        }, 1000);};

        element.onblur 		  		 = function(){ fluid_select.blurIt(this); };
        search_element.onblur 		 = function(){ fluid_select.blurIt(document.getElementById(myIdthis));}.bind(0, myIdthis);

        let closeButton = document.createElement('button');
        closeButton.innerHTML = 'x';
        closeButton.className = 'xinclose_button';
        closeButton.addEventListener("click",  fluid_select.SelectIClose.bind(1, 0, myIdthis, 0, '', 0, true), false);
        $(`#${myIdthis} .fluid-select-option_group`)[0].appendChild(closeButton);

        let elseButton = document.createElement('div');
        elseButton.innerHTML = 'Ещё';
        elseButton.className = 'fluid-select-search_btn';
        elseButton.setAttribute('tabindex', 0);
        elseButton.addEventListener("click", fluid_select.Else.bind(1, myIdthis, callBack), false);

        $(`#${myIdthis} .fluid-select-else`)[0].appendChild(elseButton);
    }

    static Else(id, callBack = false){
        searching_steps[id].offset += searching_steps[id].limit;
        fluid_select.loadFromServer(id, searching_steps[id], callBack);
        $(`#${id}`).focus();
    }

    static createElem(elm, myId, myOpt, myIdthis, select_idthis, params, callBack){
        let myOPTGROUP = '';

        fluid_select.loadFromServer(myId, params, callBack);
    }

    static loadFromServer(id, params, callBack = false){
        if (searching[id] != `${params.text} - ${params.offset}`){
            searching[id] = `${params.text} - ${params.offset}`;

            /*
                Если много селектов дефолтных на странице,
                чтобы не обращаться постоянно к серверу за данными
                мы можем один раз обратиться к серверу за дефолтным списком и хранить его
                в переменной default_enviroment_data
            */
            if ((can_load_default & params.offset==0 & params.task=='load' & params.select=='') |
                (can_load_default & params.offset==0 & params.task=='search' & params.text=='' & params.select=='')){

                if (Object.keys(default_enviroment_data).length==0){
                    default_render_id.push({id :id, params: params, callBack: callBack});
                    console.log(default_render_id);

                } else {
                    fluid_select.render(id, default_enviroment_data, params, callBack);
                }

            } else {
                if (params.offset==0 & params.task=='load') can_load_default = true;

                $.post('/assets/snippets/reborn/information/CVExpenseCalculator/select_for_environment/getter.php',
                    { task: params.task, params: params },
                    function (id, params, callBack, data) {

                        fluid_select.render(id, data, params, callBack);

                        if (Object.keys(default_enviroment_data).length==0){
                            if (params.offset==0 & params.task=='load'){
                                default_enviroment_data = data;

                                if (default_render_id.length != 0){
                                    console.log('Загрузили дефолт, распределяем по default_render_id');
                                    for (let itm of default_render_id){
                                        fluid_select.render(itm.id, data, itm.params, itm.callBack);

                                    }
                                    default_render_id = [];
                                }
                            }
                        }
                    }.bind(0, id, params, callBack));





                //console.log('loadFromServer');
            }
        }
    }


    static render(id, data, params, callBack = false){
        console.log(arguments);


        searching_steps[id] = params;
        if ($.isEmptyObject(data)){
            $(`#${id} .fluid-select-else`).addClass('fluid-select_empty_hide');
            if (params.offset==0)
                $(`#${id} .fluid-select-list`).html('<div class="fluid-select_empty">Запрос не найден</div>');
        } else {
            let toDrop = $(`#${id} .fluid-select-list`)[0];
            if (params.offset == 0)
                toDrop.innerHTML = "";

            let count = 0;

            for (let itm in data){
                let opx = document.createElement('li');
                opx.addEventListener("click",  fluid_select.SelectIClose.bind(1, itm, id, id, data[itm]['environment'], data[itm], false, callBack, opx), false);

                if (params.task=='search' && params.text!=''){
                    let regEx = new RegExp(params.text, "ig");
                    opx.innerHTML = data[itm]['environment'].replace(regEx, '<span class="seltext">'+params.text+'</span>') +' '+((data[itm]['weight']>50)?'[жидкость]':'[газ]');
                } else {
                    opx.innerHTML = data[itm]['environment']+' '+((data[itm]['weight']>50)?'[жидкость]':'[газ]');
                }


                let classkdf = "OPTIONX ";
                if (params.select != '' & params.select==data[itm]['id']){
                    classkdf += '__selected__';
                    $(`#${id} .fluid-select-title`).html(data[itm]['environment']);
                    $(`#${id}_weight`).val(data[itm]['weight']);
                    $(`#${id}_state`).val(data[itm]['state']);
                    $(`#${id}_sreda_id`).val(data[itm]['id']);



                    ////////////
                    console.log(`--${id}---`);
                    //$(`#${id}`).parent().find(`.sreda_title`).html(value['weight']>50?' [Жидкость]':' [Газ]');
                    //$(`#${id}`).parents('envirement_label').find(`.sreda_title`).html(value['weight']>50?' [Жидкость]':' [Газ]');


                    ////////////
                    console.log(RefNotPlease);
                    if (RefNotPlease[id]){
                        RefNotPlease[id] = false;
                    } else {
                        callBack(data[itm], opx);
                    }

                }


                opx.className = classkdf;

                //console.log(params.select);
                //console.log(data[itm]['id']);

                //if (this.parentElement.tagName == 'OPTGROUP'){
                //	opx.className+=' ingrp';
                //}
                toDrop.appendChild(opx);
                count++;
            }

            if (count<params.limit){
                $(`#${id} .fluid-select-else`).addClass('fluid-select_empty_hide');
            } else {
                $(`#${id} .fluid-select-else`).removeClass('fluid-select_empty_hide');
            }
        }
    }




    static focusIt(myIdthis){
        myIdthis.classList.add('fluid_select_focus');
    }

    static blurIt(myIdthis){
        myIdthis.classList.remove('fluid_select_focus');
    }

    static CloseFilter(myIdthis){
        $(`#${myIdthis}`).blur();
    }




    static SelectIClose(myOpt, myIdthis, select_idthis, iHTML, value = null, toClose = false, callBack = false, opx){
        $(`#${myIdthis}`).find('.__selected__').removeClass('__selected__');
        opx.classList.add('__selected__');


        if(toClose){
            $(`#${myIdthis}`).blur();
            console.log('close');
            return false;
        }

        $(`#${myIdthis}_weight`).val(value.weight);
        $(`#${myIdthis}_state`).val(value.state);
        $(`#${myIdthis}_sreda_id`).val(value.id);

        let xs = $(`#${myIdthis} .fluid-select-title`);
        xs.html(iHTML);
        xs.attr('title', iHTML);
        $(`#${myIdthis}`).blur();
        $(`#${select_idthis}`)[0].selectedIndex = myOpt-1;

        console.log(select_idthis);


        let sreda = {id: 222, name: "cs"};
        $(`#${fluid_select_doners[myIdthis]}`).change();

        if (callBack!=false)
            callBack(value, opx);



        $(`#${myIdthis}`).parent().find(`.sreda_title`).html(value['weight']>50?' [Жидкость]':' [Газ]');
    }


    static createSelectFromClass(className, callBack = false){
        let g = 0;
        fluid_select.stack = {};
        $(`.${className}`).each(function(){
            if (this.tagName=='SELECT') {
                g++;
                let id = this.getAttribute('id');
                let classNames = this.getAttribute('class');
                let style = this.getAttribute('style');
                if (id == null){
                    id = 'hidenSelect'+g
                    this.setAttribute('id', id);
                }
                let valx = $(this).find("option:selected").text();
                fluid_select.stack[id] = new fluid_select(id, {
                    title: valx,
                    classNames: classNames,
                    styles: style
                }, callBack);
            }
        });
    }
}

/** *******************************************************************************************************************/

let ifj23awle912 = 0;

let units = {
    'speed': {
        m3h: "м3/час",
        lm:  "л/мин",
        lh:  "л/час",
        ftm: "фут3/мин"
    },
    '_speed': {
        nm3h: "нм3/час",
        nlm:  "нл/мин",
        nlh:  "нл/час",
        nftm: "нфут3/мин"
    },
    'pressure' : {
        br:  "бар",
        psi: "PSI",
        pa: "Па",
        kpa: "кПа",
        mpa: "МПа"
    },
    'temp' : {
        f: "°F",
        c: "°C",
        k: "K"
    },
    'bandwidth': {
        cv: "Cv",
        kv: "Kv"
    },
    'weith': {
        kgH: "кг/час",
        kgM: "кг/мин",
        grH: "г/час",
        grM: "г/мин"
    }
};




class fluid_input {
    constructor(select_id, param = {}, callBack = false, dop_func = false) {
        this.myId = `-fluid_input-${++ifj23awle912}`;
        this.select_id = select_id;
        let doner = $(`#${select_id}`);

        //console.log(doner[0]);
        //console.log(param);

        let {title, classNames, styles, inline} = param;
        if (title==undefined){
            title = doner[0].getAttribute('title');
        }

        //if (auto_translate==undefined){
        //	auto_translate = false;
        //}







        //console.log(`inline: ${inline}`);
        let value = doner[0].getAttribute('value');
        if (value==undefined) value = 0;


        let empty = doner[0].getAttribute('empty');
        if (empty==undefined) empty = 0;

        let measure = doner[0].getAttribute('measure');
        let name = doner[0].getAttribute('name');
        let unit = doner[0].getAttribute('unit');
        let ident = doner[0].getAttribute('ident');
        if (ident==undefined) ident = '';
        let type = doner[0].getAttribute('type');
        let readonly = doner[0].getAttribute('readonly');
        if (readonly!=undefined) readonly = true;
        const error = doner[0].getAttribute('error');
        const description = doner[0].getAttribute('description');
        let unit2 = "ru";
        if (unit!=undefined && measure!=undefined){
            //console.log(unit);
            unit2 = units[measure][unit];
        }


        let imputs_measure = "";

        if (measure!=undefined){
            imputs_measure = `<input type="hidden" class="measure_input" name="${name}.measure" value="${measure.substr(0,1)=='_'?measure.substr(1):measure}">
							  <input type="hidden" class="unit_input"    name="${name}.unit" value="${unit!=undefined?(measure.substr(0,1)=='_'?unit.substr(1):unit):''}">
							  <input type="hidden"                       name="${name}.empty" value="${empty}">`;
        }


        doner[0].insertAdjacentHTML('afterEnd',
            `<label class="fluid-input ${classNames!=undefined?classNames:''} "
			${styles!=undefined?`style="${styles} ${type=="hidden"?'display: none !important;':""}"`:''} id="${this.myId}">
				<div class="fluid-input-title"><div class="layout">${title}</div></div>
				<div class="input_border ${error==1 | empty==1?'input_error':''}" ${readonly?'style="background: #eee;"':""}>
					<div class="rel">
						<div class="text input_fnt">${value}</div>`+

            (empty==1?
                `<input class="fluid-input_number input_fnt" ${readonly?"readonly":""} type="text" name="${name}.value" value="">`:
                `<input step="0.001" class="fluid-input_number input_fnt" ${readonly?"readonly":""} type="${type}" name="${name}.value" value="${value}">`)

            +`${imputs_measure}

						<div class="shower ${measure==undefined?'hide_from_see':''}">${description!=undefined?description:''}</div>
					</div>
					<div class="measure ${measure==undefined?'hide_from_see':''}">
						<div class="selected">${unit2}</div>
						<div class="select_block"></div>
					</div>
				</div>
			</label>`);


        let input = $(`#${this.myId}`).find('input');
        let text  = $(`#${this.myId}`).find('.text');



        //let loadConvert = () => {
        //	if (auto_translate){
        //		console.log("Перевод!");
        //	}
        //}


        input.on('keyup',
            () => {
                fluid_input.keyup(input[0].value, text[0]);
                if (typeof(dop_func)=="function") {
                    let cx = dop_func.bind(0, input[0]);
                    console.log(cx);
                    cx();
                    console.log(input[0]);
                }
            }
        );





        input.on('change',
            () => {
                if (typeof(callBack)=="function") callBack(input.val(), input[0], text.innerHTML, this.unit);
                //loadConvert();
            }
        );






        if (measure!=undefined){
            const new_measure = measure.substr(0,1)=='_'?measure.substr(1):measure;

            //console.log('>> '+measure);
            let select_block  = $(`#${this.myId}`).find('.select_block')[0];
            let unit_input = $(`#${this.myId}`).find('.unit_input')[0];

            let text = $(`#${this.myId}`).find('.selected')[0];

            for (let i of Object.keys(units[measure])){
                const new_i = measure==new_measure?i:i.substr(1);
                //console.log(units[measure][i]);
                //console.log(i);
                let option = document.createElement('div');
                option.classList.add('measure_option');
                option.innerHTML = units[measure][i];
                option.addEventListener("click",

                    () => {
                        this.unit = new_i;
                        fluid_input.measure_option_click(unit_input, text, [new_i, units[measure][i]]);
                        if (typeof(callBack)=="function") callBack(input.val(), input[0], text.innerHTML, this.unit );
                        //loadConvert();
                    }

                    , false);
                select_block.appendChild(option);
            }
        }
        doner.remove();
    }


    static measure_option_click(block, text, value){
        block.value = value[0];
        text.innerHTML = value[1];
        //console.log(`Выбран ${value[1]}`);
        //console.log(`Выбран ${value[0]}`);
    }


    static keyup(th, txt){
        txt.innerHTML = th;
    }


    static createInputFromClass(className, styles, callBack = false, dop_func = false){
        //console.log('---createInputFromClass '+className);
        let g = 0;
        fluid_input.stack = {};

        $(`.${className}`).each(function(){
            //console.log(this);
            if (this.tagName=='INPUT') {
                g++;
                let id = this.getAttribute('id');
                let classNames = this.getAttribute('class');
                let style = this.getAttribute('style');
                if (id == null){
                    id = 'hidenSelect'+g
                    this.setAttribute('id', id);
                }
                let valx = $(this).find("option:selected").text();
                fluid_input.stack[id] = new fluid_input(id,
                    Object.assign({
                        classNames: classNames,
                        styles: style
                    }, styles),
                    callBack, dop_func);
            }
        });
    }


    static serializator(a, b = {}){
        let new_ar = b;
        for(const i of a){
            new_ar[i.name] = i.value;
        }
        return new_ar;
    }
}

/** *******************************************************************************************************************/

let main = $('#main');

if (main[0] !== undefined) {

    const urlParams = new URLSearchParams(window.location.search);
    const myParam = urlParams.get('environment');

    let environment = urlParams.get('environment');
    if (environment === null) {
        environment = 14;
    }

    let inputs = { };

    let codeValue = urlParams.get('code');
    if (codeValue !== null) {
        inputs['code'] = {
            title: 'Code',  value: codeValue, type: 'hidden'
        };
    }

/** *******************************************************************************************************************/

    let p1 = {
        measure: "pressure",
        title: "Входное давление (абс.)"
    };

    let p1Unit = urlParams.get('p1_unit');
    if (p1Unit === null) {
        p1['unit'] = "br";
    } else {
        p1['unit'] = p1Unit;
    }

    let p1Value = urlParams.get('p1');
    if (p1Value === null) {
        p1['value'] = "";
        p1['empty'] = 1;
    } else {
        p1['value'] = p1Value;
    }

/** *******************************************************************************************************************/

    let p2 = {
        measure: "pressure",
        title: "Выходное давление (абс.)"
    };

    let p2Unit = urlParams.get('p2_unit');
    if (p2Unit === null) {
        p2['unit'] = 'br';
    } else {
        p2['unit'] = p2Unit;
    }

    let p2Value = urlParams.get('p2');
    if (p2Value === null) {
        p2['value'] = "1";
    } else {
        p2['value'] = p2Value;
    }

/** *******************************************************************************************************************/

    let temp = {
        measure : "temp",
        title : "Температура"
    };

    let tempUnit = urlParams.get('temp_unit');
    if (tempUnit === null) {
        temp['unit'] = "c";
    } else {
        temp['unit'] = tempUnit;
    }

    let tempValue = urlParams.get('temp');
    if (tempValue === null) {
        temp['value'] = 21;
    } else {
        temp['value'] = tempValue;
    }

/** *******************************************************************************************************************/

    let dn = {
        title : "Минимальный условный проход, мм"
    };

    let dnValue = urlParams.get('dn');
    if (dnValue === null) {
        dn['value'] = "";
    } else {
        dn['value'] = dnValue;
    }

/** *******************************************************************************************************************/

    let mode = {
        type: 'radio',
        var: {
            consumption: "Расчитать расход",
            'cv-kv': "Расчитать Cv/Kv"
        }
    };

    let modeValue = urlParams.get('mode');
    if (mode === null) {
        mode['value'] = "consumption";
    } else {
        mode['value'] = modeValue;
    }

/** *******************************************************************************************************************/

    let bandwidth = {
        title : "Пропускная способность",
        measure : "bandwidth"
    };

    if (mode !== "consumption") {
        bandwidth['type'] = "hidden";
    } else {
        bandwidth['type'] = "number";
    }

    if (bandwidth['type'] !== null && codeValue !== null) {
        bandwidth['empty'] = 1;
    } else {
        bandwidth['empty'] = 0;
    }

    let bandwidthUnit = urlParams.get('bandwidthUnit');
    if (bandwidthUnit === null) {
        bandwidth['unit'] = "cv";
    } else {
        bandwidth['unit'] = bandwidthUnit;
    }

    let bandwidthValue = urlParams.get('bandwidth');
    if (bandwidthValue === null) {
        bandwidth['value'] = "";
    } else {
        bandwidth['value'] = bandwidthValue;
    }

/** *******************************************************************************************************************/

    let rate = {
        title: "Расход",
        measure: "speed"
    };

    if (mode !== "cv-kv") {
        rate['type'] = "number";
    } else {
        rate['type'] = "hidden";
    }

    let rateValue = urlParams.get('rate');
    if (rateValue === null) {
        rate['value'] = "";
    } else {
        rate['value'] = rateValue
    }

    let rateUnit = urlParams.get('rate_unit');
    if (rateUnit === null) {
        rate['unit'] = "m3h";
    } else {
        rate['unit'] = rateUnit;
    }

/** *******************************************************************************************************************/

    inputs = {
        mode : mode,
        p1 : p1,
        p2 : p2,
        temp : temp,
        dn : dn,
        bandwidth : bandwidth,
        rate : rate
    };

/** *******************************************************************************************************************/

    let send_counters = {};

    class fluid_form {
        constructor(select_id, param = { }, functions = { }) {

            this.myId = select_id;
            let doner = $(`#${select_id}`);
            console.log('-------------------');
            console.log(param);

            send_counters = Object.assign({[select_id]: 0});

            let {section, style, inputs, settings} = param;
            let {callback, on_submit, on_create} = functions;

            console.log('[[[param]]]');
            console.log(param);

            if (typeof(settings)!='object') settings = {};

            let detal = "";
            if ("code" in Object.keys(inputs)) {
                detal = "Изделие: "+inputs.code.value;
            }

            let sreda_id = style.sreda_id;
            if (sreda_id == undefined) {
                sreda_id = 1;
            }

            let kor_style = "";
            if (('fluid_form_mode' in style) && style.fluid_form_mode=='korr') {
                kor_style = "width: 100%;";
            }

            doner.html(
                `<div class="dataform" style="${kor_style}">
                    ${('fluid_form_mode' in style)?`<input type='hidden' value='${style.fluid_form_mode}' name='calc_mode'>`:''}
                    <span class="updatered"></span>
                    <hr>
                    <span class="not_update">
                        ${detal}
                        <label class="envirement_label"  ${style.input!=undefined?"style='"+style.input+"'":""}>
                            <div style="font-size: 14px; " class="fluid-input-title">Среда <span class="sreda_title" style="margin-left: 5px; color: #f44;"></span></div>
                            <select statenotnull="1" select="${sreda_id}" id="${select_id}_sreda" class="sred_list" style="width:100%; max-width: 180px; background: #fff;">
                                <option>Выберите среду</option>
                            </select>
                        </label>
                    </span>
                    <span class="compat"></span>
                </div>
                <div class="ret"></div>
            `);

            let updatered = doner.find('.updatered');

            fluid_form.render(select_id, section, style, settings, inputs, updatered,
                () => fluid_form.send(select_id, doner, style, settings, callback)
            );

            doner.on('submit', function(){
                fluid_form.send(select_id, doner, style, settings, callback);
                return false;
            });

            console.log(fluid_input);

            fluid_input.createInputFromClass('customInputs', {}, function() {
                fluid_form.send(select_id, doner, style, settings);
            }, fluid_form.send.bind(0, select_id, doner, style, settings, callback, true));

            new fluid_select(`${select_id}_sreda`, {styles: 'width: 100%'}, function(id, data, th){
                fluid_form.send(select_id, doner, style, settings, callback);
            }.bind(0, select_id));

            if (typeof (on_create) == 'function') {
                on_create(this.myId);
            }
        }

        static serializator(a, b = { }) {
            let new_ar = b;
            for (const i of a) {
                new_ar[i.name] = i.value;
            }
            return new_ar;
        }

        static send(id, th, style, settings, callback, not_update = false, input = false){

            console.log('===========================' + typeof(callback));
            console.log(arguments);

            let data = fluid_form.serializator(th.serializeArray());
            console.log('Отправляем');
            console.log(data);

            $.post('/assets/snippets/reborn/information/CVExpenseCalculator/calc/c.php',
                data,
                function(id, th, style, settings, callback, not_update, data){

                    const {write_in_url, hide_class_if_empty} = settings;

                    console.log('Принимаем');
                    console.log(data);

                    if (typeof (callback) == 'function') {
                        callback(data, id);
                    }


                    if (data.calc_mode=='korr'){
                        if (
                            data.inputs.p1.type=='hidden' &&
                            data.inputs.p2.type=='hidden' &&
                            data.inputs.temp.type=='hidden' &&
                            data.inputs.dn.type=='hidden' &&
                            data.inputs.rate.type=='hidden' &&
                            data.inputs.bandwidth.type=='hidden' &&
                            data.inputs.consumption2.type=='hidden'
                        ){
                            th.css('visibility', 'hidden');
                        }
                    }

                    send_counters[id]++;

                    let inputs = data.inputs;
                    let reters = data.returned;
                    let section = data.section;

                    if (!not_update){
                        let updatered = $(`#${id}`).find('.updatered');
                        updatered.html('');

                        fluid_form.render(id, section, style, settings, inputs, updatered, () => fluid_form.send(id, th, style, settings, callback) );

                        fluid_input.createInputFromClass('customInputs', {}, function() {
                            fluid_form.send(id, th, style, settings, callback);
                        }, fluid_form.send.bind(0, id, th, style, settings, callback, true));

                    } else {

                        if (input != false) {
                            let nm = input.getAttribute('name').split('.')[0];

                            const vals = data.inputs[nm].value;
                            const gtitle = data.inputs[nm].info

                            let ops = (gtitle!=undefined?gtitle+"<hr>":'');

                            for (let itmm in vals) {
                                ops += data.inputs[nm].ident + ' = ' + vals[itmm] + ' ' + units[data.inputs[nm].measure][itmm]+'<br>';
                            }

                            const border = $(input).parents(".input_border");
                            if (data.inputs[nm].error == 1) border.addClass('input_error');
                            else border.removeClass('input_error');
                            border.find('.shower').html(ops);

                            if (data.calc_mode == "korr") {
                                const consumption2 = $("#main").find('[name="consumption2.value"]');
                                const gblock =  consumption2.parents('.fluid-input');
                                gblock.find('.layout').html(data.inputs.consumption2.title);
                                const _consumption2 = data.inputs.consumption2;

                                if (_consumption2.error==0){
                                    gblock.find('.input_border').removeClass('input_error');
                                } else {
                                    gblock.find('.input_border').addClass('input_error');
                                }

                                consumption2.val(_consumption2.value[_consumption2.unit]);
                                consumption2.find('.input_fnt').html(_consumption2.value[_consumption2.unit]);
                            }
                        }
                    }

                    /** set url */
                    if (write_in_url != false) {
                        let url = "";

                        for (let name of Object.keys(data.inputs)) {
                            const itm = data.inputs[name];
                            url += `&${name}=${(typeof(itm.value)=='object'?(itm.error!='1'?itm.value[itm.unit]:'_')+`&${name}_unit=${(itm.measure.substr(0, 1)=="_"?itm.unit.substr(1):itm.unit)}`:itm.value)}`;
                        }

                        url = location.origin+ location.pathname+'?'+url.substr(1);

                        history.pushState(null, null, url);
                    }

                    let ops = "";

                    if (reters.errors.value!=''){
                        ops += '<div class="errors_panel">'+reters.errors.value+"</div>";
                    }
                    ops += "<div class='p10'>";
                    if (data.calc_mode != "korr")
                        ops += "<div class='_ret-title'>Результат</div>";

                    for (let name of Object.keys(reters)) {
                        if (name!="errors"){
                            ops += `<h4 class='_ret-subtitle'>${reters[name].title}</h4>`;

                            if (typeof(reters[name].value)=='object'){
                                Object.keys(reters[name].value).map((key ,index) => {
                                    if (name=="cv"){
                                        ops += `<div class="dtc"><span class="edz">${units[reters[name].measure][key]}</span> = ${reters[name].value[key]}</div>`;
                                    } else {
                                        ops += `<div class="dtc">${reters[name].value[key]} <span class="edz">${reters[name].title=="Расход газа"?'н':''}${units[reters[name].measure][key]}</span></div>`;
                                    }

                                });
                            } else {
                                ops += `<div>${reters[name].value}</div>`;
                            }
                        }
                    }
                    ops += "</div>";
                    $(`#${id}`).find('.ret').html(ops);

                }.bind(0, id, th, style, settings, callback, not_update)
            );
        }

        static update() { }

        static render(id, section, style, settings, inputs, updatered, callback = false){

            let mode = '';

            if ("fluid_form_mode" in style) {
                mode = style.fluid_form_mode;
            }

            if (section!=undefined && section.matr!=undefined && section.matr.length != 0){

                let table = `<table class="section_table" width="100%" cellspacing="0">
				<tr style="background: #ccc;">
					<td>№</td>
					<td>Материал</td>
					<td>Деталь</td>
					<td>Совместимость</td>
				</tr>
			`;
                for (let name of Object.keys(section.matr)) {
                    if (section.matr[name].ops!=null){
                        let itm = section.matr[name].ops.split("|");
                        table += `<tr ${section.matr[name].tempOut==1?"style='background: #ff6868;'":"" }>
								<td>${itm[2]}</td>
								<td><div class="vsp_father">${section.matr[name].mat}
									<div class="vsp">
										Основные названия: <b>${section.matr[name].russian}</b><br>
										Другие названия: <b>${section.matr[name].another}</b><br>
										Рекомендуемая температура: <b>${section.matr[name].temp}</b>
										<div style="text-align: center">
											<a target="_blank" href="http://fluid-line.ru/koroziya?sel=${inputs['environment']['value']}&sel1=${section.matr[name].id}">Подробнее...</a>   <!-- Link #сслка на seetru -->
										</div>
									</div>
								</div></td>
								<td>${itm[0]}${itm[1]==1?"*":""}</td>
								<td><a class="linkbtn sovm_${section.matr[name].sov} ${section.matr[name].sov_q==1?'ques':''}" target="_blank" href="http://fluid-line.ru/koroziya?sel=${inputs['environment']['value']}&sel1=${section.matr[name].id}">
									${section.matr[name].sov==4?"рекомендуется":
                            (section.matr[name].sov==3?"удовлетворительно":
                                (section.matr[name].sov==2?"не подойдет":
                                    (section.matr[name].sov==1?"не рекомендуется":"неизвестно")))
                        }</a></td>
							</tr>`;
                    }
                }

                table += `</table>`;

                let bnv = ` <div ${style.input!=undefined?"style='"+style.input+"'":""}>
						<label ${style.input!=undefined?"style='"+style.input+"'":""}>
							<div style="font-size: 14px; " class="fluid-input-title">Совместимость со средой</div>
							<div class="statusbar ${section.allq==1?'ques':''}">
								<div class="status_title ">

									<span style="padding: 4px;" class=" text_sovm_${section.all} ">
										${section.all==4?"рекомендуется":
                    (section.all==3?"удовлетворительно":
                        (section.all==2?"не подойдет":
                            (section.all==1?"не рекомендуется":"неизвестно")))}
									</span>
								</div>
								<div class="status_block">
									<div style="padding: 10px">
										<b>Совместимость деталей: </b><br>
										Все детали: <span class="text_sov text_sovm_${section.all} ${section.allq==1?'ques':''}">
											${section.all==4?"рекомендуется":
                    (section.all==3?"удовлетворительно":
                        (section.all==2?"не подойдет":
                            (section.all==1?"не рекомендуется":"неизвестно")))}
										</span><br>
										*контактирующие со средой: <span class="text_sov text_sovm_${section.contact} ${section.contactq==1?'ques':''}">
											${section.contact==4?"рекомендуется":
                    (section.contact==3?"удовлетворительно":
                        (section.contact==2?"не подойдет":
                            (section.contact==1?"не рекомендуется":"неизвестно")))}
										</span>
										<br>
										 <b>Cписок делатей:</b>
									</div>
									${table}
								</div>
							</div>
						</label>
						</div>`;

                $(`#${id}`).find('.compat').html(bnv);
            }

            for (let name of Object.keys(inputs)) {
                if (inputs[name]['type']=='radio'){

                    let block = document.createElement('div');
                    block.className = "radioBlock";

                    if (mode == 'korr') {
                        block.style.display = 'none';
                    }

                    for (let vr in inputs[name]['var']) {
                        let label = document.createElement('label');
                        label.innerHTML = `<div class="frm_btn" offset="0">${inputs[name]['var'][vr]}</div>`;

                        let imp = document.createElement('input');
                        imp.name = name;
                        imp.id = name+'_'+vr+'_';
                        imp.className = "hiden_radio";
                        label.setAttribute('for', name+'_'+vr+'_');

                        imp.type = "radio";
                        imp.value = vr;
                        if (inputs[name]['value'] == vr){
                            imp.checked = true;
                        }
                        imp.onchange = callback;

                        block.appendChild(imp);
                        block.appendChild(label);
                    }

                    updatered[0].appendChild(block);

                } else {
                    let imp = document.createElement('input');
                    imp.name = name;
                    imp.type = "number";

                    imp.className = "customInputs";

                    if (style.input!=undefined) imp.style = style.input;
                    for (let parm of Object.keys(inputs[name])) {  /////////////////  "ДУБЛЬ"

                        if (parm == 'value' && typeof(inputs[name][parm]) == 'object'){

                            imp.setAttribute(parm, inputs[name][parm][inputs[name]["unit"]  ]);
                            let ops = "";
                            let ident = inputs[name]['ident'];
                            let info = inputs[name]['info'];
                            if (info==undefined) info = "";
                            else info+="<hr>";
                            let show_pzx = true;
                            if (ident=="key"){
                                show_pzx = false;
                            }

                            ops += info;

                            for (let i of Object.keys(inputs[name][parm])){
                                ops += `${show_pzx?ident:units[inputs[name]["measure"]][i]} = ${inputs[name][parm][i]} ${show_pzx?units[inputs[name]["measure"]][i]:""}<br>`;
                            }
                            imp.setAttribute("description", ops);

                        } else {
                            imp.setAttribute(parm, inputs[name][parm]);
                        }
                    }

                    updatered[0].appendChild(imp);
                }
            }
        }
    }

    new fluid_form('main', {
            style: {
                input: "color: #222;",
                sreda_id: environment
            },
            inputs: inputs
        },
        function(data) {
            alert(data);
        },
        function(data) {
            console.log(data);
        }
    );
}

/** *******************************************************************************************************************/