'use strict';
/* global f */

require("jquery-placeholder");

export default (function () {
    let initViewport = function () {
        if (navigator.userAgent.match(/IEMobile\/10\.0/) || navigator.userAgent.match(/Windows Phone/)) {
            var msViewportStyle = document.createElement('style');
            msViewportStyle.appendChild(document.createTextNode('@-ms-viewport{width:auto!important}'));
            document.querySelector('head').appendChild(msViewportStyle);
        }
    };

    let initPlacholders = function () {
        if (f.isIE() !== 9)
            return;
        $('input, textarea').placeholder();
    };

    let init = function () {
        console.log('init IE');

        initViewport();
        initPlacholders();
    };

    return {
        init
    };
})();