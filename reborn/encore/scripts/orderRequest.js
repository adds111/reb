const $ = require('jquery');

let fileInput = $('#fileInput');

if (fileInput[0] !== undefined) {
    fileInput.onchange = function() {
        if (this.files[0]) {
            document.getElementById('fileInputName').innerHTML = this.files[0].name;
        }
    };
}

let orderForm = $('#orderForm');

if (orderForm[0] !== undefined) {
    orderForm.submit(function(e) {
        e.preventDefault();

        let form = document.getElementById('orderForm');
        let cart = document.createElement('input');

        cart.setAttribute('name', 'cart');
        cart.setAttribute('value', btoa(unescape(encodeURIComponent(localStorage.getItem('ShoppingCart')))));
        cart.setAttribute('type', 'hidden');
        form.appendChild(cart);
        localStorage.removeItem('ShoppingCart');
        form.submit();

    }, false);
}