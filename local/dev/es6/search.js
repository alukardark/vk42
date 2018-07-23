'use strict';
/* global f, App */

let Search = (() => {
    let eventName = f.getEventNameByAlias("search_panel");
    let className = 'opened';

    let panel_open = (sender) => {
        let $sender = $(sender);
        let $panel = $("#search-panel");

        $panel.addClass(className);
        setTimeout(() => $panel.find('.search-panel-input').focus(), 100);
        $("#body").addClass("inactive");

        let onUnbind = () => panel_close();
        App.bindClick($sender, $panel, eventName, className, onUnbind);
    };

    let panel_close = (sender) => {
        let $panel = $("#search-panel");

        $panel.removeClass(className);
        $panel.find('.search-panel-input').blur();

        $("#body").removeClass("inactive");
        $(document).unbind(eventName);
    };

    let submit = (sender) => {
        console.log('submit');

        $("#search-panel").find("form").submit();
    };

    let init = () => {
        console.log('init search');

        window.Search = Search;
    };


    return {
        init, panel_open, panel_close, submit
    };
})();

$(() => Search.init());