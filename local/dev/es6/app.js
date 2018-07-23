'use strict';
/* global f, VK, map_data, yaCounter12153865 */

import {default as IE} from './common/ie';
import {default as Ajax} from './common/ajax';

require("expose-loader?f!./common/lib.js");
require("expose-loader?map_data!./common/map_data.js");
require("./common/yml.js");
//require("jquery.maskedinput");
//require("jquery-mask-plugin");
require("inputmask");
require("fancybox-js");
require("fancybox-css");
//require("manup-js");

let App = (() => {
    let reachGoal = (goal = null) => {
        try {
            if (!!goal) {
                yaCounter12153865.reachGoal(goal);
            }
        } catch (e) {
    }
    };

    let scrollTo = (scrollTop = 0, speed = 400) => {
        $('html, body').animate({scrollTop}, speed);
    };

    let toggleUpButton = () => {
        let $btn = $('#up-button');
        let $help = $('.help');

        if ($(document).scrollTop() > 500) {
            $btn.stop(true, true).fadeIn(400);
            $help.addClass("shifted");
        } else {
            $btn.stop(true, true).fadeOut(400);
            $help.removeClass("shifted");
        }
    };

    let bindUpButton = () => {
        $(window).bind("scroll resize orientationchange", function () {
            toggleUpButton();
        });
    };

    let openBaloonAsync = (xmlId, city) => {
        console.log('waiting...' + xmlId + city);

        if (typeof openBaloonMapByXmlId !== "undefined") {
            f.delay(750).then(() => {
                openBaloonMapByXmlId(xmlId, city);
            });
        } else {
            f.delay(25).then(() => {
                openBaloonAsync(xmlId, city);
            });
        }
    };

    let readyGo = function (initialCity) {
        console.log('readyGo exec' + initialCity);

        yml.readyGo(function () {
            map_data.initStoresMap(initialCity, 'map');
        }, true, null, 'map');
    };

    let bindFancyBox = () => {
        $.fancybox.defaults.hash = false;

        $('[data-fancybox], .zoom').fancybox({

        });

        let $instance = $('[data-map-popup]').fancybox({
            touch: false,
            loop: false,
            gutter: 0,
            infobar: false,
            buttons: false,
            slideShow: false,
            fullScreen: true,
            thumbs: false,
            margin: [0, 0],
            onActivate: () => {
            },
            onComplete: (instance) => {
                console.log('fancybox onComplete');

                let xmlId = $instance.attr("data-store-xml_id");
                let city = $instance.attr("data-store-city");

                openBaloonAsync(xmlId, city);
            },
            onInit: (instance) => {
                console.log('fancybox onInit');

                let xmlId = $instance.attr("data-store-xml_id");
                let city = $instance.attr("data-store-city");

                readyGo(city.toString().toLowerCase());
            },
            afterLoad: () => {
            },
            beforeLoad: () => {
            },
            afterClose: () => {
                console.log('fancybox afterClose');

                $("#map").empty();
                delete window.openBaloonMapByXmlId;
            }
        });
    };

    let bindClick = ($sender, $container, eventName, className, onUnbind = false) => {
        $(document).bind(eventName, (event) => {
            let e = event.originalEvent;
            let target = $(e.target);
            let targetClass = target[0].className;

            if ($container.is(target) || $container.has(target).length || $sender.is(target) || $sender.has(target).length ||
                    targetClass === "js-stop-propogation" || targetClass === "autocomplete-group" || targetClass === "autocomplete-suggestion autocomplete-selected") {
                e.stopPropagation();
            } else {
                if (e.type === "click" || e.type === "touchend" || (e.type === "keyup" && e.keyCode === 27)) {
                    unBindClick($container, eventName, className, onUnbind);
                }
            }
        });
    };

    let waitStart = ($container = $("#wait"), fade = true) => {
        $container.addClass("loading");
        if (fade)
            $container.fadeIn(0);
    };

    let waitStop = ($container = $("#wait"), fade = true) => {
        $container.removeClass("loading");
        if (fade)
            $container.fadeOut(0);
    };

    let unBindClick = ($container, eventName, className, onUnbind = false) => {
        if (onUnbind !== false) {
            onUnbind();
        } else {
            $container.removeClass(className);
        }

        $(document).unbind(eventName);
    };

    let isTouchDevice = () => {
        try {
            document.createEvent("TouchEvent");
            return true;
        } catch (e) {
            return false;
        }
    };

    let onloadHandler = () => {
        if ($("#notification").length > 0) {
            setTimeout(() => {
                showNote();
            }, 400);
        }
    };

    let selectAll = (event, sender) => {
        //event.preventDefault();
        let $sender = $(sender);

        //if (!$sender.is(":focus")) {
        $(sender).select();
        //}
    };

    let hideNoteTipTimer;
    let classNameOpened = 'opened';

    let showNoteTip = (sender) => {
        let $sender = $(sender);
        let target = $sender.attr("data-target");
        let $target = $(target);
        let eventName = f.getEventNameByAlias("notetip" + target);

        if ($target.hasClass(classNameOpened)) {
            hideNoteTip(sender);
            return;
        }

        $target.addClass(classNameOpened);

        let onUnbind = () => hideNoteTip(sender);
        App.bindClick($sender, $target, eventName, classNameOpened, onUnbind);
    };

    let hideNoteTip = (sender) => {
        let $sender = $(sender);
        let target = $sender.attr("data-target");
        let $target = $(target);
        let eventName = f.getEventNameByAlias("notetip" + target);

        if (!$target.hasClass(classNameOpened)) {
            return;
        }

        hideNoteTipTimer = setTimeout(() => {
            $target.removeClass(classNameOpened);
            $(document).unbind(eventName);
        }, 100);
    };

    let telephone = (sender) => {
        window.location.href = $(sender).attr('data-tel');
    };

    let initModules = () => {
        //init common modules
        IE.init();
        Ajax.init();

        window.App = App;
        window.Ajax = Ajax;
    };

    let maskPhones = () => {
        //$('.phone, [data-type="PHONE"], [data-type="USER_PHONE"], [data-property-code="PHONE"], [type="tel"]').mask("+7 (999) 999-99-99");
        //$('.phone, [data-type="PHONE"], [data-type="USER_PHONE"], [data-property-code="PHONE"], [type="tel"]').mask("+7 (000) 000-00-00");
        $('.phone, [data-type="PHONE"], [data-type="USER_PHONE"], [data-property-code="PHONE"], [type="tel"]').inputmask({
            mask: "+7 (999) 999-99-99",
            showMaskOnHover: false,
            clearIncomplete: true
        });
    };

    let backLikeBrowserHandler = () => {
        let $button = $('.backlink-likebrowser');
        $button.on('click', (event) => {
            event.preventDefault();
            window.history.back();
        });
    };

    let init = () => {
        console.log('init app');

        maskPhones();
        initModules();
        bindUpButton();
        bindFancyBox();
        toggleUpButton();
        backLikeBrowserHandler();
        //disableHover();

        $('#up-button').on('click', function () {
            scrollTo();
        });
    };

    let alertTimeout;
    let showAlert = (message) => {
        $("#body").addClass("inactive");
        $("#alert").find(".alert-content-text").html(message);
        $("#alert").addClass("opened");

        clearTimeout(alertTimeout);
        alertTimeout = setTimeout(() => {
            //hideAlert();
        }, 30000);
    };

    let hideAlert = () => {
        clearTimeout(alertTimeout);
        $("#alert").removeClass("opened");
        $("#body").removeClass("inactive");
    };

    let showNote = () => {
        $("#notification").addClass("opened");
    };

    let hideNote = (sender) => {
        $("#notification").removeClass("opened");
        Ajax.send("/ajax/ajax_common.php", {'ACTION': 'HIDE_NOTE'}, $(sender));
    };

    let showHelp = () => {
        $("#help").addClass("opened");
    };

    let hideHelp = (sender) => {
        $("#help").removeClass("opened");
    };

    let hideAttention = (sender) => {
        $("#attention").removeClass("opened");
        Ajax.send("/ajax/ajax_common.php", {'ACTION': 'HIDE_ATTENTION'}, $(sender));
    };

    let setBackUrl = (event, sender) => {
        event.preventDefault();

        let $sender = $(sender);
        let backUrl = window.location.pathname;

        if (!!$sender.attr("data-back-url")) {
            backUrl = $sender.attr("data-back-url");
        }

        let onSuccess = () => {
            document.location.href = event.target.pathname;
        };

        Ajax.send("/ajax/ajax_common.php", {'ACTION': 'SET_BACK_URL', 'BACK_URL': backUrl}, $sender, onSuccess);
    };

    let disableHoverTimer, $body = $("#body"), className = 'disable-hover';

    let disableHover = () => {
        $(window).bind("scroll resize orientationchange", function () {
            clearTimeout(disableHoverTimer);

            if (!$body.hasClass(className)) {
                $body.addClass(className);
            }

            disableHoverTimer = setTimeout(() => $body.removeClass(className), 250);
        });
    };

    return {
        init, scrollTo, bindFancyBox, bindClick, telephone, onloadHandler,
        waitStart, waitStop, showAlert, hideAlert, hideNote, maskPhones,
        showHelp, hideHelp, showNoteTip, hideNoteTip, hideAttention, selectAll,
        setBackUrl, reachGoal
    };
})();


$(() => App.init());

$(window).load(() => {
    App.onloadHandler();
});