'use strict';
/* global f, App, yaCounter12153865, User, Menu */

import {Form} from './form';

let Order = (() => {
    let changePersonType = (event, sender) => {
        event.preventDefault();
        let $sender = $(sender);

        let PERSON_TYPE = $sender.attr("data-person-type");
        let PERSON_TYPE_OLD = $("#PERSON_TYPE_OLD").val();

        if (PERSON_TYPE === PERSON_TYPE_OLD) {
            return false;
        }

        let data = {
            PERSON_TYPE,
            PERSON_TYPE_OLD
        };

        let onComplete = () => {
            changeLocation();
        };

        refresh(sender, data, {}, null, onComplete);

    };

    let changeBonusType = (event, sender) => {
        event.preventDefault();
        let $sender = $(sender); //радио-кнопка
        let $variants = $sender.parent();

        if ($sender.hasClass("selected")) {
            return;
        }

        let BONUS_TYPE = $sender.attr("data-bonus-type");
        let $hidden_input = $("#" + $sender.attr("data-input-target")); //скрытый input

        let bonuses;

        if (BONUS_TYPE === "1") {
            bonuses = 0;
            $hidden_input.val(bonuses);
        } else {
            bonuses = $("#BONUS_COUNT").val();
            $hidden_input.val(bonuses);
        }

        $variants.find(".order-radio").removeClass("selected");
        $sender.addClass("selected");

        updateOrderPrice(sender, bonuses);
    };

    let onBonusKeyUpTimer;
    let onBonusChangeTimer;

    let onBonusChange = (e, sender) => {
        //e.preventDefault();
        clearTimeout(onBonusChangeTimer);

        onBonusKeyUpTimer = setTimeout(() => {
            let $sender = $(sender);
            let bonuses = parseInt($sender.val());
            setBonusValue($sender, bonuses);
        }, 1000);
    };

    let onBonusKeyUp = (e, sender) => {
        //e.preventDefault();
        clearTimeout(onBonusKeyUpTimer);

        onBonusKeyUpTimer = setTimeout(() => {
            let $sender = $(sender);
            let bonuses = parseInt($sender.val());
            setBonusValue($sender, bonuses);
        }, 1000);
    };

    let setBonusValue = ($input, bonuses) => {
        console.log('setBonusValue');

        let $hidden_input = $("#" + $input.attr("data-input-target")); //скрытый input
        let oldBonuses = parseInt($hidden_input.val());

        if (parseInt(bonuses) === oldBonuses) {
            return;
        }

        let maxValue = $input.attr("max");

        if (bonuses > maxValue) {
            bonuses = maxValue;
            $input.val(bonuses);
            App.showAlert('Нельзя использовать больше, чем ' + bonuses + ' бонусов');
        }

        if (isNaN(bonuses) || bonuses < 1 || bonuses === null || bonuses === undefined || bonuses === '') {
            bonuses = 1;
            $input.val(bonuses);
        }

        $hidden_input.val(bonuses);

        updateOrderPrice($input.get(0), bonuses);
    };

    let updateOrderPrice = (sender, bonuses) => {
        refresh(sender);
    };

    let changeStore = (event, sender) => {
        event.preventDefault();
        let $sender = $(sender);

        let BUYER_STORE = $sender.attr("data-store-id");
        let BUYER_STORE_OLD = $("#BUYER_STORE_OLD").val();

        if (BUYER_STORE === BUYER_STORE_OLD) {
            return false;
        }

        let data = {
            BUYER_STORE,
            BUYER_STORE_OLD
        };

        refresh(sender, data);
    };

    let changeDeliveyMethod = (event, sender) => {
        event.preventDefault();
        let $sender = $(sender);

        let DELIVERY_ID = $sender.attr("data-delivery-id");
        let DELIVERY_SET = "Y";

        let data = {
            DELIVERY_ID,
            DELIVERY_SET
        };

        refresh(sender, data);
    };

    let changeLocation = (event = null, sender = null) => {
        let $sender;

        if (!!event) {
            event.preventDefault();
        }

        if (!!sender) {
        } else {
            let $pickup_block = $(".order-pickup-variants");
            let $pickup_selected = $pickup_block.find(".order-checkbox.selected");
            sender = $pickup_selected.get(0);
        }

        $sender = $(sender);

        let $LOCATION_PROP = $("#LOCATION_PROP");
        let LOCATION_PROP_CHOOSEN = $sender.attr("data-location-id");
        let LOCATION_PROP_CURRENT = $LOCATION_PROP.val();

        if (LOCATION_PROP_CHOOSEN === LOCATION_PROP_CURRENT) {
            //return false;
        }

        let order = {
            [$LOCATION_PROP.attr("name")]: LOCATION_PROP_CHOOSEN,
            RECENT_DELIVERY_VALUE: LOCATION_PROP_CURRENT
        };

        Menu.setCityPromise($sender.data('locationKey'), true)
                .then(response => refresh(sender, order));
    };

    let changePaySystem = (event, sender) => {
        event.preventDefault();
        let $sender = $(sender);

        let PAY_SYSTEM_ID = $sender.attr("data-paysystem-id");
        let PAY_SYSTEM_ID_OLD = $("#PAY_SYSTEM_ID_OLD").val();

        if (PAY_SYSTEM_ID === PAY_SYSTEM_ID_OLD) {
            return false;
        }

        let data = {
            PAY_SYSTEM_ID,
            PAY_SYSTEM_ID_OLD
        };

        $("#prepay-block").remove();
        refresh(sender, data);
    };

    let setCheckbox = (event, sender) => {
        event.preventDefault();
        let $sender = $(sender);
        $sender.toggleClass("selected");
        refresh(sender);
    };

    let setRadio = (event, sender) => {
        event.preventDefault();
        let $sender = $(sender);

        if ($sender.hasClass("selected")) {
            return false;
        }

        $sender.parent().find("button").removeClass("selected");
        $sender.addClass("selected");
        refresh(sender);
    };

    let refresh = (sender, order = {}, data = {}, setCity = null, onComplete = null) => {
        console.log('form refresh');
        App.waitStart();

        let $sender = $(sender);
        let $order = $("#order");

        if (Object.keys(order).length === 0 || order['DELIVERY_ID'] === undefined) {
            order['DELIVERY_ID'] = $("#DELIVERY_ID").val();
        }

        if (Object.keys(order).length === 0 || order['BUYER_STORE'] === undefined) {
            order['BUYER_STORE'] = $("#BUYER_STORE").val();
        }

        if (Object.keys(order).length === 0 || order['PERSON_TYPE'] === undefined) {
            order['PERSON_TYPE'] = $("#PERSON_TYPE").val();
        }

        if (Object.keys(order).length === 0 || order['PERSON_TYPE_OLD'] === undefined) {
            order['PERSON_TYPE_OLD'] = $("#PERSON_TYPE_OLD").val();
        }

        if (Object.keys(order).length === 0 || order['PAY_SYSTEM_ID'] === undefined) {
            order['PAY_SYSTEM_ID'] = $("#PAY_SYSTEM_ID").val();
        }

        let $LOCATION_PROP = $("#LOCATION_PROP");
        let LOCATION_PROP_NAME = $LOCATION_PROP.attr("name");
        let LOCATION_PROP_VALUE = $LOCATION_PROP.val();

        if (Object.keys(order).length === 0 || order[LOCATION_PROP_NAME] === undefined) {
            order[LOCATION_PROP_NAME] = LOCATION_PROP_VALUE;
        }

        //make address
        let $ADDRESS_PROP = $("#ADDRESS_PROP");
        let $CITY_PROP = $("#CITY_PROP");
        let $ZIP_PROP = $("#ZIP_PROP");
        let $STREET_PROP = $("#STREET_PROP");
        let $CORPUS_PROP = $("#CORPUS_PROP");
        let $HOUSE_PROP = $("#HOUSE_PROP");
        let $FLAT_PROP = $("#FLAT_PROP");

        if (!!$ADDRESS_PROP) {
            let addrString = '';

            if (!!$ZIP_PROP && !!$ZIP_PROP.val()) {
                addrString += $ZIP_PROP.val();
                order[$ZIP_PROP.attr("name")] = $ZIP_PROP.val();
            }

            if (!!$CITY_PROP && !!$CITY_PROP.val()) {
                addrString += " " + $CITY_PROP.val();
                order[$CITY_PROP.attr("name")] = $CITY_PROP.val();
            }

            if (!!$STREET_PROP && !!$STREET_PROP.val()) {
                addrString += ", " + $STREET_PROP.val();
            }

            if (!!$HOUSE_PROP && !!$HOUSE_PROP.val()) {
                addrString += ", дом " + $HOUSE_PROP.val();
            }

            if (!!$CORPUS_PROP && !!$CORPUS_PROP.val()) {
                addrString += ", корп. " + $CORPUS_PROP.val();
            }

            if (!!$FLAT_PROP && !!$FLAT_PROP.val()) {
                addrString += ", кв. " + $FLAT_PROP.val();
            }

            addrString = order['BUYER_STORE'] ? '' : addrString;

            $ADDRESS_PROP.val(addrString);
            order[$ADDRESS_PROP.attr("name")] = $ADDRESS_PROP.val();
        }

        order['sessid'] = $("#sessid").val();
        order['PROFILE_ID'] = $("#PROFILE_ID").val();
        //order['DELIVERY_ID'] = $("#DELIVERY_ID").val();
        order['SITE_ID'] = $("#SITE_ID").val();
        order['ORDER_DESCRIPTION'] = $("#ORDER_DESCRIPTION").val();
        order['ZIP_PROPERTY_CHANGED'] = "N";

        $order.find("input[data-property-code]").each((index, item) => {
            order[$(item).attr("name")] = $(item).val();
        });

        $order.find("button[data-property-code]").each((index, item) => {
            let $item = $(item);
            let name = $item.attr("name");
            let multiple = $item.attr("data-multiple");
            let code = $item.attr("data-property-code");

            if (order[name] === undefined) {
                order[name] = [];
            }

            if ($item.hasClass("selected")) {
                if (multiple === 'Y') {
                    order[name] += code + ';';
                } else {
                    order[name].push(code);
                }
            }
        });

        for (let key in order) {
            if (data[key] === undefined) {
                data[key] = order[key];
            }
        }

        let dataType;

        if (data['via_json'] !== undefined && data['via_json'] === 'Y') {
            //save order request
            dataType = 'json';

            data['save'] = "Y";
            data['action'] = "saveOrderAjax";
            data['soa-action'] = "saveOrderAjax";
        } else {
            //refresh make-order form
            dataType = 'html';

            data['SITE_ID'] = $("#SITE_ID").val();
            data['is_ajax_post'] = 'Y';
            data['signedParamsString'] = $("#signedParamsString").val();
            data['order'] = order;

            let $LOCATION_ALT_PROP_DISPLAY_MANUAL = $("#LOCATION_ALT_PROP_DISPLAY_MANUAL");
            data[$LOCATION_ALT_PROP_DISPLAY_MANUAL.attr("name")] = $LOCATION_ALT_PROP_DISPLAY_MANUAL.val();
        }

        let url = '';

        if (!!setCity) {
            //Menu.setCity(setCity, true);
        }

        $.ajax({
            type: 'POST',
            url,
            dataType,
            data,
            beforeSend: (xhr) => {
                console.info('BEFORE AJAX SEND');
                console.log('url:', url);
                console.log('dataType:', dataType);
                console.log('data:', data);

                if ($sender.hasClass("loading")) {
                    xhr.abort();
                    return false;
                }
                $sender.addClass('loading');
            },
            success: (response) => {
                console.info('AJAX SUCCESS');

                if (dataType === 'html') {
                    //console.log(response);

                    //if (!!onComplete) {
                    //    onComplete();
                    //} else {
                    $order.html(response);

                    let $summary = $(response).find('#order-flysummary');
                    let price = $summary.find('#summary-price').html();
                    let quantity = $summary.find('#summary-quantity').html();

                    let $line = $("#basket-line");
                    $line.find(".js-line-price").html(price);
                    $line.find(".js-line-quantity").html(quantity);
                    //}
                } else {
                    console.log(response);

                    if (!!response.order) {
                        let order = response.order;
                        let errors;

                        if (!!order.ERROR) {
                            let $errors = $order.find("#order-errors");
                            let $errors_list = $errors.find(".order-errors-list");
                            $errors_list.empty();
                            $errors.removeClass("hidden");

                            for (let GROUP in order.ERROR) {
                                for (let KEY in order.ERROR[GROUP]) {
                                    let ERROR = order.ERROR[GROUP][KEY];
                                    $errors_list.append('<span>' + ERROR + '</span>');
                                }
                            }

                            init();
                            App.maskPhones();
                            App.scrollTo($order.offset().top);
                            App.waitStop();

                        } else if (!!order.REDIRECT_URL) {
                            //succes
                            App.reachGoal('zakaz_oform');

                            let ACTIONS_GOALS = JSON.parse($("#ACTIONS_GOALS").val());
                            if (!!ACTIONS_GOALS) {
                                for (let i in ACTIONS_GOALS) {
                                    App.reachGoal(ACTIONS_GOALS[i]);
                                }
                            }

                            document.location.href = order.REDIRECT_URL;
                        }
                    }
                }
            },
            error: (response) => {
                console.error('AJAX FAIL!');
                console.log(response);
            },
            complete: () => {
                console.info('AJAX COMPLETE');

                if (!!onComplete) {
                    onComplete();
                } else {
                    $sender.removeClass('loading');

                    if (dataType === 'html') {
                        init();
                        App.maskPhones();
                        App.waitStop();
                    }
                }
            }
        });
    };

    let submit = (event, sender) => {
        event.preventDefault();
        console.log('form submit');
    };

    let send = (event, sender) => {
        event.preventDefault();
        console.log('form send');

        let $sender = $(sender);

        let data = {
            'via_json': "Y"
        };

        let order = {
        };

        refresh(sender, order, data);
    };

    let flySummary = () => {
        let $wrap = $(".order-content-wrap");
        let $flysummary = $("#order-flysummary");

        if (!$wrap.length || !$flysummary.length) {
            return;
        }

        let init_top = 0,
                scrollTop = $(document).scrollTop(),
                offsetTop = $wrap.offset().top;


        let delta = scrollTop - offsetTop + init_top;
        let limit = offsetTop + $wrap.outerHeight() - $flysummary.outerHeight() - init_top;

        if (delta > 0 && scrollTop <= limit) {
            $flysummary.css({
                top: delta,
                bottom: 'auto'
            });
        } else if (delta <= 0) {
            $flysummary.css({
                top: init_top,
                bottom: 'auto'
            });
        } else {
            $flysummary.css({
                top: 'auto',
                bottom: 0
            });
        }
    };

    let onClickPlaceholder = (sender) => {
        let $placeholder = $(sender);
        let $input = $placeholder.parent().find("input,textarea");

        if ($placeholder.hasClass('active')) {
            return false;
        }

        $placeholder.addClass("active");
        $input.focus();
    };

    let onInputFocus = (sender) => {
        let $input = $(sender);
        let $parent = $input.parent();
        let $placeholder = $parent.find("span").first();
        let $enter_link = $parent.find(".js-enter-link");

        $parent.removeClass("error");

        if (!$placeholder.hasClass("active")) {
            $placeholder.addClass("active");
        }
    };

    let onInputBlur = (sender) => {
        let $input = $(sender);
        let $parent = $input.parent();
        let $placeholder = $parent.find("span").first();

        let $enter_link = $parent.find(".js-enter-link");
        let $enter_spinner = $parent.find(".js-enter-spinner");
        let $enter_checkmark = $parent.find(".js-enter-checkmark");
        let $enter_close = $parent.find(".js-enter-close");

        let type = $input.attr("data-type");
        let checkUnique = $input.attr("data-check-unique");
        let checkPhone = $input.attr("data-check-phone");
        let value = $.trim($input.val());

        if (checkPhone === "Y") {
            let symb = value.charAt(4);

            if (symb !== '9') {
                $input.val("+7");
                $parent.addClass("error");
            }
        }

        if ((type === "phone" || type === "email") && !!value) {
            $enter_link.slideUp();
            $enter_checkmark.hide();
            $enter_close.hide();

            if (checkUnique === "Y" && (!!value && value !== '+7 (___) ___-__-__')) {
                $enter_spinner.show();
            }

            let onSuccess = function (response) {
                $enter_spinner.hide();

                if (response.unique === false) {
                    $enter_link.slideDown();
                    $enter_close.show();
                } else {
                    $enter_checkmark.show();
                }
            };

            if (checkUnique === "Y" && (!!value && value !== '+7 (___) ___-__-__')) {
                User.checkUnique(value, type, onSuccess);
            }
        }

        if (f.isEmpty($input.val()) && $placeholder.hasClass("active")) {
            $placeholder.removeClass("active");
        }
    };

    let initPlaceholders = ($form) => {
        let $wrap = $(".order-content-wrap");

        $wrap.find("input[placeholder]").each(function (index, element) {
            let $element = $(element);

            if (!f.isEmpty($element.find("input").val())) {
                $element.find("span").addClass("active");
            } else {
                $element.find("span").removeClass("active");
            }
        });
    };

    let bindActions = () => {
        if (f.isIE()) {
            f.fixMouseJump();
        }

        $(window).bind("scroll resize orientationchange", function () {
            flySummary();
        });
    };

    let initDeliveryCalc = () => {
        let $calc = $('#ANOTHER_CITY_PROP');
        if ($('input').is($calc)) {
            Form.initSearchDestinations($calc);
        }
    };

    let setDeliveryCalcOption = (event, sender) => {
        event.preventDefault();

        let $button = $(sender);
        $button.toggleClass('selected');

        let hInputID = $button.data('hinput');
        let $hInput = $('#' + hInputID);
        $button.hasClass('selected') ? $hInput.val('Да') : $hInput.val('Нет');
    };

    let setDeliveryCalcResult = (event, sender) => {
        event.preventDefault();

        let $items = $('.order-dc__delivery-item');
        $items.find('button').removeClass('selected');

        let $button = $(sender);
        $button.addClass('selected');

        let newDeliveryTK = $button.data('tkname');
        let $deliveryNameInput = $('#DELIVERY_TK_PROP');
        $deliveryNameInput.val(newDeliveryTK);

        let newDeliveryCost = $button.data('tkcost');
        let $deliveryCostInput = $('#DELIVERY_COST_PROP');
        let $deliveryCostLabel = $('#order-flysummary-delivery-cost');
        $deliveryCostInput.val(newDeliveryCost);
        $deliveryCostLabel.html(newDeliveryCost + ' <span class="rouble"></span>');
    };

    let deliveryCalc = (event, sender) => {
        event.preventDefault();

        let $button = $(sender);

        let params = {
            ANOTHER_CITY_PROP: $('#ANOTHER_CITY_PROP').val(),
            DELIVERY_TK_NEED_DELIVERY: $('#DELIVERY_TK_NEED_DELIVERY').hasClass('selected') ? 1 : 0,
            DELIVERY_TK_LATHING: $('#DELIVERY_TK_LATHING').hasClass('selected') ? 1 : 0,
            DELIVERY_TK_INSURING: $('#DELIVERY_TK_INSURING').hasClass('selected') ? 1 : 0
        };
        let query = $.param(params);
        console.log(query);

        let $container = $(document).find("#order-dc");
        let $results = $(document).find("#order-dc-results");
        $results.removeClass("active");

        let url = "/ajax/ajax_form.php";

        let data = {
            action: "get_delivery_calc_variants",
            AJAX: "Y",
            data: query
        };

        let onSuccess = (response) => {
            let variants = response;
            let html = '<div class="order-dc__results-title">Выберите транспортную компанию</div>';

            for (var i = 0; i < variants.length; i++) {
                let variant = variants[i];

                let company = variant['company_name'];
                let site = variant['site'];
                let from_city = variant['from_city'];
                let to_city = variant['to_city'];
                let auto_period = parseInt(variant['auto_period']);
                let auto_total = variant['auto_total'];

                html += '<div class="order-dc__delivery-item">' +
                        '<button class="order-checkbox" id="ORDER_DC_RESULT_' + i + '" data-tkcost="' + auto_total + '"' +
                        ' data-tkname="' + company + '" onclick="Order.setDeliveryCalcResult(event, this);">' +
                        '<i></i>' +
                        '<span>' +
                        '<a class="order-dc__delivery-item__title" href="' + site + '" title="' + company + '" target="_blank">' + company + '</a>' +
                        '</span>' +
                        '</button>' +
                        '<span class="order-dc__delivery-item__track">' + from_city + ' &gt; ' + to_city + '</span>' +
                        '<span class="order-dc__delivery-item__price">' + auto_total + ' руб.';

                if (0 && !!auto_period && auto_period !== -1 && auto_period !== 0) {
                    html += '<span class="order-dc__delivery-item__period"> - ' +
                            auto_period + f.wordPlural(auto_period, ['день', 'дня', 'дней']) +
                            '</span>';
                }

                html += '</span>' +
                        '</div>'
                        ;
            }

            App.scrollTo($button.offset().top - 50);
            $results.html(html).addClass("active");
        };

        Ajax.send(url, data, $container, onSuccess);
    };

    let init = () => {
        console.log('init order');
        bindActions();
        flySummary();
        initPlaceholders();
        initDeliveryCalc();

        window.Order = Order;
    };

    return {
        init, refresh,
        changePersonType, changeStore, changePaySystem, changeDeliveyMethod,
        setRadio, setCheckbox, changeLocation,
        submit, send,
        onClickPlaceholder, onInputFocus, onInputBlur,
        changeBonusType, onBonusChange, onBonusKeyUp,
        setDeliveryCalcOption, setDeliveryCalcResult, deliveryCalc
    };
})();

$(() => Order.init());