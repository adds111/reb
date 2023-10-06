"use strict";


function Object_assign(a, b){
  $.extend(a, b); 
  return a;
}


function _instanceof(left, right) { if (right != null && typeof Symbol !== "undefined" && right[Symbol.hasInstance]) { return !!right[Symbol.hasInstance](left); } else { return left instanceof right; } }

function _typeof(obj) { "@babel/helpers - typeof"; if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

function _classCallCheck(instance, Constructor) { if (!_instanceof(instance, Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

var send_counters = {};

var fluid_form = /*#__PURE__*/function () {
  function fluid_form(select_id) {
    var param = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
    var functions = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : {};

    _classCallCheck(this, fluid_form);

    this.myId = select_id;
    var doner = $("#".concat(select_id)); //console.log('id: '+select_id);

    console.log('-------------------');
    console.log(param);
    send_counters = Object_assign(_defineProperty({}, select_id, 0));
    var section = param.section,
        style = param.style,
        inputs = param.inputs,
        settings = param.settings;
    var callback = functions.callback,
        on_submit = functions.on_submit,
        on_create = functions.on_create;
    console.log('[[[param]]]');
    console.log(param);
    if (_typeof(settings) != 'object') settings = {};
    var detal = "";

    if ("code" in Object.keys(inputs)) {
      detal = "Изделие: " + inputs.code.value;
    }

    var sreda_id = style.sreda_id;
    if (sreda_id == undefined) sreda_id = 1;
    doner.html("<div class=\"dataform\">\n\t\t\t\t\t\t".concat('fluid_form_mode' in style ? "<input type='hidden' value='".concat(style.fluid_form_mode, "' name='calc_mode'>") : '', "\n\t\t\t\t\t\t<span class=\"updatered\"></span>\n\t\t\t\t\t\t<hr>\n\t\t\t\t\t\t<span class=\"not_update\">\n\t\t\t\t\t\t").concat(detal, "\n\t\t\t\t\t\t<label class=\"envirement_label\"  ").concat(style.input != undefined ? "style='" + style.input + "'" : "", ">\n\t\t\t\t\t\t\t<div style=\"font-size: 14px; \" class=\"fluid-input-title\">\u0421\u0440\u0435\u0434\u0430 <span class=\"sreda_title\" style=\"margin-left: 5px; color: #f44;\"></span></div>\n\t\t\t\t\t\t\t<select statenotnull=\"1\" select=\"").concat(sreda_id, "\" id=\"").concat(select_id, "_sreda\" class=\"sred_list\" style=\"width:100%; max-width: 180px; background: #fff;\">\n\t\t\t\t\t\t\t\t<option>\u0412\u044B\u0431\u0435\u0440\u0438\u0442\u0435 \u0441\u0440\u0435\u0434\u0443</option>\n\t\t\t\t\t\t\t</select>\n\t\t\t\t\t\t</label>\n\t\t\t\t\t\t</span>\n\t\t\t\t\t\t<span class=\"compat\"></span>\n\t\t\t\t\t</div>\n\t\t\t\t\t<div class=\"ret\"></div>"));
    var updatered = doner.find('.updatered');
    fluid_form.render(select_id, section, style, settings, inputs, updatered, function () {
      return fluid_form.send(select_id, doner, style, settings, callback);
    });
    doner.on('submit', function () {
      fluid_form.send(select_id, doner, style, settings, callback);
      return false;
    }); //doner.find('input, select').on('change', function(){
    //fluid_form.send(select_id, doner, style);
    //});

    fluid_input.createInputFromClass('customInputs', {}, function () {
      fluid_form.send(select_id, doner, style, settings);
    }, fluid_form.send.bind(0, select_id, doner, style, settings, callback, true));
    new fluid_select("".concat(select_id, "_sreda"), {
      styles: 'width: 100%'
    }, function (id, data, th) {
      fluid_form.send(select_id, doner, style, settings, callback);
    }.bind(0, select_id));
    if (typeof on_create == 'function') on_create(this.myId);
  }

  _createClass(fluid_form, null, [{
    key: "serializator",
    value: function serializator(a) {
      var b = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
      var new_ar = b;
      var _iteratorNormalCompletion = true;
      var _didIteratorError = false;
      var _iteratorError = undefined;

      try {
        for (var _iterator = a[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true) {
          var i = _step.value;
          new_ar[i.name] = i.value;
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

      return new_ar;
    }
  }, {
    key: "send",
    value: function send(id, th, style, settings, callback) {
      var not_update = arguments.length > 5 && arguments[5] !== undefined ? arguments[5] : false;
      var input = arguments.length > 6 && arguments[6] !== undefined ? arguments[6] : false;
      console.log('===========================' + _typeof(callback));
      console.log(arguments); //console.log('id'); console.log(id);
      //console.log('th'); console.log(th);
      //console.log('style'); console.log(style);
      //console.log('not_update'); console.log(not_update);
      //console.log('input'); console.log(input);
      //console.log('===========================');

      var data = fluid_form.serializator(th.serializeArray());
      console.log('Отправляем');
      console.log(data);
      $.post('/assets/snippets/product/calcflow3/calc/c.php', data, function (id, th, style, settings, callback, not_update, data) {
        var write_in_url = settings.write_in_url,
            hide_class_if_empty = settings.hide_class_if_empty; //console.log(`write_in_url = ${write_in_url}`);

        if (hide_class_if_empty != undefined) {//$(th).parents('.'+hide_class_if_empty).css('display', 'none');
        }

        console.log('Принимаем');
        console.log(data); //if (send_counters[id]!=0){

        if (typeof callback == 'function') callback(data, id); //}

        send_counters[id]++; //return false;
        //console.log(`not_update (${not_update})`);

        var inputs = data.inputs;
        var reters = data.returned;
        var section = data.section;

        if (!not_update) {
          var updatered = $("#".concat(id)).find('.updatered');
          updatered.html('');
          fluid_form.render(id, section, style, settings, inputs, updatered, function () {
            return fluid_form.send(id, th, style, settings, callback);
          });
          fluid_input.createInputFromClass('customInputs', {}, function () {
            fluid_form.send(id, th, style, settings, callback);
          }, fluid_form.send.bind(0, id, th, style, settings, callback, true));
        } else {
          if (input != false) {
            var nm = input.getAttribute('name').split('.')[0]; //console.log(nm);

            var vals = data.inputs[nm].value;
            var gtitle = data.inputs[nm].info;

            var _ops = gtitle != undefined ? gtitle + "<hr>" : '';

            for (var itmm in vals) {
              //console.log(itmm);
              _ops += data.inputs[nm].ident + ' = ' + vals[itmm] + ' ' + units[data.inputs[nm].measure][itmm] + '<br>';
            }

            var border = $(input).parents(".input_border");
            if (data.inputs[nm].error == 1) border.addClass('input_error');else border.removeClass('input_error');
            border.find('.shower').html(_ops);

            if (data.calc_mode == "korr") {
              //console.log(data.calc_mode);
              //$consumption2.value
              var consumption2 = $("#main").find('[name="consumption2.value"]');
              var gblock = consumption2.parents('.fluid-input');
              gblock.find('.layout').html(data.inputs.consumption2.title);
              var _consumption2 = data.inputs.consumption2;

              if (_consumption2.error == 0) {
                gblock.find('.input_border').removeClass('input_error');
              } else {
                gblock.find('.input_border').addClass('input_error');
              }

              consumption2.val(_consumption2.value[_consumption2.unit]);
              consumption2.find('.input_fnt').html(_consumption2.value[_consumption2.unit]);
            }
          }
        } /// set url


        if (write_in_url != false) {
          var url = "";

          for (var _i = 0, _Object$keys = Object.keys(data.inputs); _i < _Object$keys.length; _i++) {
            var name = _Object$keys[_i];
            var itm = data.inputs[name];
            url += "&".concat(name, "=").concat(_typeof(itm.value) == 'object' ? (itm.error != '1' ? itm.value[itm.unit] : '_') + "&".concat(name, "_unit=").concat(itm.measure.substr(0, 1) == "_" ? itm.unit.substr(1) : itm.unit) : itm.value);
          }

          url = location.origin + location.pathname + '?' + url.substr(1);
          history.pushState(null, null, url);
        }

        var ops = "";

        if (reters.errors.value != '') {
          ops += '<div class="errors_panel">' + reters.errors.value + "</div>";
        }

        ops += "<div class='p10'>";
        if (data.calc_mode != "korr") ops += "<div style='color: #45b645; font-weight: 800;'>Результат</div>";

        var _loop = function _loop() {
          var name = _Object$keys2[_i2];

          if (name != "errors") {
            ops += "<h4>".concat(reters[name].title, "</h4>");

            if (_typeof(reters[name].value) == 'object') {
              Object.keys(reters[name].value).map(function (key, index) {
                if (name == "cv") {
                  ops += "<div class=\"dtc\"><span class=\"edz\">".concat(units[reters[name].measure][key], "</span> = ").concat(reters[name].value[key], "</div>");
                } else {
                  ops += "<div class=\"dtc\">".concat(reters[name].value[key], " <span class=\"edz\">").concat(reters[name].title == "Расход газа" ? 'н' : '').concat(units[reters[name].measure][key], "</span></div>");
                }
              });
            } else {
              ops += "<div>".concat(reters[name].value, "</div>");
            }
          }
        };

        for (var _i2 = 0, _Object$keys2 = Object.keys(reters); _i2 < _Object$keys2.length; _i2++) {
          _loop();
        }

        ops += "</div>";
        $("#".concat(id)).find('.ret').html(ops);
      }.bind(0, id, th, style, settings, callback, not_update));
    }
  }, {
    key: "update",
    value: function update() {}
  }, {
    key: "render",
    value: function render(id, section, style, settings, inputs, updatered) {
      var callback = arguments.length > 6 && arguments[6] !== undefined ? arguments[6] : false;
      //console.log(`render ${id}`);
      var mode = '';

      if ("fluid_form_mode" in style) {
        mode = style.fluid_form_mode;
      } //console.log('render');
      //console.log(style);
      //console.log(section);


      if (section != undefined && section.matr != undefined && section.matr.length != 0) {
        var table = "<table class=\"section_table\" width=\"100%\" cellspacing=\"0\">\n\t\t\t\t<tr style=\"background: #ccc;\">\n\t\t\t\t\t<td>\u2116</td>\n\t\t\t\t\t<td>\u041C\u0430\u0442\u0435\u0440\u0438\u0430\u043B</td>\n\t\t\t\t\t<td>\u0414\u0435\u0442\u0430\u043B\u044C</td>\n\t\t\t\t\t<td>\u0421\u043E\u0432\u043C\u0435\u0441\u0442\u0438\u043C\u043E\u0441\u0442\u044C</td>\n\t\t\t\t</tr>\n\t\t\t";

        for (var _i3 = 0, _Object$keys3 = Object.keys(section.matr); _i3 < _Object$keys3.length; _i3++) {
          var name = _Object$keys3[_i3];

          //console.log(section[name]);
          if (section.matr[name].ops != null) {
            var itm = section.matr[name].ops.split("|"); //console.log(itm);

            table += "<tr ".concat(section.matr[name].tempOut == 1 ? "style='background: #ff6868;'" : "", ">\n\t\t\t\t\t\t\t\t<td>").concat(itm[2], "</td>\n\t\t\t\t\t\t\t\t<td><div class=\"vsp_father\">").concat(section.matr[name].mat, "\n\t\t\t\t\t\t\t\t\t<div class=\"vsp\">\n\t\t\t\t\t\t\t\t\t\t\u041E\u0441\u043D\u043E\u0432\u043D\u044B\u0435 \u043D\u0430\u0437\u0432\u0430\u043D\u0438\u044F: <b>").concat(section.matr[name].russian, "</b><br>\n\t\t\t\t\t\t\t\t\t\t\u0414\u0440\u0443\u0433\u0438\u0435 \u043D\u0430\u0437\u0432\u0430\u043D\u0438\u044F: <b>").concat(section.matr[name].another, "</b><br>\n\t\t\t\t\t\t\t\t\t\t\u0420\u0435\u043A\u043E\u043C\u0435\u043D\u0434\u0443\u0435\u043C\u0430\u044F \u0442\u0435\u043C\u043F\u0435\u0440\u0430\u0442\u0443\u0440\u0430: <b>").concat(section.matr[name].temp, "</b>\n\t\t\t\t\t\t\t\t\t\t<div style=\"text-align: center\">\n\t\t\t\t\t\t\t\t\t\t\t<a target=\"_blank\" href=\"http://fluid-line.ru/koroziya?sel=").concat(inputs['environment']['value'], "&sel1=").concat(section.matr[name].id, "\">\u041F\u043E\u0434\u0440\u043E\u0431\u043D\u0435\u0435...</a>   <!-- Link #\u0441\u0441\u043B\u043A\u0430 \u043D\u0430 seetru -->\n\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t</div></td>\n\t\t\t\t\t\t\t\t<td>").concat(itm[0]).concat(itm[1] == 1 ? "*" : "", "</td>\n\t\t\t\t\t\t\t\t<td><a class=\"linkbtn sovm_").concat(section.matr[name].sov, " ").concat(section.matr[name].sov_q == 1 ? 'ques' : '', "\" target=\"_blank\" href=\"http://fluid-line.ru/koroziya?sel=").concat(inputs['environment']['value'], "&sel1=").concat(section.matr[name].id, "\">\n\t\t\t\t\t\t\t\t\t").concat(section.matr[name].sov == 4 ? "рекомендуется" : section.matr[name].sov == 3 ? "удовлетворительно" : section.matr[name].sov == 2 ? "не подойдет" : section.matr[name].sov == 1 ? "не рекомендуется" : "неизвестно", "</a></td>\n\t\t\t\t\t\t\t</tr>");
          }
        }

        table += "</table>";
        var bnv = " <div ".concat(style.input != undefined ? "style='" + style.input + "'" : "", ">\n\t\t\t\t\t\t<label ").concat(style.input != undefined ? "style='" + style.input + "'" : "", ">\n\t\t\t\t\t\t\t<div style=\"font-size: 14px; \" class=\"fluid-input-title\">\u0421\u043E\u0432\u043C\u0435\u0441\u0442\u0438\u043C\u043E\u0441\u0442\u044C \u0441\u043E \u0441\u0440\u0435\u0434\u043E\u0439</div>\n\t\t\t\t\t\t\t<div class=\"statusbar ").concat(section.allq == 1 ? 'ques' : '', "\">\n\t\t\t\t\t\t\t\t<div class=\"status_title \">\n\n\t\t\t\t\t\t\t\t\t<span style=\"padding: 4px;\" class=\" text_sovm_").concat(section.all, " \">\n\t\t\t\t\t\t\t\t\t\t").concat(section.all == 4 ? "рекомендуется" : section.all == 3 ? "удовлетворительно" : section.all == 2 ? "не подойдет" : section.all == 1 ? "не рекомендуется" : "неизвестно", "\n\t\t\t\t\t\t\t\t\t</span>\n\n\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t<div class=\"status_block\">\n\t\t\t\t\t\t\t\t\t<div style=\"padding: 10px\">\n\t\t\t\t\t\t\t\t\t\t<b>\u0421\u043E\u0432\u043C\u0435\u0441\u0442\u0438\u043C\u043E\u0441\u0442\u044C \u0434\u0435\u0442\u0430\u043B\u0435\u0439: </b><br>\n\t\t\t\t\t\t\t\t\t\t\u0412\u0441\u0435 \u0434\u0435\u0442\u0430\u043B\u0438: <span class=\"text_sov text_sovm_").concat(section.all, " ").concat(section.allq == 1 ? 'ques' : '', "\">\n\t\t\t\t\t\t\t\t\t\t\t").concat(section.all == 4 ? "рекомендуется" : section.all == 3 ? "удовлетворительно" : section.all == 2 ? "не подойдет" : section.all == 1 ? "не рекомендуется" : "неизвестно", "\n\t\t\t\t\t\t\t\t\t\t</span><br>\n\t\t\t\t\t\t\t\t\t\t*\u043A\u043E\u043D\u0442\u0430\u043A\u0442\u0438\u0440\u0443\u044E\u0449\u0438\u0435 \u0441\u043E \u0441\u0440\u0435\u0434\u043E\u0439: <span class=\"text_sov text_sovm_").concat(section.contact, " ").concat(section.contactq == 1 ? 'ques' : '', "\">\n\t\t\t\t\t\t\t\t\t\t\t").concat(section.contact == 4 ? "рекомендуется" : section.contact == 3 ? "удовлетворительно" : section.contact == 2 ? "не подойдет" : section.contact == 1 ? "не рекомендуется" : "неизвестно", "\n\t\t\t\t\t\t\t\t\t\t</span>\n\t\t\t\t\t\t\t\t\t\t<br>\n\t\t\t\t\t\t\t\t\t\t <b>C\u043F\u0438\u0441\u043E\u043A \u0434\u0435\u043B\u0430\u0442\u0435\u0439:</b>\n\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t").concat(table, "\n\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t</label>\n\t\t\t\t\t\t</div>");
        $("#".concat(id)).find('.compat').html(bnv); //$(compat[0]).innerHTML = bnv;
      } //console.log('/////////////////////////');
      //console.log(inputs);
      //console.log('/////////////////////////');


      for (var _i4 = 0, _Object$keys4 = Object.keys(inputs); _i4 < _Object$keys4.length; _i4++) {
        var _name = _Object$keys4[_i4];

        if (inputs[_name]['type'] == 'radio') {
          //console.log('radio');
          //console.log(inputs[name]);
          var block = document.createElement('div');
          block.className = "radioBlock";

          if (mode == 'korr') {
            block.style.display = 'none';
          }

          for (var vr in inputs[_name]['var']) {
            var label = document.createElement('label');
            label.innerHTML = "<div class=\"frm_btn\" offset=\"0\">".concat(inputs[_name]['var'][vr], "</div>");
            var imp = document.createElement('input');
            imp.name = _name;
            imp.id = _name + '_' + vr + '_';
            imp.className = "hiden_radio";
            label.setAttribute('for', _name + '_' + vr + '_');
            imp.type = "radio";
            imp.value = vr;

            if (inputs[_name]['value'] == vr) {
              imp.checked = true;
            }

            imp.onchange = callback;
            block.appendChild(imp);
            block.appendChild(label);
          }

          updatered[0].appendChild(block);
        } else {
          var _imp = document.createElement('input');

          _imp.name = _name;
          _imp.type = "number";
          _imp.className = "customInputs";
          if (style.input != undefined) _imp.style = style.input;

          for (var _i5 = 0, _Object$keys5 = Object.keys(inputs[_name]); _i5 < _Object$keys5.length; _i5++) {
            var parm = _Object$keys5[_i5];

            /////////////////  "ДУБЛЬ"
            if (parm == 'value' && _typeof(inputs[_name][parm]) == 'object') {
              _imp.setAttribute(parm, inputs[_name][parm][inputs[_name]["unit"]]);

              var ops = "";
              var ident = inputs[_name]['ident'];
              var info = inputs[_name]['info'];
              if (info == undefined) info = "";else info += "<hr>";
              var show_pzx = true;

              if (ident == "key") {
                show_pzx = false;
              }

              ops += info;

              for (var _i6 = 0, _Object$keys6 = Object.keys(inputs[_name][parm]); _i6 < _Object$keys6.length; _i6++) {
                var i = _Object$keys6[_i6];
                ops += "".concat(show_pzx ? ident : units[inputs[_name]["measure"]][i], " = ").concat(inputs[_name][parm][i], " ").concat(show_pzx ? units[inputs[_name]["measure"]][i] : "", "<br>");
              }

              _imp.setAttribute("description", ops);
            } else {
              _imp.setAttribute(parm, inputs[_name][parm]);
            }
          }

          updatered[0].appendChild(_imp);
        }
      }
    }
  }]);

  return fluid_form;
}();