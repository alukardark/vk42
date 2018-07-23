'use strict';
/* global f, App, Ajax, yaCounter12153865, BX, Faq, Form */

let Card = (() => {
    const resendTimeout = 120;
    let resendInterval = null;

    let goToStep = (e, step) => {
        e.preventDefault();

        $(".addcard-step").slideUp();
        $("#addcard-step" + step).slideDown();
    };

    let startResendTimer = () => {
        let $counter = $("#resend-timer");
        let $descr = $("#resend-descr");
        let $link = $("#resend-link");

        $descr.show();
        $link.removeClass("active");
        $counter.text(resendTimeout);

        clearInterval(resendInterval);
        resendInterval = setInterval(function () {
            let current = parseInt($counter.text());

            if (current > 0) {
                $counter.text(--current);
            } else {
                clearInterval(resendInterval);
                $descr.hide();
                $link.addClass("active");
            }
        }, 1000);
    };

    let sendSmsAndSaveUser = (e, sender) => {
        e.preventDefault();
        App.waitStart();

        let $form = $(sender);
        let $errorsBlock = $form.parent().find(".form-error");

        $errorsBlock.slideUp().empty();

        if (Form.checkFormFields($form) && Form.checkBXSessid($form)) {
            let onSuccess = (response) => {
                App.maskPhones();

                let cardNumber = $("#addcard-step1").find('input[name="CARD"]').val();
                $("#addcard-step2").find('input[name="CARD"]').val(cardNumber);

                $(".addcard-step").slideUp();
                $("#addcard-step2").slideDown();

                startResendTimer();

                App.waitStop();
            };

            let OnError = (response) => {
                App.maskPhones();
                $errorsBlock.html(response.message).slideDown();
                App.scrollTo($errorsBlock.offset().top - 40);
                App.waitStop();
            };

            let data = {
                ACTION: 'SEND_SMS',
                VALUES: $form.serialize(),
                JS_SESSID: BX.bitrix_sessid()
            };
            Ajax.send('/ajax/ajax_card.php', data, $form, onSuccess, OnError);
        } else {
            App.waitStop();
        }
    };



    let activate = (e, sender) => {
        e.preventDefault();
        App.waitStart();

        let $form = $(sender);
        let $errorsBlock = $form.parent().find(".form-error");

        $errorsBlock.slideUp().empty();

        if (Form.checkFormFields($form) && Form.checkBXSessid($form)) {
            let onSuccess = (response) => {
                App.maskPhones();
                //$errorsBlock.html(response.message).slideDown();

                $(".addcard-step").slideUp();
                $("#addcard-step3").slideDown();

                App.waitStop();
            };

            let OnError = (response) => {
                App.maskPhones();
                $errorsBlock.html(response.message).slideDown();
                App.scrollTo($errorsBlock.offset().top - 40);
                App.waitStop();
            };

            let data = {
                ACTION: 'ACTIVATE',
                VALUES: $form.serialize(),
                JS_SESSID: BX.bitrix_sessid()
            };
            Ajax.send('/ajax/ajax_card.php', data, $form, onSuccess, OnError);
        } else {
            App.waitStop();
        }
    };

    let init = () => {
        console.log('init card');
        window.Card = Card;
    };

    return {
        init, sendSmsAndSaveUser, activate, goToStep
    };
})();

$(() => Card.init());