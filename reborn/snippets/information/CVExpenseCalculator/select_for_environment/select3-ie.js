"use strict";

function _instanceof(left, right) { if (right != null && typeof Symbol !== "undefined" && right[Symbol.hasInstance]) { return !!right[Symbol.hasInstance](left); } else { return left instanceof right; } }

function _classCallCheck(instance, Constructor) { if (!_instanceof(instance, Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

var jN9j12dsID = 0;
/*

	statenotnull="1"
	Только известные среды

*/

var default_enviroment_data = {};
var searching = [];
var searching_steps = [];
var default_render_id = [];
var can_load_default = false;
var fluid_select_doners = [];

var fluid_select = /*#__PURE__*/function () {
  function fluid_select(select_id) {
    var param = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
    var callBack = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : false;

    _classCallCheck(this, fluid_select);

    this.myId = "jN9j12dsID-".concat(++jN9j12dsID);
    this.select_id = select_id;
    var doner = $("#".concat(select_id));
    fluid_select_doners[this.myId] = select_id;
    doner.css("display", "none");
    var search = doner[0].getAttribute('search');
    var weight = doner[0].getAttribute('weight');
    if (weight == null) weight = '';
    var state = doner[0].getAttribute('state');
    if (state == null) state = '';
    var statenotnull = doner[0].getAttribute('statenotnull');
    if (statenotnull == "1") statenotnull = 1;else statenotnull = 0;
    var select = doner[0].getAttribute('select');

    if (select == null | select == '') {
      select = '';
    }

    var task = "load";
    var params = {
      task: 'load',
      offset: 0,
      limit: 50,
      select: select,
      statenotnull: statenotnull
    };
    this.lastSearch = "";
    searching[this.myId] = ''; //console.log(search);

    if (search != null & search != '') {
      task = 'search';
      params = {
        text: search,
        task: 'search',
        offset: 0,
        limit: 50,
        select: select,
        statenotnull: statenotnull
      };
    }

    var title = param.title,
        classNames = param.classNames,
        styles = param.styles;
    doner[0].insertAdjacentHTML('afterEnd', "<div class=\"fluid-select ".concat(classNames != undefined ? classNames : '', "\"\n\t\t\t").concat(styles != undefined ? "style=\"".concat(styles, "\"") : '', " tabindex=\"1\" id=\"").concat(this.myId, "\">\n\t\t\t\t<div class=\"fluid-select-title\">").concat(title != undefined ? title : 'выбор', "</div>\n\t\t\t\t\n\t\t\t\t<div class=\"fluid-select-option_group\">\n\t\t\t\t\t<input class=\"fluid-select-search-input\" type=\"text\" id=\"").concat(this.myId, "_search\" value=\"").concat(task == 'search' ? search : '', "\">\n\n\t\t\t\t\t<input name=\"sreda_id\" type=\"hidden\" id=\"").concat(this.myId, "_sreda_id\" value=\"").concat(select, "\">\n\t\t\t\t\t<input name=\"weight\" type=\"hidden\" id=\"").concat(this.myId, "_weight\" value=\"").concat(weight, "\">\n\t\t\t\t\t<input name=\"state\" type=\"hidden\" id=\"").concat(this.myId, "_state\" value=\"").concat(state, "\">\n\n\n\t\t\t\t\t<ol class=\"fluid-select-list\">\n\t\t\t\t\t\t\n\t\t\t\t\t</ol>\n\t\t\t\t\t<div class=\"fluid-select-else\">\n\t\t\t\t\t</div>\n\t\t\t\t</div>\n\t\t\t</div>"));
    var opts = "";
    this.myOpt = 0;
    var myIdthis = this.myId;
    var select_idthis = this.select_id;
    fluid_select.createElem(doner, myIdthis, this.myOpt, myIdthis, select_idthis, params, callBack);
    var element = document.getElementById(this.myId);
    var search_element = document.getElementById(this.myId + '_search');

    element.onfocus = function () {
      fluid_select.focusIt(this);
    };

    search_element.onfocus = function () {
      fluid_select.focusIt(document.getElementById(myIdthis));
    }.bind(0, myIdthis);

    search_element.onkeydown = function () {
      setTimeout(function () {
        return fluid_select.loadFromServer(myIdthis, {
          text: search_element.value.trim(),
          task: 'search',
          offset: 0,
          limit: 50,
          statenotnull: statenotnull
        }, callBack);
      }, 1000);
      if (event.key == 'Enter') return false;
    }.bind(myIdthis, search_element);

    search_element.onchange = function () {
      setTimeout(function () {
        fluid_select.loadFromServer(myIdthis, {
          text: search_element.value.trim(),
          task: 'search',
          offset: 0,
          limit: 50
        }, callBack);
      }, 1000);
    };

    element.onblur = function () {
      fluid_select.blurIt(this);
    };

    search_element.onblur = function () {
      fluid_select.blurIt(document.getElementById(myIdthis));
    }.bind(0, myIdthis);

    var closeButton = document.createElement('button');
    closeButton.innerHTML = 'x';
    closeButton.className = 'xinclose_button';
    closeButton.addEventListener("click", fluid_select.SelectIClose.bind(1, 0, myIdthis, 0, '', 0, true), false);
    $("#".concat(myIdthis, " .fluid-select-option_group"))[0].appendChild(closeButton);
    var elseButton = document.createElement('div');
    elseButton.innerHTML = 'Ещё';
    elseButton.className = 'fluid-select-search_btn';
    elseButton.setAttribute('tabindex', 0);
    elseButton.addEventListener("click", fluid_select.Else.bind(1, myIdthis, callBack), false);
    $("#".concat(myIdthis, " .fluid-select-else"))[0].appendChild(elseButton);
  }

  _createClass(fluid_select, null, [{
    key: "Else",
    value: function Else(id) {
      var callBack = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;
      //console.log(id);
      searching_steps[id].offset += searching_steps[id].limit;
      fluid_select.loadFromServer(id, searching_steps[id], callBack);
      $("#".concat(id)).focus();
    }
  }, {
    key: "createElem",
    value: function createElem(elm, myId, myOpt, myIdthis, select_idthis, params, callBack) {
      var myOPTGROUP = '';
      fluid_select.loadFromServer(myId, params, callBack);
    }
  }, {
    key: "loadFromServer",
    value: function loadFromServer(id, params) {
      var callBack = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : false;

      if (searching[id] != "".concat(params.text, " - ").concat(params.offset)) {
        searching[id] = "".concat(params.text, " - ").concat(params.offset); //console.log(Object.keys(default_enviroment_data).length);
        //console.log(params.offset);
        //console.log(params.task);
        //console.log(params.select);

        /*
        	Если много селектов дефолтных на странице, 
        	чтобы не обращаться постоянно к серверу за данными
        	мы можем один раз обратиться к серверу за дефолтным списком и хранить его 
        	в переменной default_enviroment_data
        */

        if (can_load_default & params.offset == 0 & params.task == 'load' & params.select == '' | can_load_default & params.offset == 0 & params.task == 'search' & params.text == '' & params.select == '') {
          if (Object.keys(default_enviroment_data).length == 0) {
            default_render_id.push({
              id: id,
              params: params,
              callBack: callBack
            }); //console.log('add in stack');

            console.log(default_render_id);
          } else {
            fluid_select.render(id, default_enviroment_data, params, callBack); //console.log('render');
          }
        } else {
          if (params.offset == 0 & params.task == 'load') can_load_default = true;
          $.post('http://fluid-line.ru/assets/snippets/product/calcflow3/select_for_environment/getter.php', {
            task: params.task,
            params: params
          }, function (id, params, callBack, data) {
            //console.log(data);
            fluid_select.render(id, data, params, callBack);

            if (Object.keys(default_enviroment_data).length == 0) {
              if (params.offset == 0 & params.task == 'load') {
                default_enviroment_data = data;

                if (default_render_id.length != 0) {
                  console.log('Загрузили дефолт, распределяем по default_render_id');
                  var _iteratorNormalCompletion = true;
                  var _didIteratorError = false;
                  var _iteratorError = undefined;

                  try {
                    for (var _iterator = default_render_id[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true) {
                      var itm = _step.value;
                      fluid_select.render(itm.id, data, itm.params, itm.callBack);
                    }
                  } catch (err) {
                    _didIteratorError = true;
                    _iteratorError = err;
                  } finally {
                    try {
                      if (!_iteratorNormalCompletion && _iterator.return != null) {
                        _iterator.return();
                      }
                    } finally {
                      if (_didIteratorError) {
                        throw _iteratorError;
                      }
                    }
                  }

                  default_render_id = [];
                }
              }
            }
          }.bind(0, id, params, callBack)); //console.log('loadFromServer');
        }
      }
    }
  }, {
    key: "render",
    value: function render(id, data, params) {
      var callBack = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : false;
      console.log(arguments);
      searching_steps[id] = params;

      if ($.isEmptyObject(data)) {
        $("#".concat(id, " .fluid-select-else")).addClass('fluid-select_empty_hide');
        if (params.offset == 0) $("#".concat(id, " .fluid-select-list")).html('<div class="fluid-select_empty">Запрос не найден</div>');
      } else {
        var toDrop = $("#".concat(id, " .fluid-select-list"))[0];
        if (params.offset == 0) toDrop.innerHTML = "";
        var count = 0;

        for (var itm in data) {
          var opx = document.createElement('li');
          opx.addEventListener("click", fluid_select.SelectIClose.bind(1, itm, id, id, data[itm]['environment'], data[itm], false, callBack, opx), false);

          if (params.task == 'search' && params.text != '') {
            var regEx = new RegExp(params.text, "ig");
            opx.innerHTML = data[itm]['environment'].replace(regEx, '<span class="seltext">' + params.text + '</span>') + ' ' + (data[itm]['weight'] > 50 ? '[жидкость]' : '[газ]');
          } else {
            opx.innerHTML = data[itm]['environment'] + ' ' + (data[itm]['weight'] > 50 ? '[жидкость]' : '[газ]');
          }

          var classkdf = "OPTIONX ";

          if (params.select != '' & params.select == data[itm]['id']) {
            classkdf += '__selected__';
            $("#".concat(id, " .fluid-select-title")).html(data[itm]['environment']);
            $("#".concat(id, "_weight")).val(data[itm]['weight']);
            $("#".concat(id, "_state")).val(data[itm]['state']);
            $("#".concat(id, "_sreda_id")).val(data[itm]['id']); ////////////

            console.log("--".concat(id, "---")); //$(`#${id}`).parent().find(`.sreda_title`).html(value['weight']>50?' [Жидкость]':' [Газ]');
            //$(`#${id}`).parents('envirement_label').find(`.sreda_title`).html(value['weight']>50?' [Жидкость]':' [Газ]');
            ////////////

            callBack(data[itm], opx);
          }

          opx.className = classkdf; //console.log(params.select);
          //console.log(data[itm]['id']);
          //if (this.parentElement.tagName == 'OPTGROUP'){
          //	opx.className+=' ingrp';
          //}

          toDrop.appendChild(opx);
          count++;
        }

        if (count < params.limit) {
          $("#".concat(id, " .fluid-select-else")).addClass('fluid-select_empty_hide');
        } else {
          $("#".concat(id, " .fluid-select-else")).removeClass('fluid-select_empty_hide');
        }
      }
    }
  }, {
    key: "focusIt",
    value: function focusIt(myIdthis) {
      myIdthis.classList.add('fluid_select_focus');
    }
  }, {
    key: "blurIt",
    value: function blurIt(myIdthis) {
      myIdthis.classList.remove('fluid_select_focus');
    }
  }, {
    key: "CloseFilter",
    value: function CloseFilter(myIdthis) {
      $("#".concat(myIdthis)).blur();
    }
  }, {
    key: "SelectIClose",
    value: function SelectIClose(myOpt, myIdthis, select_idthis, iHTML) {
      var value = arguments.length > 4 && arguments[4] !== undefined ? arguments[4] : null;
      var toClose = arguments.length > 5 && arguments[5] !== undefined ? arguments[5] : false;
      var callBack = arguments.length > 6 && arguments[6] !== undefined ? arguments[6] : false;
      var opx = arguments.length > 7 ? arguments[7] : undefined;
      $("#".concat(myIdthis)).find('.__selected__').removeClass('__selected__');
      opx.classList.add('__selected__');

      if (toClose) {
        $("#".concat(myIdthis)).blur();
        console.log('close');
        return false;
      }

      $("#".concat(myIdthis, "_weight")).val(value.weight);
      $("#".concat(myIdthis, "_state")).val(value.state);
      $("#".concat(myIdthis, "_sreda_id")).val(value.id);
      var xs = $("#".concat(myIdthis, " .fluid-select-title"));
      xs.html(iHTML);
      xs.attr('title', iHTML);
      $("#".concat(myIdthis)).blur();
      $("#".concat(select_idthis))[0].selectedIndex = myOpt - 1;
      var sreda = {
        id: 222,
        name: "cs"
      };
      $("#".concat(fluid_select_doners[myIdthis])).change();
      if (callBack != false) callBack(value, opx);
      $("#".concat(myIdthis)).parent().find(".sreda_title").html(value['weight'] > 50 ? ' [Жидкость]' : ' [Газ]');
    }
  }, {
    key: "createSelectFromClass",
    value: function createSelectFromClass(className) {
      var callBack = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;
      var g = 0;
      fluid_select.stack = {};
      $(".".concat(className)).each(function () {
        if (this.tagName == 'SELECT') {
          g++;
          var id = this.getAttribute('id');
          var classNames = this.getAttribute('class');
          var style = this.getAttribute('style');

          if (id == null) {
            id = 'hidenSelect' + g;
            this.setAttribute('id', id);
          }

          var valx = $(this).find("option:selected").text();
          fluid_select.stack[id] = new fluid_select(id, {
            title: valx,
            classNames: classNames,
            styles: style
          }, callBack);
        }
      });
    }
  }]);

  return fluid_select;
}();