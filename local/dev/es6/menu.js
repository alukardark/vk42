'use strict';
/* global f, App, Ajax, CITIES */

let Menu = (() => {
    let className = 'opened';
    let eventNameNavMobile = f.getEventNameByAlias("nav_mobile");
    let eventNameNavCities = f.getEventNameByAlias("nav_cities");
    let eventNameNavCitiesQuestion = f.getEventNameByAlias("nav_cities_question");
    let eventNameMenuSwipe = "touchstart.nav_mobile_swipe touchmove.nav_mobile_swipe touchend.nav_mobile_swipe";
    let eventNameMenuItemClick = "click.root_item";

    let navMobileToggle = (sender) => {
        let $navMobile = $("#nav-mobile");
        $navMobile.hasClass(className) ? navMobileClose(sender) : navMobileOpen(sender);
    };

    let navMobileOpen = (sender) => {
        let $sender = $(sender);
        let $navMobile = $("#nav-mobile");

        navMobileInnerClose();
        $navMobile.addClass(className);
        $("#body").addClass("inactive");

        let onUnbind = () => navMobileClose(sender);
        App.bindClick($sender, $navMobile, eventNameNavMobile, className, onUnbind);
        bindMobileMenu();
        bindMobileMenuItem();
    };

    let navMobileClose = (sender) => {
        let $navMobile = $("#nav-mobile");

        $navMobile.removeClass(className);
        $("#body").removeClass("inactive");
        unBindMobileMenu();
        unBindMobileMenuItem();
        $(document).unbind(eventNameNavMobile);
    };

    let navCitiesToggle = (sender) => {
        let $navCities = $("#nav-cities");
        $navCities.hasClass(className) ? navCitiesClose(sender) : navCitiesOpen(sender);
    };

    let navCitiesOpen = (sender) => {
        let $sender = $(sender);
        let $navCities = $("#nav-cities");

        navCitiesQuestionClose();
        $navCities.addClass(className);

        let onUnbind = () => navCitiesClose(sender);
        App.bindClick($sender, $navCities, eventNameNavCities, className, onUnbind);
    };

    let navCitiesClose = (sender) => {
        let $navCities = $("#nav-cities");
        $navCities.removeClass(className);
        $(document).unbind(eventNameNavCities);

        let cityKey = $('.nav-cities-current').attr("data-city-key");

        if (!!CITIES[cityKey]) {
            let cityName = CITIES[cityKey].toString().toLowerCase();
            $('.contacts-cities-buttons > button[data-city="' + cityName + '"]').trigger('click');
        }
    };

    let navCitiesQuestionOpen = (sender) => {
        let $sender = $(sender);
        let $navCitiesQuestion = $("#nav-cities-question");

        $navCitiesQuestion.addClass(className);

        let onUnbind = () => navCitiesQuestionClose(sender);
        App.bindClick($sender, $navCitiesQuestion, eventNameNavCitiesQuestion, className, onUnbind);
    };

    let navCitiesQuestionClose = (sender) => {
        let $navCitiesQuestion = $("#nav-cities-question");

        $navCitiesQuestion.removeClass(className);
        $(document).unbind(eventNameNavCitiesQuestion);
    };

    let setCity = (sender = null, isCityAlias = false) => {
        let cityKey, cityName;
        let $navCities = $("#nav-cities");
        let $navCitiesQuestion = $("#nav-cities-question");
        let $currentCity = $navCities.find(".nav-cities-current span");

        if (sender !== null) {
            let $sender = isCityAlias ? $('.nav-cities-list > li[data-city-key="' + sender + '"]') : $(sender);
            cityKey = isCityAlias ? sender : $sender.attr("data-city-key");

            $currentCity.text($sender.text());
            $currentCity.attr("data-city-key", cityKey);
        } else {
            cityKey = $currentCity.attr("data-city-key");
        }

        $('.nav-cities-current').attr("data-city-key", cityKey);

        let action = 'set_city';

        navCitiesClose();

        let onSuccess = (responce) => {
            let $block = $("#detail-notes");
            if (!!$block && $block.length) {
                Catalog.getDetailNotes();
            }
        };

        Ajax.send('/ajax/ajax_menu.php', {action, cityKey}, $navCitiesQuestion, onSuccess);
    };

    let setCityPromise = (sender = null, isCityAlias = false) => {
        return new Promise(function (resolve, reject) {
            let cityKey, cityName;
            let $navCities = $("#nav-cities");
            let $navCitiesQuestion = $("#nav-cities-question");
            let $currentCity = $navCities.find(".nav-cities-current span");

            if (sender !== null) {
                let $sender = isCityAlias ? $('.nav-cities-list > li[data-city-key="' + sender + '"]').first() : $(sender);
                cityKey = isCityAlias ? sender : $sender.attr("data-city-key");

                $currentCity.text($sender.text());
                $currentCity.attr("data-city-key", cityKey);
            } else {
                cityKey = $currentCity.attr("data-city-key");
            }

            $('.nav-cities-current').attr("data-city-key", cityKey);

            let action = 'set_city';

            navCitiesClose();

            let onSuccess = () => {
                resolve();
            };

            Ajax.send('/ajax/ajax_menu.php', {action, cityKey}, $navCitiesQuestion, onSuccess);
        });




    };


    let navMobileInnerOpen = () => {
        let $navMobileContent = $("#nav-mobile-content");
        let $navMobileInner = $("#nav-mobile-inner");

        $navMobileContent.removeClass("opened");
        $navMobileInner.addClass("opened");
        App.scrollTo(0, 200);
    };

    let navMobileInnerClose = () => {
        let $navMobileContent = $("#nav-mobile-content");
        let $navMobileInner = $("#nav-mobile-inner");

        $navMobileContent.addClass("opened");
        $navMobileInner.removeClass("opened");
    };

    let bindMobileMenu = () => {
        let $navMobile = $("#nav-mobile");

        let startX = 0, startY = 0, deltaX = 0, deltaY = 0, direction = "none", isMultiTouch = false;
        let delta = 50; //pixels distantion for detect direction

        $navMobile.bind(eventNameMenuSwipe, function (event) {
            let e = event.originalEvent;

            if (e.type === 'touchstart') {
                isMultiTouch = false;

                if (e.touches.length === 1) {
                    startX = e.touches[0].pageX;
                    startY = e.touches[0].pageY;
                }
            }

            if (e.type === 'touchmove') {
                if (e.touches.length !== 1) {
                    isMultiTouch = true;
                }
            }

            if (e.type === 'touchend') {
                if (isMultiTouch)
                    return;

                deltaX = startX - e.changedTouches[0].pageX;
                deltaY = startY - e.changedTouches[0].pageY;

                if (deltaX < -delta && Math.abs(deltaX) > Math.abs(deltaY))
                    direction = "right";
                else if (deltaX > delta && Math.abs(deltaX) > Math.abs(deltaY))
                    direction = "left";
                else if (deltaY < -delta && Math.abs(deltaY) > Math.abs(deltaX))
                    direction = "down";
                else if (deltaY > delta && Math.abs(deltaY) > Math.abs(deltaX))
                    direction = "up";
                else
                    direction = "none";

                if (direction === "left") {
                    $(".nav-mobile-close").trigger("click");
                }
            }
        });
    };

    let unBindMobileMenu = () => {
        let $navMobile = $("#nav-mobile");
        $navMobile.unbind(eventNameMenuSwipe);
    };

    let fillMobileMenu = () => {
        $("#search-panel-m").html($("#search-panel").html());
        $("#nav-personal-m").html($("#nav-personal").html());
        //$("#nav-sections-m").html($("#nav-sections").html());
        //$("#nav-bottom-m").html($("#nav-bottom").html());
        $("#socnets-m").html($("#socnets").html());
    };

    let bindMobileMenuItem = () => {
        let $navMobile = $("#nav-mobile");
        let $navMobileInner = $("#nav-mobile-inner");

        $navMobile.on(eventNameMenuItemClick, "[data-menu-root-item]", function (e) {
            e.preventDefault();
            let $sender = $(this);
            let $content = $sender.find("[data-menu-children-list]").first();

            $navMobileInner.find(".nav-mobile-inner-title span").text($sender.attr("title"));
            $navMobileInner.find(".nav-mobile-inner-content").html($content.html());
            navMobileInnerOpen();
        });
    };

    let unBindMobileMenuItem = () => {
        let $navMobile = $("#nav-mobile");
        $navMobile.off(eventNameMenuItemClick, "[data-menu-root-item]");
    };

    let init = () => {
        console.log('init menu');
        fillMobileMenu();

        $('.nav-top-list a.drop').on('click', function (e) {
            e.preventDefault();
            $(this).toggleClass('opened');
        });

        $(document).on('click', function (e) {
            var containers = $('.header');
            if (containers.is(e.target) || containers.has(e.target).length) {
                return;
            }
            $('.nav-top-list a.drop.opened').removeClass('opened');
        });

        window.Menu = Menu;
    };


    return {
        init, navMobileToggle, navMobileClose, navMobileInnerClose, navCitiesToggle,
        navCitiesOpen, setCity, navCitiesQuestionClose, setCityPromise
    };
})();

$(() => Menu.init());