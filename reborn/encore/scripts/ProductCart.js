import {ShoppingCart} from './ShoppingCart';
const $ = require('jquery');

class ProductCart {
    constructor(cart, shop) {
        // Cart modal
        this.modalCart = cart;

        this.productTitleTag = '_products-pagetitle';
        this.productPriceTag = '_products-price';

        this.productCount = this.modalCart.find('input[name=_products-count]');

        this.form = this.modalCart.find('#pushToCart');

        this.shoppingCart = shop;

        $(this.form).submit(function (e) {
            e.preventDefault();
            shop.pushToCart(new FormData($(this)[0]));
        });
    }

    setCount (count) {
        this.productCount.attr('value', count);
    }

    formSubmit() {
        this.form.submit();
    }

    clearCart() {
        this.shoppingCart.executeClear();
    }

    hide () {
        this.modalCart.removeClass('_show');
    }

    setProduct(product) {
        let productInfo = JSON.parse(decodeURIComponent(atob(product)));

        if (productInfo === "" || productInfo === "undefined") {
            return;
        }

        this.setCode(productInfo['code']);
        this.setPrice(productInfo['price']);
        this.setMinCount(1);
        this.setParameters(productInfo['parameters']);
    }

    setParameters = function (parameters) {
        let parametersGroup = this.modalCart.find('._attachments-group');
        parametersGroup.empty();

        for (let i = 0; i < parameters.length; i++) {
            this.appendParameters(
                parametersGroup, i, parameters[i]['name'], parameters[i]['value'], parameters[i]['description']
            );
        }
    }

    appendParameters = function (group, position, name, value, description) {
        let data = "";

        if (description !== undefined && description !== null) {
            data = ' | ' + description;
        }

        group.append(
            '<div class="_body-group">' +
                '<label>' + name + ':</label>' +
                '<p>' + value + ' ' + data + '</p>' +
            '</div>'
        );
    }

    setCode = function (title) {
        let titleTag = this.productTitleTag;
        $('#' + titleTag)[0].innerHTML = title;
        $('input[name='+titleTag + ']').attr('value', title);
    }

    setPrice = function (price) {
        let priceTag = this.productPriceTag;
        $('#' + priceTag)[0].innerHTML = price['currency'] + price['value'];
    }

    setMinCount = function (count) {
        this.productCount.attr('value', count).attr('min', count);

        let productCountLabel = this.modalCart.find('._products-count-label');
        productCountLabel.empty().append('мин. заказ от ' + count + ' шт.')
    }

    updateCountInShop(btn) {
        this.shoppingCart.updateCount(btn)
    }

    removeItemInShop(btn) {
        this.shoppingCart.removeItem(btn)
    }

    updateCommentInShop(textarea) {
        this.shoppingCart.updateComment(textarea);
    }
}

let cart = new ProductCart($('#productCart'), new ShoppingCart('#shoppingCart'));

$('body').on('click', '._shop-clear', function () {
    cart.clearCart();
});

$('body').on('click', '._item-count-change', function () {
    cart.updateCountInShop($(this));
});

$('body').on('change', '._comment-textarea', function () {
    cart.updateCommentInShop($(this));
});

$('body').on('click', '._comment-delete', function () {
    let textarea = $('#' + $(this).attr('for'));
    $(textarea).val('');
    cart.updateCommentInShop($(textarea));
})

$('body').on('click', '._item-delete', function () {
    cart.removeItemInShop($(this));
});

$('body').on('click', '._cart-label', function () {
    cart.setProduct($(this).attr('data-fl-product'));
    cart.setCount($($(this).attr('for')).val());
    cart.formSubmit();
}).parent('._attach-cart');

$('body').on('click', '._btn', function () {
    if ($(this).attr('data-fl-product') !== undefined) {
        cart.setProduct($(this).attr('data-fl-product'));
    }
});

$(window).scroll(function () {
    cart.hide();
})
