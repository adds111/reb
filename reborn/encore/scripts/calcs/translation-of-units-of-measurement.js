const $ = require('jquery');

function Trunc(f) {
    return Math.round(f - 0.5);
}

function Tochn(f, n) {
    p = Math.pow(10, n);
    return Math.round(f * p) / p;
}

let length = $('#OkLength');

if (length[0] !== undefined) {
    class Length {
        constructor(lengthBlock) {
            let text_mm = $(lengthBlock).find('#txtBoxMm');
            let text_in = $(lengthBlock).find('#txtBoxIn');
            let text_ft = $(lengthBlock).find('#txtBoxFt');

            $(text_mm).on('keyup', function () {
                text_in.val(Tochn(0.03937 * txtBoxMm.value.replace(',', '.'), 8));
                text_ft.val(Tochn(0.0033 * txtBoxMm.value.replace(',', '.'), 8));
            });

            $(text_in).on('keyup', function () {
                text_mm.val(Tochn(25.4 * txtBoxIn.value.replace(',', '.'), 8));
                text_ft.val(Tochn(0.083 * txtBoxIn.value.replace(',', '.'), 8));
            });

            $(text_ft).on('keyup', function () {
                text_mm.val(Tochn(304.799 * txtBoxFt.value.replace(',', '.'), 8));
                text_in.val(Tochn(12 * txtBoxFt.value.replace(',', '.'), 8));
            });
        }
    }
    new Length(length);
}

let volume = $('#OkVolume');

if (volume[0] !== undefined) {
    class Volume {
        constructor(volumeBlock) {
            let text_m3 = $(volumeBlock).find('#txtBoxM3');
            let text_l = $(volumeBlock).find('#txtBoxL');
            let text_f3 = $(volumeBlock).find('#txtBoxF3');
            let text_g = $(volumeBlock).find('#txtBoxG');

            $(text_m3).on('keyup', function () {
                text_g.val(Tochn(264.2007926 * txtBoxM3.value.replace(',', '.'), 8));
                text_f3.val(Tochn(35.3146001 * txtBoxM3.value.replace(',', '.'), 8));
                text_l.val(Tochn(1000 * txtBoxM3.value.replace(',', '.'), 8));
            });

            $(text_l).on('keyup', function () {
                text_m3.val(Tochn(0.001 * txtBoxL.value.replace(',', '.'), 8));
                text_g.val(Tochn(0.2642008 * txtBoxL.value.replace(',', '.'), 8));
                text_f3.val(Tochn(0.0353146 * txtBoxL.value.replace(',', '.'), 8));
            });

            $(text_f3).on('keyup', function () {
                text_m3.val(Tochn(0.0283169 * txtBoxF3.value.replace(',', '.'), 8));
                text_l.val(Tochn(28.3169 * txtBoxF3.value.replace(',', '.'), 8));
                text_g.val(Tochn(7.4813474 * txtBoxF3.value.replace(',', '.'), 8));
            });

            $(text_g).on('keyup', function () {
                text_m3.val(Tochn(0.003785 * txtBoxG.value.replace(',', '.'), 8));
                text_l.val(Tochn(3.785 * txtBoxG.value.replace(',', '.'), 8));
                text_f3.val(Tochn(0.1336658 * txtBoxG.value.replace(',', '.'), 8));
            });
        }
    }

    new Volume(volume);
}

let mass = $('#OkMass');

if (mass[0] !== undefined) {
    class Mass {
        constructor(massBlock) {
            let text_kg = $(massBlock).find('#txtBoxKg');
            let text_lbs = $(massBlock).find('#txtBoxLbs');

            $(text_kg).on('keyup', function () {
                text_lbs.val(Tochn(2.2046 * txtBoxKg.value.replace(',', '.'), 8));
            });

            $(text_lbs).on('keyup', function () {
                text_kg.val(Tochn(0.4536 * txtBoxLbs.value.replace(',', '.'), 8));
            });
        }
    }

    new Mass(mass);
}

let temp = $('#OkTemp');

if (temp[0] !== undefined) {
    class Temp {
        constructor(tempBlock) {
            let text_c = $(tempBlock).find('#txtBoxC');
            let text_k = $(tempBlock).find('#txtBoxK');
            let text_frg = $(tempBlock).find('#txtBoxFrg');

            $(text_c).on('keyup', function () {
                text_k.val(Tochn(273.4 + parseFloat(text_c.val().replace(',', '.')), 8));
                text_frg.val(Tochn(32.0 + 1.8 * text_c.val().replace(',', '.'), 8));
            });

            $(text_k).on('keyup', function () {
                text_c.val(Tochn(-273.4 + parseFloat(txtBoxK.value.replace(',', '.')), 8));
                text_frg.val(Tochn(32.0 + 1.8 * txtBoxC.value.replace(',', '.'), 8));
            });

            $(text_frg).on('keyup', function () {
                text_c.val(Tochn(0.5556 * (parseFloat(txtBoxFrg.value.replace(',', '.')) - 32.0), 8));
                text_k.val(Tochn(273.4 + parseFloat(txtBoxC.value.replace(',', '.')), 8));
            });
        }
    }

    new Temp(temp);
}

let pres = $('#OkPres');

if (pres[0] !== undefined) {
    class Pres {
        constructor(presBlock) {
            let text_pa = $(presBlock).find('#txtBoxPa');
            let text_b = $(presBlock).find('#txtBoxB');
            let text_psi = $(presBlock).find('#txtBoxPsi');
            let text_kgs = $(presBlock).find('#txtBoxKgs');

            $(text_pa).on('keyup', function () {
                text_b.val(Tochn((0.00001) * txtBoxPa.value.replace(',', '.'), 8).toFixed(2));
                text_psi.val(Tochn((0.0001450) * txtBoxPa.value.replace(',', '.'), 8).toFixed(2));
                text_kgs.val(Tochn(0.0000101972 * txtBoxPa.value.replace(',', '.'), 8).toFixed(2));
            });

            $(text_b).on('keyup', function () {
                text_pa.val(Tochn((100000) * txtBoxB.value.replace(',', '.'), 8).toFixed(2));
                text_psi.val(Tochn((14.5038) * txtBoxB.value.replace(',', '.'), 8).toFixed(2));
                text_kgs.val((txtBoxB.value.replace(',', '.') * (250 / 245.17)).toFixed(2));
            });

            $(text_psi).on('keyup', function () {
                text_pa.val(Tochn((6894.74483) * txtBoxPsi.value.replace(',', '.'), 8).toFixed(2));
                text_b.val(Tochn((0.0689474) * txtBoxPsi.value.replace(',', '.'), 8).toFixed(2));
                text_kgs.val(Tochn(0.070307 * txtBoxPsi.value.replace(',', '.'), 8).toFixed(2));
            });

            $(text_kgs).on('keyup', function () {
                text_pa.val(Tochn(98066.52 * txtBoxKgs.value.replace(',', '.'), 8).toFixed(2));
                text_b.val((txtBoxKgs.value.replace(',', '.') * (245.17 / 250)).toFixed(2));
                text_psi.val(Tochn(14.2233 * txtBoxKgs.value.replace(',', '.'), 8).toFixed(2));
            });
        }
    }

    new Pres(pres);
}

let consump = $('#OkConsump');

if (consump[0] !== undefined) {
    class Consump {
        constructor(consumpBlock) {
            let text_m3h = $(consumpBlock).find('#txtBoxM3H');
            let text_lm = $(consumpBlock).find('#txtBoxLM');
            let text_f3m = $(consumpBlock).find('#txtBoxF3M');
            let text_gm = $(consumpBlock).find('#txtBoxGM');

            $(text_m3h).on('keyup', function () {
                text_lm.val(Tochn((16.6667) * txtBoxM3H.value.replace(',', '.'), 8));
                text_f3m.val(Tochn((0.58857667) * txtBoxM3H.value.replace(',', '.'), 8));
                text_gm.val(Tochn((44.0334654) * txtBoxM3H.value.replace(',', '.'), 8) / 10);
            });

            $(text_lm).on('keyup', function () {
                text_m3h.val(Tochn((1 / 16.6667) * txtBoxLM.value.replace(',', '.'), 8));
                text_f3m.val(Tochn((0.58857667) * txtBoxM3H.value.replace(',', '.'), 8));
                text_gm.val(Tochn((44.0334654) * txtBoxM3H.value.replace(',', '.'), 8) / 10);
            });

            $(text_f3m).on('keyup', function () {
                text_m3h.val(Tochn((1 / 0.58857667) * txtBoxF3M.value.replace(',', '.'), 8));
                text_lm.val(Tochn((16.6667) * txtBoxM3H.value.replace(',', '.'), 8));
                text_gm.val(Tochn((44.0334654) * txtBoxM3H.value.replace(',', '.'), 8) / 10);
            });

            $(text_gm).on('keyup', function () {
                text_m3h.val(Tochn((1 / 44.0334654) * txtBoxGM.value.replace(',', '.'), 8) * 10);
                text_lm.val(Tochn((16.6667) * txtBoxM3H.value.replace(',', '.'), 8));
                text_f3m.val(Tochn((0.58857667) * txtBoxM3H.value.replace(',', '.'), 8));
            });
        }
    }

    new Consump(consump);
}

function cv() {
    dynblock.innerHTML = '<H1 class=title align=center>Расчёт требуемого коэффициента пропускной способности Cv и расхода</H1>' +

        'Каждый клапан и регулятор, произведённый CIRCOR, характеризуется <B>коэффициентом пропускной способности Cv</B>, ' +
        'который указывается в каталогах. Коэффициент пропускной способности введён для облегчения работы проектировщиков ' +
        'пневматических и гидравлических систем. С его помощью можно без труда найти расход через клапан или подобрать ' +
        'подходящий клапан, который этот расход обеспечит. <B>Коллеги, настоятельно обращаем Ваше внимание, что приведённые ' +
        'ниже формулы носят рекомендательный характер. Полученные результаты должны рассматриваться только как ориентировочные.</B>' +

        '<BR><BR><a href="#F1" onclick=ChemX("cv1.php",500,400)>Автоматический расчет расхода Q для газовой среды</a>' +
        '<BR><a href="#F1" onclick=ChemX("cv2.php",500,400)>Автоматический расчет пропускной способности Cv для газовой среды</a>' +
        '<BR><a href="#F1" onclick=ChemX("cv3.php",500,400)>Автоматический расчет расхода Q для жидкой среды</a>' +
        '<BR><a href="#F1" onclick=ChemX("cv4.php",500,400)>Автоматический расчет пропускной способности Cv для жидкой среды</a>' +

        '<BR><BR>Расчёт в американской системе единиц можно провести на сайте' +
        ' <a href="http://www.goreg.com/technical/calculations/calc-cv_gas.html" target="blank_">GO Regulator</a> .' +

        '<BR><BR><B>Расчетные формулы</B>' +
        '<BR><BR>1. Применительно к газовой среде' +
        '<BR>1.1. Расчёт расхода' +
        '<BR>Дано:' +
        '<BR>- давление на входе P1 [бар]' +
        '<BR>- давление на выходе P2 [бар]' +
        '<BR>- относительная плотность газа Sг (относительно воздуха)' +
        '<BR>- коэффициентом пропускной способности Cv' +
        '<BR>Если P2+1>0.5*(P1+1) тогда <img align=center src="images/formula/F1.jpg">[литр/мин]' +
        '<BR>Если P2+1<0.5*(P1+1) тогда <img align=center src="images/formula/F2.jpg">[литр/мин]' +
        '<BR>1.2. Расчёт требуемого минимального коэффициента Cv' +
        '<BR>Дано:' +
        '<BR>- давление на входе P1 [бар] ' +
        '<BR>- давление на выходе P2 [бар]' +
        '<BR>- расход Q [норм. литр/мин]' +
        '<BR>- относительная плотность газа Sг (относительно воздуха)' +
        '<BR>Если P2+1>0.5*(P1+1) тогда <img align=center src="images/formula/F3.jpg">' +
        '<BR>Если P2+1<0.5*(P1+1) тогда <img align=center src="images/formula/F4.jpg">' +
        '<BR><BR>2. Применительно к жидкой среде' +
        '<BR>2.1. Расчёт расхода' +
        '<BR>Дано:' +
        '<BR>- давление на входе P1 [бар] ' +
        '<BR>- давление на выходе P2 [бар]' +
        '<BR>- относительная плотность жидкости Sж (относительно воды)' +
        '<BR>- коэффициентом пропускной способности Cv' +
        '<BR><img align=center src="images/formula/F5.jpg">[литр/мин]' +
        '<BR>1.2. Расчёт требуемого минимального коэффициента Cv' +
        '<BR>Дано:' +
        '<BR>- давление на входе P1 [бар] ' +
        '<BR>- давление на выходе P2 [бар]' +
        '<BR>- расход Q [норм. литр/мин]' +
        '<BR>- относительная плотность жидкости Sж (относительно воды)' +
        '<BR><img align=center src="images/formula/F6.jpg">' +
        '<BR><BR>Будьте внимательны с переводом единиц измерения. Это можно сделать в ' +
        ' <a href="teh.php">соответствующем разделе</a> нашего сайта.' +
        '<BR>Если у Вас возникли вопросы, пожалуйста, обращайтесь к сотрудникам компании.' +
        '<BR><BR><H4 class=footer align=center>&nbsp;</H4>'
}

function corr() {
    dynblock.innerHTML = '<H1 class=title align=center>Таблица коррозионной стойкости</H1>' +
        '<br>' +
        '<img src="images/pdf.gif" border="0" alt="">&nbsp;&nbsp;&nbsp;<a href="#f1" onclick=Chem("pdf/Hoke/HokeCorrosionGuide.pdf")>Таблица коррозионных совместимостей материалов и сред&nbsp;(0.08 МБ) </a>' +

        '<BR><BR><H4 class=footer align=center>&nbsp;</H4>'
}

function LD() {
    var t = location.search.substring(1).split("&");

    if (t == '1') {
        trans();
        return
    }
    if (t == '2') {
        cv();
        return
    }
    trans()
}

function Chem(name) {
    var newWindow
    newWindow = open(name, "", "toolbar=no,menubar=no,scrollbars=yes,width=700px,height=480px, resizable=yes")
}

function ChemX(name, LX, LY) {
    var newWindow;
    newWindow = open(name, "", "toolbar=no,menubar=no,scrollbars=yes,width=" + LX + ",height=" + LY + ", resizable=yes");
}