'use strict';
/* global f, App, Ajax, yaCounter12153865, BX, Faq, Services */

require("mousewheel");
require("scrollbox-js");
require("scrollbox-css");
require("air-datepicker-js");
require("air-datepicker-css");
//require("jquery.maskedinput");
//require("jquery-mask-plugin");
require("inputmask");
require("autocomplete");

export let Form = (() => {

    let className = 'opened';

    let maskPhone = ($input) => {
        //$input.mask("+7 (000) 000-00-00");
        $input.inputmask({
            mask: "+7 (999) 999-99-99",
            showMaskOnHover: false,
            clearIncomplete: true
        });
        //$input.val("+7 (___) ___-__-__");
        $input.focus();
        $input[0].setSelectionRange(4, 4);
    };

    let maskPhoneOrEmail = (event, sender) => {
        let $sender = $(sender);

        let inputLength = $sender.val().length;
        let keyCode = event.keyCode;

        if (inputLength === 0) {
            $sender.unmask();
        } else if (inputLength === 2 && $sender.val() === '+7') {
            maskPhone($sender);
        }
    };

    let toggleForm = (sender, loadDestinations = false) => {
        let $sender = $(sender);
        let form = $sender.attr("data-form");
        let $form = $(form);

        $form.hasClass(className) ? closeForm(sender) : openForm(sender);
    };

    let openForm = (sender) => {
        let $sender = $(sender);
        let form = $sender.attr("data-form");
        let $form = $(form);
        let eventName = f.getEventNameByAlias(form);

        if (form === "#buy-oneclick") {
            openBuyOneClickForm($sender, $form);
        }
        
        if (form === "#form-service-entry") {
            Services.onOpenServiceEntryForm();
        }

        if (form === "#help_akb") {
            App.reachGoal('help_akb_open');
        }

        if (form === "#delivery_calc") {
            initSearchDestinations($("#autocomplete-destinations"));
            //App.reachGoal('delivery_calc_open');
            //$form.find("form").addClass("loading");

//            let data = {
//                action: 'get_delivery_calc_destinations'
//            };
//
//            let onSuccess = function (response) {
//
//            };

            //Ajax.send("/ajax/ajax_form.php", data, $form.find("form"), onSuccess);
        }

        initFormPlaceholders($form);
        $form.addClass(className);
        $("#body").addClass("inactive");

        let onUnbind = () => closeForm(sender);
        App.bindClick($sender, $form, eventName, className, onUnbind);
    };

    let closeForm = (sender) => {
        let $sender = $(sender);
        let form = $sender.attr("data-form");
        let $form = $(form);
        let eventName = f.getEventNameByAlias(form);

        $form.removeClass(className);
        $form.find(".form-result").empty().addClass("hidden");
        $form.find(".form-right").removeClass("hidden");

        $("#body").removeClass("inactive");
        $(document).unbind(eventName);
    };

    let openBuyOneClickForm = ($sender, $form) => {
        $form.find('.form-description').text($sender.attr("data-product-name"));

        $form.find('input[data-type="PRODUCT_XML_ID"]').val($sender.attr("data-product-xml-id"));
        $form.find('input[data-type="QUANTITY"]').val($sender.attr("data-quantity"));
        $form.find('input[data-type="USER_ID"]').val($sender.attr("data-user-id"));
        $form.find('input[data-type="CITY"]').val($sender.attr("data-user-city"));
        $form.find('input[data-type="USER_NAME"]').val($sender.attr("data-user-name"));
        $form.find('input[data-type="USER_PHONE"]').val($sender.attr("data-user-phone"));

    };

    let onSuccessBuyOneClickForm = (response) => {
        let $container = $("#buy_one_click");

        if (response.includes('RESULT_OK')) {
            App.reachGoal('buy_one_click');
            response = response.replace('RESULT_OK', '');

            $container.find(".form-result").html(response).removeClass("hidden");
            $container.find(".form-right").addClass("hidden");
        } else {
            $container.html(response);
        }
    };

    let onSuccessHelpAkbForm = (response) => {
        let $container = $("#help_akb");

        if (response.includes('RESULT_OK')) {
            App.reachGoal('help_akb_success');
            response = response.replace('RESULT_OK', '');

            $container.find(".form-result").html(response).removeClass("hidden");
            $container.find(".form-right").addClass("hidden");
        } else {
            $container.html(response);
        }
    };

    let onSuccessSupportForm = (response) => {
        let $container = $("#form-support");

        if (response.includes('RESULT_OK')) {
            App.reachGoal('support_form');
            response = response.replace('RESULT_OK', '');

            $container.find(".form-result").html(response).removeClass("hidden");
            $container.find(".form-right").addClass("hidden");
        } else {
            $container.html(response);
        }
    };

    let onSuccessFaqForm = (response) => {
        let $container = $("#form-faq");

        if (response.includes('RESULT_OK')) {
            console.log('RESULT_OK');
            response = response.replace('RESULT_OK', '');

            $container.find(".form-result").html(response).removeClass("hidden");
            $container.find(".form-faq").addClass("hidden");

        } else {
            console.log('RESULT_FAIL');

            let $response = $(response);
            let $response_errors = $response.find(".form-errors-text").html();

            console.log($response_errors);

            $container.find(".form-error").html($response_errors).removeClass("hidden");
        }
    };

    let onClickPlaceholder = (sender) => {
        let $placeholder = $(sender);
        let $input = $placeholder.parent().find("input, textarea");

        if ($placeholder.hasClass('active')) {
            return false;
        }

        $placeholder.addClass("active");
        $input.focus();
    };

    let onInputFocus = (sender) => {
        let $input = $(sender);
        let $placeholder = $input.parent().find(".form-question-placeholder");

        $input.parent().removeClass("error");
        if (!$placeholder.hasClass("active")) {
            $placeholder.addClass("active");
        }
    };

    let onInputBlur = (sender) => {
        let $input = $(sender);
        let $placeholder = $input.parent().find(".form-question-placeholder");

        if (f.isEmpty($input.val()) && $placeholder.hasClass("active")) {
            $placeholder.removeClass("active");
        }
    };

    let initFormPlaceholders = ($form) => {
        $form.find(".form-question").each(function (index, element) {
            let $element = $(element);

            if (!f.isEmpty($element.find("input").val())) {
                $element.find(".form-question-placeholder").addClass("active");
            } else {
                $element.find(".form-question-placeholder").removeClass("active");
            }
        });
    };

    let submit = (e, sender) => {
        e.preventDefault();
        let $form = $(sender);
        let formName = $form.attr("name");
        let formAction = f.isEmpty($form.attr("action")) ? "" : $form.attr("action");

        if (checkFormFields($form)) {
            let onSuccess = function (response) {

                if (formName === "BUYCLICK") {
                    onSuccessBuyOneClickForm(response);
                }

                if (formName === "HELPAKB") {
                    onSuccessHelpAkbForm(response);
                }

                if (formName === "SUPPORT") {
                    onSuccessSupportForm(response);
                }

                if (formName === "FAQ") {
                    onSuccessFaqForm(response);
                }

                App.maskPhones();
            };

            let url = formAction + "?AJAX_REQUEST=Y";
            let data = $form.serialize() + "&web_form_submit=Отправить";

            Ajax.html(url, data, $form, onSuccess);
        }
    };

    let checkBXSessid = ($form) => {
        let result = $form.find('[name="sessid"]').val() === BX.bitrix_sessid();
        if (result !== true) {
            App.showAlert('Session error');
        }
        return result;
    };

    let checkFormFields = ($form) => {
        let result = true;

        $(".form-error").empty().hide();
        $(".form-errors-text").empty().addClass("hidden");

        $form.find(".form-question").each(function (index, element) {
            let $element = $(element);
            let $input = $element.find("input, textarea");
            let name = $input.attr("name");
            let $error = $element.find(".form-question-error");

            //clear errors
            $element.removeClass("error");

            if ($input.attr("data-required") === "Y" && f.isEmpty($input.val()) === true) {
                result = false;
                $element.addClass("error");
                $error.text("Заполните это поле");
            }

            if ($input.attr("data-input-dropdown") === "Y" && f.isEmpty($input.val()) === true) {
                result = false;
                $element.addClass("error");
                $error.text("Выберите значение");
            }

            if (name === "PASSWORD2") {
                if ($input.val() !== $form.find('[name="PASSWORD"]').val()) {
                    result = false;
                    $element.addClass("error");
                    $error.text("Пароли не совпадают");
                }
            }
        });

        return result;
    };

    let toggleFakeCheckbox = (sender, val_active = "1", val_inactive = "0") => {
        let $sender = $(sender);
        let $input = $sender.find("input");
        let input_name = $input.attr("name");

        $sender.toggleClass("selected");

        if ($sender.hasClass("selected")) {
            $input.val(val_active);

            if (input_name === "CONSENT") {
                $(".form-submit-button, .order-summary-button > button").removeClass("disabled");
            }
        } else {
            $input.val(val_inactive);
            setTimeout(() => $sender.blur(), 100);

            if (input_name === "CONSENT") {
                $(".form-submit-button, .order-summary-button > button").addClass("disabled");
            }
    }
    };

    let toggleDropdown = (sender) => {
        let $sender = $(sender);
        let $block = $sender.parent();

        $block.parent().removeClass("error");

        $block.hasClass(className) ? closeDropdown(sender) : openDropdown(sender);
    };

    let openDropdown = (sender) => {
        let $sender = $(sender);
        let $block = $sender.parents(".form-question-fakeselect");
        let $list = $block.find("ul");
        let eventName = f.getEventNameByAlias("dropdown." + $block.attr("data-target"));

        $block.addClass(className).find("ul").scrollbox();

        let onUnbind = () => closeDropdown(sender);
        App.bindClick($sender, $block, eventName, className, onUnbind);
    };

    let closeDropdown = (sender) => {
        let $sender = $(sender);
        let $block = $sender.parents(".form-question-fakeselect");
        let eventName = f.getEventNameByAlias("dropdown." + $block.attr("data-target"));

        $block.removeClass(className).find("ul").scrollbox("destroy");
        $(document).unbind(eventName);
    };

    let setDropdown = (sender, async = false) => {
        let $sender = $(sender);
        let $list = $sender.parent();
        let $block = $sender.parents(".form-question-fakeselect");
        let $title = $block.find(".form-question-fakeselect-current");
        let target = $block.attr("data-target");
        let $input = $block.parent().find('input[name="' + target + '"]');

        let dropdown_id = $sender.attr("data-dropdown-id");

        if (async === false) {
            Faq.setCategory($('[data-category-id="' + dropdown_id + '"]').get(0), true);
        }

        $list.find("li").removeClass("selected");
        $sender.addClass("selected");
        $input.val(dropdown_id);
        $title.find("span").text($sender.find("span").text());
        closeDropdown(sender);
    };

    let setDestination = (sender, async = false) => {
        let $sender = $(sender);
        let $list = $sender.parent();
        let $block = $sender.parents(".form-question-fakeselect");
        let $title = $block.find(".form-question-fakeselect-current");
        let target = $block.attr("data-target");
        let $input = $block.parent().find('input[name="' + target + '"]');

        let dropdown_id = $sender.attr("data-dropdown-id");

        if (async === false) {
            //setCategory($('[data-category-id="' + dropdown_id + '"]').get(0), true);
        }

        $list.find("li").removeClass("selected");
        $sender.addClass("selected");
        $input.val(dropdown_id);
        $title.find("span").text($sender.find("span").text());
        closeDropdown(sender);
    };

    let setSubscribe = () => {
        let SUBSCRIBE = '';

        if ($('[name="SUBSCRIBE_EMAIL"]').val() === "1") {
            SUBSCRIBE += "EMAIL;";
        }
        if ($('[name="SUBSCRIBE_SMS"]').val() === "1") {
            SUBSCRIBE += "SMS;";
        }

        $('[name="SUBSCRIBE"]').val(SUBSCRIBE);
    };

    let register = (e, sender) => {
        e.preventDefault();
        App.waitStart();

        let $form = $(sender);
        let $errorsBlock = $(".form-error");

        $errorsBlock.slideUp().empty();

        if (checkFormFields($form) && checkBXSessid($form)) {
            let onSuccess = (response) => {
                if (!!response.redirect) {
                    document.location.href = response.redirect;
                } else {
                    App.waitStop();
                }
            };

            let OnError = (response) => {
                App.maskPhones();
                $errorsBlock.html(response.message).slideDown();
                App.scrollTo($(".auth-inner").offset().top - 40);
                App.waitStop();
            };

            setSubscribe();

            let data = {
                ACTION: 'REGISTER',
                VALUES: $form.serialize(),
                JS_SESSID: BX.bitrix_sessid()
            };
            Ajax.send('/ajax/ajax_user.php', data, $form, onSuccess, OnError);
        } else {
            App.waitStop();
        }
    };

    let saveUser = (e, sender) => {
        e.preventDefault();
        App.waitStart();

        let $form = $(sender);
        let $errorsBlock = $(".form-error");
        let $resultBlock = $(".form-result");

        $errorsBlock.slideUp().empty();
        $resultBlock.slideUp().empty();

        if (checkFormFields($form) && checkBXSessid($form)) {
            let onSuccess = (response) => {
                if (!!response.redirect) {
                    document.location.href = response.redirect;
                } else {
                    App.maskPhones();
                    $resultBlock.html(response.message).slideDown();
                    App.scrollTo($(".auth-inner").offset().top - 40);
                    App.waitStop();
                }
            };

            let OnError = (response) => {
                App.maskPhones();
                $errorsBlock.html(response.message).slideDown();
                App.scrollTo($(".auth-inner").offset().top - 40);
                App.waitStop();
            };

            setSubscribe();

            let data = {
                ACTION: 'SAVE_USER',
                VALUES: $form.serialize(),
                JS_SESSID: BX.bitrix_sessid()
            };
            Ajax.send('/ajax/ajax_user.php', data, $form, onSuccess, OnError);
        } else {
            App.waitStop();
        }
    };

    let auth = (e, sender) => {
        e.preventDefault();
        App.waitStart();

        let $form = $(sender);
        let $errorsBlock = $(".form-error");

        $errorsBlock.slideUp().empty();

        if (checkFormFields($form) && checkBXSessid($form)) {
            let onSuccess = (response) => {
                document.location.href = response.redirect;
            };

            let OnError = (response) => {
                App.maskPhones();
                $errorsBlock.html(response.message).slideDown();
                App.scrollTo($(".auth-inner").offset().top - 40);
                App.waitStop();
            };

            setSubscribe();

            let data = {
                ACTION: 'AUTH',
                VALUES: $form.serialize(),
                JS_SESSID: BX.bitrix_sessid()
            };
            Ajax.send('/ajax/ajax_user.php', data, $form, onSuccess, OnError);
        } else {
            App.waitStop();
        }
    };

    let recovery = (e, sender) => {
        e.preventDefault();
        App.waitStart();

        let $form = $(sender);
        let $errorsBlock = $(".form-error");

        $errorsBlock.slideUp().empty();

        if (checkFormFields($form) && checkBXSessid($form)) {
            let onSuccess = (response) => {
                if (!!response.redirect) {
                    document.location.href = response.redirect;
                    return true;
                } else {
                    App.maskPhones();
                    $errorsBlock.html(response.message).slideDown();
                    App.waitStop();
                }
            };

            let OnError = (response) => {
                App.maskPhones();
                $errorsBlock.html(response.message).slideDown();
                App.scrollTo($(".auth-inner").offset().top - 40);
                App.waitStop();
            };

            setSubscribe();

            let data = {
                ACTION: 'RECOVERY',
                VALUES: $form.serialize(),
                JS_SESSID: BX.bitrix_sessid()
            };
            Ajax.send('/ajax/ajax_user.php', data, $form, onSuccess, OnError);
        } else {
            App.waitStop();
        }
    };

    let change = (e, sender) => {
        e.preventDefault();
        App.waitStart();

        let $form = $(sender);
        let $errorsBlock = $(".form-error");

        $errorsBlock.slideUp().empty();

        if (checkFormFields($form) && checkBXSessid($form)) {
            let onSuccess = (response) => {
                if (!!response.redirect) {
                    document.location.href = response.redirect;
                    return true;
                } else {
                    App.maskPhones();
                    $errorsBlock.html(response.message).slideDown();
                    App.waitStop();
                }
            };

            let OnError = (response) => {
                App.maskPhones();
                $errorsBlock.html(response.message).slideDown();
                App.scrollTo($(".auth-inner").offset().top - 40);
                App.waitStop();
            };

            setSubscribe();

            let data = {
                ACTION: 'CHANGE',
                VALUES: $form.serialize(),
                JS_SESSID: BX.bitrix_sessid()
            };
            Ajax.send('/ajax/ajax_user.php', data, $form, onSuccess, OnError);
        } else {
            App.waitStop();
        }
    };

    let addcard = (e, sender) => {
        e.preventDefault();
        App.waitStart();

        let $form = $(sender);
        let $errorsBlock = $(".form-error");

        $errorsBlock.slideUp().empty();

        if (checkFormFields($form) && checkBXSessid($form)) {
            let onSuccess = (response) => {
                document.location.href = response.redirect;
            };

            let OnError = (response) => {
                App.maskPhones();
                $errorsBlock.html(response.message).slideDown();
                App.scrollTo($(".auth-form").offset().top - 40);
                App.waitStop();
            };

            let data = {
                ACTION: 'ADDCARD',
                VALUES: $form.serialize(),
                JS_SESSID: BX.bitrix_sessid()
            };
            Ajax.send('/ajax/ajax_user.php', data, $form, onSuccess, OnError);
        } else {
            App.waitStop();
        }
    };

    let bindActions = () => {
        $('input[type="datepicker"]').datepicker({
            view: 'years',
            autoClose: true
        });
        //$('input[type="passport"]').mask("99 99 999999");
        $('input[type="passport"]').inputmask("99 99 999999");

        setTimeout(function () {
            console.log('autofill detecting...');

            try {
                $('input:-webkit-autofill').each(function (i, item) {
                    let $placeholder = $(item).parent().find(".form-question-placeholder").addClass("active");
                });
            } catch (e) {
                console.warn(e.message);
            }
        }, 25);

    };

    let init = () => {
        console.log('init form');
        window.Form = Form;

        bindActions();
    };

    let kreditSendRequest = (e, sender) => {
        e.preventDefault();
        let $form = $(sender);
        let $wrap = $form.parents(".kredit");
        let $errorsBlock = $form.find(".form-error");
        let $resultsBlock = $form.find(".form-result");

        let $innerBlock = $wrap.find(".kredit-inner");
        let $waitBlock = $wrap.find(".kredit-wait");

        if ($innerBlock.hasClass("wait")) {
            kreditWaitResult(sender);
            return false;
        }

        $errorsBlock.slideUp().empty();

        if (checkFormFields($form) && checkBXSessid($form)) {
            $innerBlock.addClass("wait");
            $waitBlock.addClass("active");

            let onSuccess = (response) => {
                response = $.parseJSON(response);

                App.maskPhones();
                bindActions();

                if (response.success) {
                    console.log(response);
                    kreditWaitResult(sender);
                } else {
                    console.log(response.result);

                    $innerBlock.removeClass("wait");
                    $waitBlock.removeClass("active");

                    if (!!response.result.message) {
                        $errorsBlock.html(response.result.message).slideDown();
                    } else if (!!response.result.reason) {
                        $errorsBlock.html(response.result.reason).slideDown();
                    }
                }
            };

            let data = {
                AJAX: 'Y',
                ACTION: 'KREDIT_REQUEST',
                VALUES: $form.serialize(),
                JS_SESSID: BX.bitrix_sessid()
            };

            //в ответе будет html, но это будет строка json.
            Ajax.html('', data, $form, onSuccess);
        } else {
            console.log('error safity 1');
        }
    };

    let kreditWaitResultInterval;
    let kreditGetResult = (sender) => {
        console.log('kreditGetResult');

        let $form = $(sender);
        let $wrap = $form.parents(".kredit");
        let $errorsBlock = $form.find(".form-error");

        let $innerBlock = $wrap.find(".kredit-inner");
        let $waitBlock = $wrap.find(".kredit-wait");

        if (!$innerBlock.hasClass("wait")) {
            return false;
        }

        $errorsBlock.slideUp().empty();

        if (checkBXSessid($form)) {
            let onSuccess = (response) => {
                response = $.parseJSON(response);
                console.log(response);

                if (response.success) {
                    let result = response.result[0];
                    let status = result.status;

                    if (status === "accepted" || status === "cancel") {
                        clearInterval(kreditWaitResultInterval);
                        location.reload();
                    }
                } else {
                    $innerBlock.removeClass("wait");
                    $waitBlock.removeClass("active");
                    $errorsBlock.html(response.message).slideDown();
                }
            };

            let data = {
                AJAX: 'Y',
                ACTION: 'KREDIT_RESULT',
                VALUES: $form.serialize(),
                JS_SESSID: BX.bitrix_sessid()
            };

            //в ответе будет html, но это будет строка json.
            Ajax.html('', data, $form, onSuccess);
        } else {
            console.log('error safity 2');
        }
    };

    let kreditWaitResult = (sender) => {
        console.log('kreditWaitResult');

        kreditWaitResultInterval = setInterval(function () {
            kreditGetResult(sender);
        }, 1000);
    };

    let onloadHandler = () => {
        if (!!$(".kredit-wait") && $(".kredit-wait").hasClass("active")) {
            kreditWaitResult($("#kredit-form").get(0));
        }
    };

    let deliveryCalc = (event, sender) => {
        event.preventDefault();

        let $form = $(document).find("#delivery_calc").find("form");
        let $results = $(document).find("#delivery_calc-results");

        $results.removeClass("active");

        let url = "/ajax/ajax_form.php";

        let data = {
            action: "get_delivery_calc_variants",
            AJAX: "Y",
            data: $form.serialize()
        };

        let onSuccess = (response) => {
            let variants = response;
            let html = '<div class="delivery_calc-results__title">Результаты</div>';

            for (var i = 0; i < variants.length; i++) {
                let variant = variants[i];

                let company = variant['company_name'];
                let site = variant['site'];
                let from_city = variant['from_city'];
                let to_city = variant['to_city'];
                let auto_period = parseInt(variant['auto_period']);
                let auto_total = variant['auto_total'];

                html += '<div class="delivery-item">' +
                        '<a class="delivery-item__title" href="' + site + '" title="' + company + '" target="_blank">' + company + '</a>' +
                        '<span class="delivery-item__track">' + from_city + ' &gt; ' + to_city + '</span>' +
                        '<span class="delivery-item__price">' + auto_total + ' руб.';

                if (0 && !!auto_period && auto_period !== -1 && auto_period !== 0) {
                    html += '<span class="delivery-item__period"> - ' +
                            auto_period + f.wordPlural(auto_period, ['день', 'дня', 'дней']) +
                            '</span>';
                }

                html += '</span>' +
                        '</div>'
                        ;
            }

            $results.html(html).addClass("active");
        };

        Ajax.send(url, data, $form, onSuccess);
    };

    let initSearchDestinations = ($input) => {
        let formatResult = (suggestion, currentValue) => {
            console.log(suggestion, currentValue);

            if (!currentValue) {
                return suggestion.value;
            }

            let title = suggestion.value;
            let data = suggestion.data;

            let result = '';

            if (!!data.url) {
                result += '<a href="' + data.url + '" title="' + title + '">' + title;
            } else {
                result += '<span class="js-stop-propogation">' + title + '</span>';
            }

            if (!!data.description) {
                result += ', <span class="autocomplete-suggestion-description">' + data.description + '</span>';
            }

            if (!!data.url) {
                result += '</a>';
            }

            return result;
        };

        let onSelect = (suggestion) => {
            $(document).find("#delivery-calc-submit").removeClass("disabled");
            $(document).find("#ORDER_DC_SUBMIT").removeClass("disabled");
        };

        let onSearchError = (query, jqXHR, textStatus, errorThrown) => {
            App.showAlert('Произошла ошибка');
        };

        let formatGroup = (suggestion, category) => {
            return '<div class="autocomplete-group"><span>' + category + '</span></div>';
        };

        let noSuggestionNotice = () => {
            return '<div class="autocomplete-group">Ничего не найдено</div>';
        };

        let lookup = (query, done) => {
            let $sender = $($input);

            let url = "/ajax/ajax_form.php";

            let data = {
                action: "get_delivery_calc_destinations",
                AJAX: "Y",
                QUERY: query
            };

            let result = {
                suggestions: {}
            };

            let onSuccess = (response) => {
                result.suggestions = response;

                done(result);
            };

            Ajax.send(url, data, $sender, onSuccess);

        };

        let opts = {
            noCache: true,
            minChars: 2,
            maxHeight: 480,
            width: 'flex', //pixels, 'flex' for max suggestion size , 'auto' takes input field width
            zIndex: 9999,
            groupBy: 'category',
            showNoSuggestionNotice: true,
            noSuggestionNotice,
            formatGroup,
            formatResult,
            onSelect,
            onSearchError,
            lookup
        };

        $input.autocomplete(opts);
    };

    return {
        init, toggleForm, openForm, closeForm, onInputFocus, onInputBlur,
        onClickPlaceholder, submit, toggleFakeCheckbox, toggleDropdown, closeDropdown, setDropdown,
        register, auth, recovery, change, saveUser, kreditSendRequest, kreditWaitResult, onloadHandler,
        maskPhoneOrEmail, addcard, checkFormFields, checkBXSessid, setDestination, deliveryCalc, initSearchDestinations
    };
})();

$(() => Form.init());

$(window).load(() => {
    Form.onloadHandler();
});