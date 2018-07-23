'use strict';

export function  delay(ms) {
    return  new Promise((resolve) => setTimeout(resolve, ms));
}

export function getEventNameByAlias(eventName, bTouch = false, bClick = true, bKey = true) {
    let eventNameReturn = '';

    if (bClick === true)
        eventNameReturn += 'click.' + eventName + ' ';
    if (bKey === true)
        eventNameReturn += 'keyup.' + eventName + ' ';
    if (bTouch === true)
        eventNameReturn += 'touchend.' + eventName + ' ';

    return $.trim(eventNameReturn);
}

export function wordPlural(value, texts) {
    if (value % 10 === 1 && (value < 10 || value > 20))
        return texts[0];
    if ((value % 10 === 2 || value % 10 === 3 || value % 10 === 4) && (value < 10 || value > 20))
        return texts[1];
    return texts[2];
}

export function isEmpty(value) {
    value = $.trim(value);
    return value === undefined || value === '' || value === null;
}

export function isExist($el) {
    return $el.length > 0;
}

export function isEmail(val) {
    let pattern = new RegExp(/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i);
    return pattern.test(val);
}

export function animateRowRemove($el) {
    $el.children('td, th').animate({padding: 0}).wrapInner('<div />')
            .children().slideUp(200, function () {
        $el.closest('tr').remove();
    });
}

export function isMobile() {
    let ua = navigator.userAgent;
    if (ua.match(/Android/i) || ua.match(/webOS/i) || ua.match(/iPhone/i) || ua.match(/iPad/i) ||
            ua.match(/iPod/i) || ua.match(/BlackBerry/i) || ua.match(/Windows Phone/i))
        return true;
    return false;
}

export function isIE() {
    let ua = window.navigator.userAgent;
    let msie = ua.indexOf('MSIE ');
    if (msie > 0) {
        return parseInt(ua.substring(msie + 5, ua.indexOf('.', msie)), 10);
    }

    let trident = ua.indexOf('Trident/');
    if (trident > 0) {
        let rv = ua.indexOf('rv:');
        return parseInt(ua.substring(rv + 3, ua.indexOf('.', rv)), 10);
    }

    let edge = ua.indexOf('Edge/');
    if (edge > 0) {
        return parseInt(ua.substring(edge + 5, ua.indexOf('.', edge)), 10);
    }

    return false;
}

export function setCookie(name, value, days) {
    let expires = "";
    if (days) {
        let date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toGMTString();
    }
    document.cookie = encodeURIComponent(name) + "=" + encodeURIComponent(value) + expires + "; path=/";
}

export function getCookie(name) {
    let nameEQ = encodeURIComponent(name) + "=";
    let ca = document.cookie.split(';');
    for (let i = 0; i < ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) === ' ')
            c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) === 0)
            return decodeURIComponent(c.substring(nameEQ.length, c.length));
    }
}

export function makeId()
{
    return Math.random().toString(36).substring(7);
}

export function fixMouseJump() {
    let we;
    let delta = 0;

    $('body').on("DOMMouseScroll mousewheel wheel", function (e) {
        //console.log(e);
        var event_phases = e.eventPhase !== undefined ? e.eventPhase : 1;
        if (e.originalEvent)
            e = e.originalEvent; //jquery fix

        try {
            new window.WheelEvent('wheel');
            we = 'wheel';
        } catch (e) {
        }

        if (!we && document.onmousewheel !== undefined)
            we = 'mousewheel';
        if (!we)
            we = 'DOMMouseScroll';

        if (e.detail)
            delta = -e.detail; //Opera & IE
        else if (we === 'mousewheel')
            delta = e.wheelDelta; //WebKits
        else if (we === 'DOMMouseScroll')
            delta = -e.detail; //Old FireFox
        else if (we === 'wheel')
            delta = (Math.abs(e.deltaX) > Math.abs(e.deltaY)) ? -e.deltaX : -e.deltaY; //New FireFox

        if (window.mozInnerScreenX !== undefined) {
            //delta = delta * event_phases * 2;
        }
        if (delta)
        {
            //console.log(window.pageYOffset + '-' + delta);
            e.preventDefault();
            var currentScrollPosition = window.pageYOffset;
            window.scrollTo(0, currentScrollPosition - delta);
        }
    });
}

export function getPathFromUrl(url) {
    return url.split("?")[0];
}

export function stripQueryStringAndHashFromPath(url) {
    return url.split("?")[0].split("#")[0];
}