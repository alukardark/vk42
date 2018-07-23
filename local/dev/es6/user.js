'use strict';
/* global f, App, sender, Ajax */

let User = (() => {
    let eventName = f.getEventNameByAlias("header_menu");
    let className = 'opened';


    let toggleHeaderMenu = (sender) => {
        let $sender = $(sender);
        let $headerMenu = $("#nav-auth-menu");

        $headerMenu.hasClass(className) ? closeHeaderMenu(sender) : openHeaderMenu(sender);
    };

    let openHeaderMenu = (sender) => {
        let $sender = $(sender);
        let $headerMenu = $("#nav-auth-menu");

        $headerMenu.addClass(className);

        let onUnbind = () => closeHeaderMenu(sender);
        App.bindClick($sender, $headerMenu, eventName, className, onUnbind);
    };

    let closeHeaderMenu = (sender) => {
        let $sender = $(sender);
        let $headerMenu = $("#nav-auth-menu");

        $headerMenu.removeClass(className);
        $(document).unbind(eventName);
    };

    let checkUnique = (value, type, onSuccess) => {
        let data = {
            'ACTION': 'CHECK_UNIQUE',
            'TYPE': type, //phone || email
            'VALUE': value
        };

        Ajax.send("/ajax/ajax_common.php", data, $("body"), onSuccess);
    };

    let bindActions = () => {

    };

    let init = () => {
        console.log('init search');
        //bindActions();

        window.User = User;
    };


    return {
        init, toggleHeaderMenu, checkUnique
    };
})();

$(() => User.init());