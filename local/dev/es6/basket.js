'use strict';
/* global f, App, Ajax, yaCounter12153865, Catalog */

let Basket = (() => {
    let timer = false, timer_notice = false;
    const MAX_QUANTITY = 1000;

    //плавающая ссылка на корзину
    let toggleBasketLineFixed = () => {
        let $line = $("#basket-line");
        let $cities = $('#nav-cities');

        if ($(document).scrollTop() > 300) {
            $line.addClass("fixed");
            $cities.addClass('hidden');
        } else {
            $line.removeClass("fixed");
            $cities.removeClass('hidden');
        }
    };

    //добавить в корзину на посадочной
    let doAction = (sender) => {
        let $sender = $(sender);

        if ($sender.parents("#detail-buy").hasClass("disabled")) {
            console.log('disabled');
            return;
        }

        let quantity; //сколько товара добавить в корзину

        let product_id = $sender.attr("data-product-id");
        let action = $sender.attr("data-basket-action");

        if (action === 'plus_quantity') {
            action = 'plus';
            quantity = $("[data-quantity]").val();
        } else if (action === 'set') {
            quantity = $sender.val();
        } else {
            quantity = 1;
        }

        let data = {
            action,
            product_id,
            quantity
        };

        let onSuccess = function (response) {
            update(action, response);
        };

        //на что вешаем loading
        let $loading;
        if (f.isExist($sender.parents(".cart-row"))) {
            //корзина
            $loading = $sender.parents(".cart-row");
        } else if (f.isExist($sender.parents("#detail-buy"))) {
            //карточка товара
            $loading = $sender.parents("#detail-buy");
        } else if (f.isExist($sender.parents(".catalog-item-buy"))) {
            //каталог витрина
            $loading = $sender.parents(".catalog-item-buy");
        } else {
            $loading = $sender;
        }

        Ajax.send("/ajax/ajax_basket.php", data, $loading, onSuccess);
    };

    let setInputValue = ($input, value) => {
        let maxValue = $input.attr("data-max-value");

        if (value >= maxValue) {
            value = maxValue;
            App.showAlert('Нельзя добавить больше, чем ' + value + ' шт.');
        }

        if (value > MAX_QUANTITY) {
            value = MAX_QUANTITY;
            App.showAlert('Нельзя добавить больше, чем ' + value + ' товаров');
        }

        if (isNaN(value) || value < 1 || value === null || value === undefined || value === '') {
            value = 1;
        }

        $input.val(value);
        $("#buy-oneclick").attr("data-quantity", value);
        Catalog.getDetailNotes(value);
    };

    //клик на кнопки +/- на посадочной странице товара
    let setInput = (sender, action = false) => {
        if (action !== 'plus' && action !== 'minus') {
            return false;
        }

        let $input = $(sender).parent().find("input");
        let value = parseInt($input.val());
        action === 'plus' ? value++ : value--;

        setInputValue($input, value);
    };

    //на посадочной
    let onInputChange = (sender) => {
        let $sender = $(sender);
        let value = parseInt($sender.val());
        setInputValue($sender, value);
    };

    //на посадочной
    let onInputKeyUp = (e, sender) => {
        e.preventDefault();

        let $sender = $(sender);
        let key_code = e.keyCode;
        let value = parseInt($sender.val());

        if (key_code === 38) {//up
            value++;
        }

        if (key_code === 40) {//down
            value--;
        }

        setInputValue($sender, value);
    };

    let applyCoupon = (sender) => {
        let $sender = $(sender);
        let coupon = $sender.attr("data-coupon");
        let data;
        let classNameSelected = "selected";

        App.waitStart();

        if ($sender.hasClass(classNameSelected)) {
            data = {'delete_coupon': coupon};
            $sender.removeClass(classNameSelected);
        } else {
            data = {'coupon': coupon};
            $sender.addClass(classNameSelected);
        }

        let onSuccess = function () {
            location.reload();
        };

        Ajax.html("", data, $sender, onSuccess);
    };

    let update = (action, response) => {
        let $cart = $("#cart");
        let $cart_empty = $("#cart-empty");

        let BASKET = response.basket;
        let RECORD = response.record;

        App.reachGoal(response.goal);

        //обновляем ссылку на корзину в хедере
        updateBasketLine(action, BASKET.QUANTITY, BASKET.PRICES.PRINT.DISCOUNT_PRICE, RECORD.ACTION_GOAL_ADD);

        //обновляем инфу на странице корзины
        if (f.isExist($cart)) {
            let $priceBase = $("#cart-summary-price-base");
            let $priceDiscount = $("#cart-summary-price-discount");
            let PRICES = BASKET.PRICES;

            $priceBase.html(PRICES.PRINT.BASE_PRICE);
            $priceDiscount.html(PRICES.PRINT.DISCOUNT_PRICE);

            parseFloat(PRICES.BASE_PRICE) > 100000 ? $priceBase.addClass("smaller") : $priceBase.removeClass("smaller");
            parseFloat(PRICES.DISCOUNT_PRICE) > 100000 ? $priceDiscount.addClass("smaller") : $priceDiscount.removeClass("smaller");


            if (PRICES.BASE_PRICE === PRICES.DISCOUNT_PRICE) {
                $priceBase.parent().hide();
            } else {
                $priceBase.parent().show();
            }

            if (f.isEmpty(RECORD.QUANTITY)) {
                f.animateRowRemove($cart.find('.cart-row[data-product-id="' + RECORD + '"]'));
            } else {
                let PRODUCT_ID = RECORD.PRODUCT_ID;
                let QUANTITY = RECORD.QUANTITY;
                let TOTAL_BASE_PRICE = RECORD.PRICES.PRINT.TOTAL_BASE_PRICE;
                let TOTAL_DISCOUNT_PRICE = RECORD.PRICES.PRINT.TOTAL_DISCOUNT_PRICE;

                let $totalBasePrice = $cart.find('[data-total-base-price-product-id="' + PRODUCT_ID + '"]');
                let $totalDiscountPrice = $cart.find('[data-total-discount-price-product-id="' + PRODUCT_ID + '"]');

                $cart.find('input[data-product-id="' + PRODUCT_ID + '"]').val(QUANTITY);
                $totalBasePrice.html(TOTAL_BASE_PRICE);
                $totalDiscountPrice.html(TOTAL_DISCOUNT_PRICE);
            }

            if (response.basket.QUANTITY === 0) {
                $cart.addClass("hidden");
                $cart_empty.removeClass("hidden");
            }
        }

        //обновляем инфу на посадочной странице товара
        if (f.isExist($("#detail-buy"))) {
            $("#quantity-input").val(1);

            if (f.isEmpty(RECORD.QUANTITY)) {
                //something wrong..
                $("#quantity-inbasket").addClass("hidden");
                $("#detail-buy").addClass("disabled");
            } else {
                $("#quantity-inbasket").removeClass("hidden").find("b").text(RECORD.QUANTITY);
                $("#delivery-date").removeClass("hidden").text(RECORD.DELIVERY_DATE_PRINT);

                if (RECORD.QUANTITY === RECORD.MAX_QUANTITY) {
                    $("#detail-buy").addClass("disabled");
                } else {
                    $("#detail-buy").removeClass("disabled");
                    $("#quantity-input").attr("data-max-value", (RECORD.MAX_QUANTITY - RECORD.QUANTITY));
                }
            }
        }

        try {
            Catalog.getDetailNotes();
        } catch (e) {
        }
    };

    let updateBasketLine = (action, quantity, price, goal = null) => {
        let $line = $("#basket-line");
        let $notice = $line.find(".js-cart-notice");

        if (action === 'plus') {
            App.reachGoal('add_to_cart');

            if (!!goal) {
                App.reachGoal(goal);
            }

            if (!!timer_notice) {
                clearTimeout(timer_notice);
            }
            $notice.addClass("opened");
            timer_notice = setTimeout(function () {
                $notice.removeClass("opened");
            }, 1500);
        }

        $line.find(".js-line-price").html(price);
        $line.find(".js-line-quantity").html(quantity);
    };

    let clear = (sender) => {
        let $sender = $(sender);
        let action = 'clear';

        let data = {action};

        let onSuccess = function (response) {
            console.log(response);
            location.reload();
        };

        Ajax.send("/ajax/ajax_basket.php", data, $sender, onSuccess);
    };

    let init = () => {
        console.log('init basket');

        $(window).bind("load scroll resize orientationchange", function () {
            toggleBasketLineFixed();
        });

        window.Basket = Basket;
    };

    return {
        init, doAction, setInput, onInputChange, onInputKeyUp, clear, applyCoupon
    };
})();

$(() => Basket.init());