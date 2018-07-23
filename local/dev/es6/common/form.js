'use strict';
/* global f, Ajax */

export default (function () {
    let bindInputsFocus = function () {
        $('input, textarea').focus(function () {
            $(this).parent().removeClass('error');
        });
    };

    let submit = function (self) {
        let $self = $(self);
        let $form = $self.parents("form");
        let $div = $form.parents(".popup-form");
        let $error = $div.find(".popup-form-error");

        $error.hide(400);
        let bErrors = check($form);

        if (!bErrors) {
            let data = {
                action: 'submit',
                form_id: $form.attr("data-form-id"),
                values: $form.serialize()
            };

            let onSuccess = function (response) {
                if (response.result) {
                    $form.remove();
                    $div.append('<div class="popup-form-success row middle center">' + response.info + '</div>');

                    setTimeout(function () {
                        //parent.$.fancybox.close();
                    }, 7000);

                } else {
                    $error.empty().html(response.info).fadeIn(400);
                }
            };

            Ajax.send("/ajax/ajax_form.php", data, $("#wait"), onSuccess);
        }
    };

    let subscribe = function (self) {
        let $self = $(self);
        let $subscribe = $self.parents("#subscribe");
        let $content = $subscribe.find(".subscribe-form-content-wrap");
        //let $error = $div.find(".popup-form-error");

        let $input = $subscribe.find('input[type="email"]');

        //$error.hide(400);
        let bErrors = check($subscribe);

        if (!bErrors) {
            let data = {
                action: 'subscribe',
                email: $input.val()
            };

            let onSuccess = function (response) {
                if (response.result) {
                    $content.empty().append('<div class="subscribe-form-success">' + response.info + '</div>');
                } else {
                    //$error.empty().html(response.info).fadeIn(400);
                }
            };

            Ajax.send("/ajax/ajax_form.php", data, $("#wait"), onSuccess);
        }
    };

    let check = function ($form) {
        //console.log($form);
        let bErrors = false;
        let arInputs = $form.find("input, textarea").filter(':visible');
        console.log(arInputs);

        arInputs.each(function (key, input) {
            let $input = $(input);
            let bReq = $input.attr('data-required');
            let sType = $input.attr('data-type');
            let sValue = $.trim($input.val());

            if (bReq === 'Y' && f.isEmpty(sValue)) {
                bErrors = true;
                $input.parent().addClass("error");
            }

            if (sType === 'EMAIL') {
                if (!f.isEmail(sValue)) {
                    bErrors = true;
                    $input.parent().addClass("error");
                }
            }

            if (sType === 'PHONE') {
                if ($input.hasClass("error")) {
                    bErrors = true;
                    $input.parent().addClass("error");
                }
            }
        });

        return bErrors;
    };

    let init = function () {
        console.log('init form');
        bindInputsFocus();
    };

    return {
        init: init,
        submit: submit,
        subscribe: subscribe
    };
})();

