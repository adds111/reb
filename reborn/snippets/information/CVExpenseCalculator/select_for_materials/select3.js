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



		const {title, classNames, styles} = param;

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
		//console.log(id);
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

			//console.log(Object.keys(default_enviroment_data).length);
			//console.log(params.offset);
			//console.log(params.task);
			//console.log(params.select);


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
					//console.log('add in stack');
					console.log(default_render_id);
				} else {
					fluid_select.render(id, default_enviroment_data, params, callBack);
					//console.log('render');
				}
				
			} else {
				if (params.offset==0 & params.task=='load') can_load_default = true;
				




					$.post('http://fluid-line.ru/assets/snippets/product/calcflow3/select_for_environment/getter.php', 
						{ task: params.task, params: params },
					function(id, params, callBack, data){
						//console.log(data);


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


					callBack(data[itm], opx);
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
	
		let sreda = {id: 222, name: "cs"};
		$(`#${fluid_select_doners[myIdthis]}`).change();
		if (callBack!=false) callBack(value, opx);

		
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
