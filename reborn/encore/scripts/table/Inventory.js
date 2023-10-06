const $ = require('jquery');

let inventory = $('._table-container');

if (inventory[0] !== undefined) {
    $('body').on('click', '._item-title', function () {
        let activeHolders = $('._item-holder._active');

        if (activeHolders[0] !== undefined) {
            activeHolders.removeClass('_active');
        }

        let holder = $(this).parent('._filter-item').find('._item-holder');

        if (!holder.hasClass('_active')) {
            holder.addClass('_active');
        }
    });

    $('body').on('click', '._item-holder._active ._item-title', function () {
        $(this).parent().removeClass('_active');
    });

    class InventoryTable {
        constructor(table) {
            this.table = table;

            this.listFixed = [
                {
                    class : '_col-image',
                    value : '<a><img src="/assets/reborn/images/default/table-default-preview.png"></a>',
                    title : ''
                },
                {
                    class : '_col-coding',
                    value : '<a>H1B-F-4N-PK</a>',
                    title : 'Кодировка'
                },
                {
                    class: '_col-model',
                    value: '<button class="btn"><img src="/assets/reborn/images/inventory/model.svg"></button>',
                    title : 'Модель'
                },
                {
                    class : '_col-cart',
                    value: '<button class="btn"><img src="/assets/reborn/images/inventory/cart.svg"></button>',
                    title : 'Корзина'
                },
                {
                    class: '_col-price',
                    value: '$101.2',
                    title : 'Цена'
                },
            ];

            this.prevImage = {'src' : '', 'count' : 0, 'class' : ''};

            this.order = [];

            this.getSerialInventory(this, this.table.getAttribute('id'));
        }

        setOrder = function (value) {
            if (value === "empty") {
                let inputs = $(this.table).find('._item').find('input');

                for (let i = 0; i < inputs.length; i++) {
                    if ($(inputs[i]).is(':checked')) {
                        $(inputs[i]).prop('checked', false);
                    }
                }
            } else {
                let param = value.split(':', 2);

                if (this.order.length === 0) {
                    this.order = [{key : param[0], value : param[1]}];
                } else {
                    this.order.push({key : param[0], value : param[1]})
                }
            }

            this.getOrderedRequest();
        }

        removeOrder = function (value) {
            let param = value.split(':', 2);

            let index = 0;

            this.order.forEach((item) => {
                if (item['key'] === param[0] && item['value'] === param[1]) {
                    this.order.splice(index, 1);
                }

                index++;
            });

            if (this.order.length === 0) {
                this.order = [{}];
            }

            this.getOrderedRequest();
        }

        getOrderedRequest = function () {
            let serial = $(this.table).attr('id');
            let table = this;

            $.ajax({
                url: 'https://inventory.fluid-line.ru/get/ordered/' + serial,
                method: 'POST',
                enctype: 'application/json',
                dataType: 'json',
                data: {
                    order : this.order,
                    limit : 100
                },
                statusCode: {
                    200: function (response) {
                        table.setTable(table, response['products'], response['filter']);
                    },
                }
            });
        }

        getSerialInventory = function (table, serial) {
            $.ajax({
                url: 'https://inventory.fluid-line.ru/get/' + serial,
                method: 'POST',
                enctype: 'application/json',
                dataType: 'json',
                data: {
                    limit : 100
                },
                statusCode: {
                    200: function (response) {
                        $(table.table).find('._plug').remove();

                        table.generateFilter(table);
                        table.generateTable(table);

                        table.setFilters(table, response['filter']);
                        table.setTable(table, response['products'], response['filter']);

                        $('._items').on('scroll', function () {
                            if ($(this).scrollTop() > 0) {
                                $(this).addClass('_scrolled');
                            } else if ($(this).scrollTop() === 0) {
                                $(this).removeClass('_scrolled');
                            }
                        });

                        $('._item-search').on('input', function () {
                            $('._item-not-found').removeClass('_active');

                            let hidden = $('._item._hidden');

                            if (hidden[0] !== undefined) {
                                hidden.removeClass('_hidden');
                            }

                            let itemsObject = $(this).parent().parent().find('._items ._item');

                            let childs = itemsObject.children();

                            let count = 0;
                            for (let i = 0; i < childs.length; i++) {
                                let input = $(childs[i]).find('input');

                                let value = input.val();

                                if (value !== undefined) {
                                    let thisValue = $(this).val().replace('/', '.');
                                    let regex = new RegExp(thisValue, 'g');

                                    if (value.match(regex) === null) {
                                        $(input).parent().parent().addClass('_hidden');
                                    }
                                }
                            }

                            if (count === 0) {
                                $(this).parent().find('._items ._item-not-found').addClass('_active');
                            }
                        });
                    },
                }
            });
        }

        setTable = function (table, products, filter) {
            let tableFixed = $(this.table).find('._inventory-table-fixed');
            let tbodyFixed = $(tableFixed).find('tbody');

            let tableScroll = $(this.table).find('._inventory-table-scrollable');
            let theadScroll = $(tableScroll).find('thead');
            let tbodyScroll = $(tableScroll).find('tbody');

            if (filter !== undefined) {
                table.setTableParametersHeader(theadScroll, filter);
            }

            tbodyFixed.empty();
            tbodyScroll.empty();

            for (let i = 0; i < products.length; i++) {
                table.setTableRow(tbodyFixed, products[i]);
                table.setTableParameters(tbodyScroll, products[i]['parameters']);
            }
        }

        setTableRow = function (tbody, item) {
            let tr = document.createElement('tr');
            let generatedClass = '_class' + Math.floor(Math.random() * 9999);

            for (let i = 0; i < this.listFixed.length; i++) {
                let td = document.createElement('td');
                td.className = this.listFixed[i]['class'];
                td.innerHTML = '';

                if (this.listFixed[i]['class'] === '_col-image') {
                    td.style.verticalAlign = "baseline";
                    td.style.padding = "1em 0";

                    let img = document.createElement('img');
                    img.src = item['attachments']['image'];
                    img.style.width = '100%';

                    if (this.prevImage.src === "" || this.prevImage.src !== item['attachments']['image']) {
                        this.prevImage.src = item['attachments']['image'];
                        this.prevImage.count = 1;
                        this.prevImage.class = generatedClass;
                        img.className = generatedClass;

                        td.appendChild(img);
                        $('.' + this.prevImage.class).parent().attr('rowspan', this.prevImage.count);

                    } else {
                        this.prevImage.count++;
                        $('.' + this.prevImage.class).parent().attr('rowspan', this.prevImage.count);
                        continue;
                    }





                } else if (this.listFixed[i]['class'] === '_col-coding') {
                    let a = document.createElement('a');
                    a.href = item['serial'] + '/' + item['code'];
                    a.innerHTML = item['code'];
                    td.appendChild(a);

                } else if (this.listFixed[i]['class'] === '_col-model') {
                    let img = document.createElement('img');
                    img.src = '/assets/reborn/images/inventory/model.svg';

                    let button = document.createElement('button');
                    button.className = "btn _btn";
                    button.appendChild(img);

                    td.className = td.className + " _col-" + item['code'];

                    this.makeModel(item, td, button);

                } else if (this.listFixed[i]['class'] === '_col-cart') {
                    let img = document.createElement('img');
                    img.src = "/assets/reborn/images/inventory/cart.svg";

                    let button = document.createElement('button');
                    button.setAttribute('data-fl-show', 'productCart');
                    button.setAttribute(
                        'data-fl-product', btoa((encodeURIComponent(JSON.stringify(item))))
                    );
                    button.className = "btn _btn";
                    button.appendChild(img);

                    td.appendChild(button);

                } else if (this.listFixed[i]['class'] === '_col-price') {
                    td.innerHTML = item['price']['currency'] + item['price']['value'];
                }

                tr.appendChild(td);
            }
            tbody.append(tr);
        }

        setTableParametersHeader = function (thead, filterItems) {
            let filterKeys = Object.keys(filterItems);
            let tr = document.createElement('tr');

            for (let i = 0; i < filterKeys.length; i++) {
                let th = document.createElement('th');

                th.innerHTML = filterKeys[i];

                tr.appendChild(th);
            }

            thead.empty().append(tr);
        }

        setTableParameters = function (tbody, itemParameters) {
            let tr = document.createElement('tr');
            for (let i = 0; i < itemParameters.length; i++) {
                let td = document.createElement('td');

                td.innerHTML = itemParameters[i]['value'];

                tr.appendChild(td);
            }

            tbody.append(tr);
        }

        setFilters = function (table, filter) {
            let filterKeys = Object.keys(filter);

            for (let i = 0; i < filterKeys.length; i++) {
                let contains = Object.values(filter[filterKeys[i]]);
                let values = contains[0];
                let descriptions = contains[1];

                $(this.table).find('._inventory-filter').append(
                    table.setFilterItem(filterKeys[i], table.setCheckboxItem(filterKeys[i], values, descriptions))
                );
            }
            $(table.table).find('._item').find('input').on('click', function () {
                let parameterValue = $(this).attr('value');
                if ($(this).is(':checked')) {
                    table.setOrder(parameterValue);
                } else {
                    table.removeOrder(parameterValue);
                }
            })
        }

        setFilterItem = function (filterKey, items) {
            return '' +
            '<div class="_filter-item">' +
                '<h5 class="_item-title">' + filterKey +
                    '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="8" viewBox="0 0 16 8" fill="none">' +
                        '<path d="M1 1L7.95105 7L15 1" stroke="#3D4857" stroke-width="1.5"/>' +
                    '</svg>' +
                '</h5>' +
                '<div class="_item-holder">' +
                    '<p class="_item-title">' + filterKey +
                        '<svg xmlns="http://www.w3.org/2000/svg" width="15" height="9" viewBox="0 0 15 9" fill="none">' +
                            '<path d="M14 8L7.54545 2L1 8" stroke="#3D4857" stroke-width="1.5"/>' +
                        '</svg>' +
                    '</p>' +
                    items +
                '</div>' +
            '</div>'
        }

        setCheckboxItem = function (filterKey, values, descriptions) {
            let items = '';
            let data = '';

            for (let n = 0; n < values.length; n++) {
                if (descriptions !== undefined) {
                    if (descriptions[n] !== "") {
                        data = ' | ' + descriptions[n];
                    }
                }

                if (values[n] !== "") {
                    items +=
                        '<div class="_item">' +
                        '    <div class="_checkbox-group">' +
                        '        <input type="checkbox" value="' + filterKey + ':' + values[n] + '" id="' + filterKey + n + '">' +
                        '        <label for="' + filterKey + n + '" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-title="Tooltip on right">\n' +
                        values[n] + data + '</label>' +
                        '    </div>' +
                        '</div>'
                }
            }

            items +=
                '<div class="_item-not-found">' +
                    '<p>Параметров не найдено :(</p>'
                '</div>'

            if (values.length > 5) {
                return '' +
                    '<div class="_search-block">' +
                        '<input class="form-control _item-search" placeholder="Поиск...">' +
                    '</div>' +
                    '<div class="_items">' + items + '</div>';
            }

            return items;
        }

        generateFilter(table) {
            let button = document.createElement('button');
            button.className = 'btn _btn _inventory-erase';
            button.innerHTML = 'Сбросить фильтр';

            let eraseFilter = document.createElement('div');
            eraseFilter.className = '_inventory-filter-eraser';
            eraseFilter.appendChild(button);

            let inventoryFilter = document.createElement('div');
            inventoryFilter.className = '_inventory-filter';

            let filterContainer = document.createElement('div');
            filterContainer.className = "_inventory-filter-container";

            filterContainer.appendChild(inventoryFilter);
            filterContainer.appendChild(eraseFilter);

            table.table.prepend(filterContainer);

            $('._btn._inventory-erase').on('click', function () {
                table.setOrder('empty');
            })
        }

        generateTable(table) {
            let tables = document.createElement('table');
            tables.className = '_inventory-tables';
            tables.appendChild(this.generateTableFixed());
            tables.appendChild(this.generateTableScroll());

            table.table.append(tables);

            let isDown = false;
            let startX;
            let startY;
            let scrollLeft;
            let scrollTop;


            $('._inventory-overflow').on('mousedown', function (e) {
                isDown = true;

                startX = e.pageX - $(this)[0].offsetLeft;
                startY = e.pageY - $(this)[0].offsetTop;
                scrollLeft = $(this)[0].scrollLeft;
                scrollTop = $(this)[0].scrollTop;
                $(this).css('cursor', 'grabbing');
            });

            $('._inventory-overflow').on('mouseleave', function (e) {
                isDown = false;
                $(this).css('cursor', 'grab');
            });

            $('._inventory-overflow').on('mouseup', function (e) {
                isDown = false;
                $(this).css('cursor', 'grab');
            });

            $('._inventory-overflow').on('mousemove', function (e) {
                if (!isDown) return;

                e.preventDefault();

                const x = e.pageX - $(this)[0].offsetLeft;
                const y = e.pageY - $(this)[0].offsetTop;
                const walkX = (x - startX) * 1; // Change this number to adjust the scroll speed
                const walkY = (y - startY) * 1; // Change this number to adjust the scroll speed

                $(this)[0].scrollLeft = scrollLeft - walkX;
                $(this)[0].scrollTop = scrollTop - walkY;
            });
        }

        generateTableFixed = function () {
            let overflow = document.createElement('div');
            overflow.className = '_inventory-overflow';

            let table = document.createElement('table');
            table.className = '_inventory-table-fixed placeholder-glow';

            let thead = document.createElement('thead');
            let tr = document.createElement('tr');

            for (let i = 0; i < this.listFixed.length; i++) {
                let th = document.createElement('th');

                th.scope = 'col';
                th.innerHTML = this.listFixed[i]['title'];
                th.className = this.listFixed[i]['class'];

                tr.appendChild(th);
            }

            thead.appendChild(tr);

            let tbody = document.createElement('tbody');
            tr = document.createElement('tr');
            tr.className = 'placeholder';

            for (let i = 0; i < this.listFixed.length; i++) {
                let td = document.createElement('td');

                td.innerHTML = this.listFixed[i]['value'];
                td.className = this.listFixed[i]['class'];

                tr.appendChild(td);
            }

            tbody.appendChild(tr);

            table.appendChild(thead);
            table.appendChild(tbody);
            overflow.appendChild(table);

            return overflow;
        }

        generateTableScroll = function () {
            let overflow = document.createElement('div');
            overflow.className = '_inventory-overflow';

            let table = document.createElement('table');
            table.className = '_inventory-table-scrollable placeholder-glow';

            let thead = document.createElement('thead');
            let tr = document.createElement('tr');
            let th = document.createElement('th');
            tr.appendChild(th);
            thead.appendChild(tr);

            let tbody = document.createElement('tbody');
            tr = document.createElement('tr');
            tr.className = 'placeholder';
            let td = document.createElement('td');
            tr.appendChild(td);
            tbody.appendChild(tr);

            table.appendChild(thead);
            table.appendChild(tbody);
            overflow.appendChild(table);

            return overflow;
        }

        makeModel = function (product, td, button) {
            $.ajax({
                url: '/assets/reborn/api/InventoryTable/getModel.php',
                method: 'POST',
                enctype: 'application/json',
                dataType: 'json',
                data: {'item' : product['code'] },
                statusCode: {
                    200: function (response) {
                        if (response['responseText'] !== "") {
                            button.setAttribute('data-fl-show', 'modelView');
                            button.setAttribute('data-fl-model', response['responseText']);
                            button.className = 'btn _btn';
                            td.appendChild(button);
                        }
                    },
                }
            });
        }
    }

    for (let i = 0; i < inventory.length; i++) {
        new InventoryTable(inventory[i]);
    }
}