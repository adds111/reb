const $ = require('jquery');

export class ShoppingCart {
    constructor(shoppingCart) {
        this.shoppingCart = shoppingCart;
        this.cartHolder = localStorage.getItem('ShoppingCart');

        if (this.cartHolder === "") {
            this.cartHolder = null;
        }

        this.fillCart();
        this.fillTotalCart()
    }

    getCookie = function (name) {
        let matches = document.cookie.match(new RegExp(
            "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
        ));
        return matches ? decodeURIComponent(matches[1]) : undefined;
    }
    
    convertToUsd = function (price, itemCurrency) {
        let usd = this.getCookie('CUR_USD');
        let current = this.getCookie(itemCurrency);

        let result = parseFloat(price) * parseFloat(current);
        return result / usd;
    }

    convertToRub = function (price) {
        return parseFloat(price) * parseFloat(this.getCookie('CUR_USD'));
    }

    totalInfo = function () {
        let count = 0;
        let price = 0;
        let index = 0;

        if (this.cartHolder !== null) {
            let items = JSON.parse(this.cartHolder);

            items.forEach((item) => {
                let itemPrice = 0;

                if (item['currency'] === "CUR_USD") {
                    itemPrice = parseInt(item['count']) * parseFloat(item['price']);

                } else {
                    itemPrice = parseInt(item['count']) * this.convertToUsd(item['price'], item['currency']);
                }

                price = price + itemPrice;
                count = count + parseInt(item['count']);

                $('._item-price-' + index).empty().append(itemPrice.toFixed(2));

                index++;
            });
        }

        $('._total-count-value').empty().append(count);
        $('._total-result-value').empty().append(price.toFixed(2));
        $('._total-result-converted').empty().append(this.convertToRub(price).toFixed(2));
    }

    removeItem(btn) {
        let btnIndex = $(btn).attr('data-fl-cart-item');

        let index = 0;
        let items = JSON.parse(this.cartHolder);

        items.forEach((item) => {
            if (index === parseInt(btnIndex)) {
                items.splice(index, 1);
            }

            index++;
        });

        this.cartHolder = JSON.stringify(items);
        localStorage.setItem('ShoppingCart', this.cartHolder);

        this.totalInfo();
        this.fillCart();
        this.fillTotalCart(true);
    }

    updateCount(btn) {
        let btnIndex = $(btn).attr('data-fl-cart-item');
        let btnOption = $(btn).attr('data-fl-cart-option');

        let index = 0;
        let items = JSON.parse(this.cartHolder);

        items.forEach((item) => {
            if (index === parseInt(btnIndex)) {
                if (btnOption === "down") {
                    items[index]['count'] = parseInt(items[index]['count']) - 1;

                } else if (btnOption === "up") {
                    items[index]['count'] = parseInt(items[index]['count']) + 1;
                }
                this.updateInput(index, items[index]['count']);
            }
            index++;
        });

        this.cartHolder = JSON.stringify(items);
        localStorage.setItem('ShoppingCart', this.cartHolder);
        this.totalInfo();
    }

    updateInput = function (index, count) {
        $('._item-input-' + index).val(count);
    }

    updateComment = function (textarea) {
        let items = JSON.parse(this.cartHolder);
        let index = $(textarea).attr('data-fl-cart-item');

        items.some((item, itemIndex) => {
            if (parseInt(index) === parseInt(itemIndex)) {
                items[index]['comment'] = $(textarea).val();
            }
        });

        this.cartHolder = JSON.stringify(items);
        localStorage.setItem('ShoppingCart', this.cartHolder);
    }

    openCart = function () {
        if (!$(this.shoppingCart).hasClass('_show')) {
            $(this.shoppingCart).addClass('_show');
        }
    }

    fillCart = function (empty = false) {
        let shopItems = $('._shop-items');
        shopItems.empty();

        let index = 0;

        if (this.cartHolder !== null) {
            let items = JSON.parse(this.cartHolder);

            items.forEach((item) => {
                this.addCartItem(index, item)
                index++;
            });
        }

        this.totalInfo();
    }

    fillTotalCart = function (empty = false) {
        let shopItems = $('._shop-items-full');
        let index = 0;

        if (empty === true) {
            shopItems.empty();
        }

        let before = $(shopItems).find('._item.placeholder-glow');

        if (this.cartHolder !== null) {
            let items = JSON.parse(this.cartHolder);

            items.forEach((item) => {
                this.addTotalCartItem(index, item, before)
                index++;
            });
        } else {
            shopItems.empty();
        }

        $(before).remove();

        this.totalInfo();
    }

    executeClear() {
        this.cartHolder = null;
        localStorage.removeItem('ShoppingCart');
        this.fillCart();
        this.fillTotalCart()
        this.totalInfo();
    }

    async pushToCart(form) {
        let response = await fetch('/assets/reborn/api/ShoppingCart/getItem.php', {
            method: 'POST',
            body: form
        });

        let result = await response.json();
        if (result !== undefined && result['code'] !== undefined) {
            this.addShoppingItem(result);
        }
    }

    addShoppingItem = function (item) {
        if (this.cartHolder === null) {
            this.cartHolder = JSON.stringify(new Array(item));
            localStorage.setItem('ShoppingCart', this.cartHolder);
        } else {
            let products = JSON.parse(this.cartHolder);

            if (item['new'] === true) {
                products.push(item);
                this.addCartItem(products.length, item);

            } else {
                let writed = false;

                products.some(function (productItem, productIndex) {
                    if (productItem['code'] === item['code'] && productItem['new'] === false) {
                        products[productIndex]['count'] = parseInt(products[productIndex]['count']) + parseInt(item['count']);

                        writed = true;
                    }

                    if (writed === true) {
                        return true;
                    }
                });

                if (writed === false) {
                    products.push(item);
                    this.addCartItem(products.length, item);
                }
            }

            this.cartHolder = JSON.stringify(products);
            localStorage.setItem('ShoppingCart', this.cartHolder);
        }

        this.fillCart();
        this.fillTotalCart()
    }

    addCartItem = function (index, item) {
        $('._shop-items').append(
            '<div class="_item">' +
            '    <a class="_item-title" href="/'+ item['code'] +'">'+ item['code'] +'</a>' +
            '    <div class="_item-button-group">' +
            '        <a class="_item-count-change" data-fl-cart-option="down" data-fl-cart-item="'+ index +'">' +
            '            <img src="/assets/reborn/images/objects/product/count-down.svg" alt="down">' +
            '        </a>' +
            '        <input class="_item-count-input _item-input-'+ index +'" value="'+ item['count'] +'" readonly>' +
            '        <a class="_item-count-change" data-fl-cart-option="up" data-fl-cart-item="'+ index +'">' +
            '            <img src="/assets/reborn/images/objects/product/count-up.svg" alt="down">' +
            '        </a>' +
            '        <a class="_item-delete" data-fl-cart-item="'+ index +'">' +
            '            <img src="/assets/reborn/images/objects/product/product-delete.svg" alt="X">' +
            '        </a>' +
            '    </div>' +
            '</div>'
        );
    }

    addTotalCartItem = function (index, item, before) {
        let commentActiveClass = "";

        if (item['comment'] !== "") {
            commentActiveClass = '_show';
        }
        let str = '<div class="_item">' +
            '<div class="_item-scroll"></div>' +
            '    <div class="_item-image">' +
            '        <img src="/assets/reborn/images/default/cart-default-preview.png" alt="default">' +
            '    </div>' +
            '    <div class="_item-groups">' +
            '        <div class="_info-group _info-parent">' +
            '            <p>'+ item['parent'] +'</p>' +
            '        </div>' +
            '        <div class="_button-group">' +
            '            <p class="_product-price">$ ' +
            '                <span class="_item-price-'+ index +'">'+ parseInt(item['price']) * parseInt(item['count']) +'</span>' +
            '            </p>' +
            '            <a class="_item-count-change" data-fl-cart-option="down" data-fl-cart-item="'+ index +'">' +
            '                <img src="/assets/reborn/images/objects/product/count-down.svg" alt="down">' +
            '            </a>' +
            '            <input class="_item-count-input _item-input-'+ index +'" value="'+ item['count'] +'" readonly>' +
            '            <a class="_item-count-change" data-fl-cart-option="up" data-fl-cart-item="'+ index +'">' +
            '                <img src="/assets/reborn/images/objects/product/count-up.svg" alt="down">' +
            '            </a>' +
            '            <a class="_item-delete" data-fl-cart-item="' + index + '">' +
            '                <img src="/assets/reborn/images/objects/product/product-delete.svg" alt="X">' +
            '            </a>' +
            '        </div>' +
            '        <div class="_info-group _info-product">' +
            '            <a href="/'+ item['code'] +'">'+ item['code'] +'</a>' +
            '        </div>' +
            '        <div class="_comment-button _btn" data-fl-show="comment'+ index +'">' +
            '            <a>Добавить комментарий</a>' +
            '        </div>' +
            '        <div class="_comment-big-column '+ commentActiveClass +'" id="comment'+ index +'">' +
            '            <textarea class="_comment-textarea" id="commentArea'+ index +'" rows="3" data-fl-cart-item="'+ index +'" placeholder="Комментарии">'+ item['comment'] +'</textarea>' +
            '            <label class="_comment-delete" for="commentArea'+ index +'">' +
            '                <img src="/assets/reborn/images/objects/product/product-delete.svg" alt="X">' +
            '            </label>' +
            '        </div>' +
            '    </div>' +
            '</div>';

        if ($(before)[0] !== undefined) {
            $(str).insertBefore('._item.placeholder-glow').parent('._shop-items-full');
        } else {
            $('._shop-items-full').append(str);
        }
    }
}