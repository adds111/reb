"use strict";

function Object_assign(a, b){
  $.extend(a, b); 
  return a;
}


function _instanceof(left, right) { if (right != null && typeof Symbol !== "undefined" && right[Symbol.hasInstance]) { return !!right[Symbol.hasInstance](left); } else { return left instanceof right; } }

function _classCallCheck(instance, Constructor) { if (!_instanceof(instance, Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

var ifj23awle912 = 0;
var units = {
  'speed': {
    m3h: "м3/час",
    lm: "л/мин",
    lh: "л/час",
    ftm: "фут3/мин"
  },
  '_speed': {
    nm3h: "нм3/час",
    nlm: "нл/мин",
    nlh: "нл/час",
    nftm: "нфут3/мин"
  },
  'pressure': {
    br: "бар",
    psi: "PSI",
    pa: "Па",
    kpa: "кПа",
    mpa: "МПа"
  },
  'temp': {
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

var fluid_input = /*#__PURE__*/function () {
  function fluid_input(select_id) {
    var _this = this;

    var param = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
    var callBack = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : false;
    var dop_func = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : false;

    _classCallCheck(this, fluid_input);

    this.myId = "-fluid_input-".concat(++ifj23awle912);
    this.select_id = select_id;
    var doner = $("#".concat(select_id)); //console.log(doner[0]);
    //console.log(param);

    var title = param.title,
        classNames = param.classNames,
        styles = param.styles,
        inline = param.inline;

    if (title == undefined) {
      title = doner[0].getAttribute('title');
    } //if (auto_translate==undefined){
    //	auto_translate = false;
    //}
    //console.log(`inline: ${inline}`);


    var value = doner[0].getAttribute('value');
    if (value == undefined) value = 0;
    var empty = doner[0].getAttribute('empty');
    if (empty == undefined) empty = 0;
    var measure = doner[0].getAttribute('measure');
    var name = doner[0].getAttribute('name');
    var unit = doner[0].getAttribute('unit');
    var ident = doner[0].getAttribute('ident');
    if (ident == undefined) ident = '';
    var type = doner[0].getAttribute('type');
    var readonly = doner[0].getAttribute('readonly');
    if (readonly != undefined) readonly = true;
    var error = doner[0].getAttribute('error');
    var description = doner[0].getAttribute('description');
    var unit2 = "ru";

    if (unit != undefined && measure != undefined) {
      //console.log(unit);
      unit2 = units[measure][unit];
    }

    var imputs_measure = "";

    if (measure != undefined) {
      imputs_measure = "<input type=\"hidden\" class=\"measure_input\" name=\"".concat(name, ".measure\" value=\"").concat(measure.substr(0, 1) == '_' ? measure.substr(1) : measure, "\">\n\t\t\t\t\t\t\t  <input type=\"hidden\" class=\"unit_input\"    name=\"").concat(name, ".unit\" value=\"").concat(unit != undefined ? measure.substr(0, 1) == '_' ? unit.substr(1) : unit : '', "\">\n\t\t\t\t\t\t\t  <input type=\"hidden\"                       name=\"").concat(name, ".empty\" value=\"").concat(empty, "\">");
    }

    doner[0].insertAdjacentHTML('afterEnd', "<label class=\"fluid-input ".concat(classNames != undefined ? classNames : '', " \"\n\t\t\t").concat(styles != undefined ? "style=\"".concat(styles, " ").concat(type == "hidden" ? 'display: none !important;' : "", "\"") : '', " id=\"").concat(this.myId, "\">\n\t\t\t\t<div class=\"fluid-input-title\"><div class=\"layout\">").concat(title, "</div></div>\n\t\t\t\t<div class=\"input_border ").concat(error == 1 | empty == 1 ? 'input_error' : '', "\" ").concat(readonly ? 'style="background: #eee;"' : "", ">\n\t\t\t\t\t<div class=\"rel\">\n\t\t\t\t\t\t<div class=\"text input_fnt\">").concat(value, "</div>") + (empty == 1 ? "<input class=\"fluid-input_number input_fnt\" ".concat(readonly ? "readonly" : "", " type=\"text\" name=\"").concat(name, ".value\" value=\"\">") : "<input step=\"0.001\" class=\"fluid-input_number input_fnt\" ".concat(readonly ? "readonly" : "", " type=\"").concat(type, "\" name=\"").concat(name, ".value\" value=\"").concat(value, "\">")) + "".concat(imputs_measure, "\n\n\t\t\t\t\t\t<div class=\"shower ").concat(measure == undefined ? 'hide_from_see' : '', "\">").concat(description != undefined ? description : '', "</div>\n\t\t\t\t\t</div>\n\t\t\t\t\t<div class=\"measure ").concat(measure == undefined ? 'hide_from_see' : '', "\">\n\t\t\t\t\t\t<div class=\"selected\">").concat(unit2, "</div>\n\t\t\t\t\t\t<div class=\"select_block\"></div>\n\t\t\t\t\t</div>\n\t\t\t\t</div>\n\t\t\t</label>"));
    var input = $("#".concat(this.myId)).find('input');
    var text = $("#".concat(this.myId)).find('.text'); //let loadConvert = () => {
    //	if (auto_translate){
    //		console.log("Перевод!");
    //	}
    //}

    input.on('keyup', function () {
      fluid_input.keyup(input[0].value, text[0]);

      if (typeof dop_func == "function") {
        var cx = dop_func.bind(0, input[0]);
        console.log(cx);
        cx();
        console.log(input[0]);
      }
    });
    input.on('change', function () {
      if (typeof callBack == "function") callBack(input.val(), input[0], text.innerHTML, _this.unit); //loadConvert();
    });

    if (measure != undefined) {
      (function () {
        var new_measure = measure.substr(0, 1) == '_' ? measure.substr(1) : measure; //console.log('>> '+measure);

        var select_block = $("#".concat(_this.myId)).find('.select_block')[0];
        var unit_input = $("#".concat(_this.myId)).find('.unit_input')[0];
        var text = $("#".concat(_this.myId)).find('.selected')[0];

        var _loop = function _loop() {
          var i = _Object$keys[_i];
          var new_i = measure == new_measure ? i : i.substr(1); //console.log(units[measure][i]);
          //console.log(i);

          var option = document.createElement('div');
          option.classList.add('measure_option');
          option.innerHTML = units[measure][i];
          option.addEventListener("click", function () {
            _this.unit = new_i;
            fluid_input.measure_option_click(unit_input, text, [new_i, units[measure][i]]);
            if (typeof callBack == "function") callBack(input.val(), input[0], text.innerHTML, _this.unit); //loadConvert();
          }, false);
          select_block.appendChild(option);
        };

        for (var _i = 0, _Object$keys = Object.keys(units[measure]); _i < _Object$keys.length; _i++) {
          _loop();
        }
      })();
    }

    doner.remove();
  }

  _createClass(fluid_input, null, [{
    key: "measure_option_click",
    value: function measure_option_click(block, text, value) {
      block.value = value[0];
      text.innerHTML = value[1]; //console.log(`Выбран ${value[1]}`);
      //console.log(`Выбран ${value[0]}`);
    }
  }, {
    key: "keyup",
    value: function keyup(th, txt) {
      txt.innerHTML = th;
    }
  }, {
    key: "createInputFromClass",
    value: function createInputFromClass(className, styles) {
      var callBack = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : false;
      var dop_func = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : false;
      //console.log('---createInputFromClass '+className);
      var g = 0;
      fluid_input.stack = {};
      $(".".concat(className)).each(function () {
        //console.log(this);
        if (this.tagName == 'INPUT') {
          g++;
          var id = this.getAttribute('id');
          var classNames = this.getAttribute('class');
          var style = this.getAttribute('style');

          if (id == null) {
            id = 'hidenSelect' + g;
            this.setAttribute('id', id);
          }

          var valx = $(this).find("option:selected").text();
          fluid_input.stack[id] = new fluid_input(id, Object_assign({
            classNames: classNames,
            styles: style
          }, styles), callBack, dop_func);
        }
      });
    }
    /*	static serializator(a, b = {}){
    		let new_ar = b;
    		for(const i of a){
    
    			let dpm = i.name.split('.');
    			
    
    			let new_ar_2 = {[dpm[1]] : i.value};
    
    			console.log(dpm[0]);
    			console.log(new_ar_2);
    			console.log("-----");
    
    
    			if (!(dpm[0] in Object.keys(new_ar))) {
    				new_ar = Object.assign( new_ar, { [dpm[0]] : new_ar_2 });
    			} else {
    				new_ar[dpm[0]] = Object.assign( new_ar[ [dpm[0]] ], new_ar_2 );
    			}
    
    
    		}
    		return new_ar;
    	}
    
    */

  }, {
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
  }]);

  return fluid_input;
}();