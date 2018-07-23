'use strict';
/* global f, App, Ajax */

require("mousewheel");
require("scrollbox-js");
require("scrollbox-css");

let Personal = (() => {

    let bindActions = () => {
        //$("#orders-list").scrollbox();
    };

    let setTab = (event, sender) => {
        let $sender = $(sender);
        let $wrap = $("#auth-tabs-wrap");

        App.waitStart();

        let data = {
            AJAX: 'Y',
            ACTION: 'get_auth_tab',
            bycard: $sender.attr("data-bycard")
        };

        let onSuccess = (response) => {
            $wrap.empty().html(response);
            $wrap.removeClass("loading");
            App.waitStop();
            App.maskPhones();
        };

        let onError = (responce) => {
            App.waitStop();
            App.maskPhones();
        };

        Ajax.html('', data, $sender, onSuccess, onError);
    };

    let init = () => {
        console.log('personal search');
        bindActions();

        window.Personal = Personal;
    };

    return {
        init, setTab
    };
})();

$(() => Personal.init());