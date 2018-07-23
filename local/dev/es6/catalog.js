'use strict';
/* global f, Ajax, App, History, Filter, url */

require('bootstrap-tab');
require('easyzoom');
require('history-js');
require("expose-loader?url!url-js");
require('slick-js');
require('slick-css');

let Catalog = (() => {
    let className = 'opened';

    let dropdown = (sender) => {
        let $sender = $(sender);
        let $list = $sender.next("ul");
        let eventName = f.getEventNameByAlias($sender.attr("data-event-alias"));

        $list.hasClass(className) ? dropdown_close(sender) : dropdown_open(sender);
    };

    let dropdown_open = (sender) => {
        let $sender = $(sender);
        let $list = $sender.next("ul");
        let eventName = f.getEventNameByAlias($sender.attr("data-event-alias"));

        $list.addClass(className);

        let onUnbind = () => dropdown_close(sender);
        App.bindClick($sender, $list, eventName, className, onUnbind);
    };

    let dropdown_close = (sender) => {
        let $sender = $(sender);
        let $list = $sender.next("ul");
        let eventName = f.getEventNameByAlias($sender.attr("data-event-alias"));

        $list.removeClass(className);
        $(document).unbind(eventName);
    };

    let sort = (sender, params, q = '') => {
        let $sender = $(sender);
        let data = {
            AJAX: 'Y',
            ACTION: 'get_section_list',
            PARAMS: params,
            q: q
        };
        data = setPagen(data, true);

        $sender.parent("ul").prev("div").find("span").html($sender.text()).parent().trigger("click");
        refresh(sender, data);
    };

    let onpage = (sender, params, q = '') => {
        let $sender = $(sender);

        let data = {
            AJAX: 'Y',
            ACTION: 'get_section_list',
            PARAMS: params,
            q: q
        };
        data = setPagen(data, true);

        $sender.parent("ul").prev("div").find("span").html($sender.text()).parent().trigger("click");
        refresh(sender, data);
    };

    let setPagen = (data, reset = false) => {
        let $container = $(".catalog-list-more").find("button");

        let pagen = 'PAGEN_' + $container.attr("data-navnum");
        let pagenomer = parseInt($container.attr("data-pagenomer"));

        data[pagen] = reset ? 1 : ++pagenomer;

        return data;
    };

    let showMore = function (sender) {
        let $sender = $(sender);
        let $list = $("#catalog-list");

        let data = {
            AJAX: 'Y',
            ACTION: 'get_section_list'
        };

        data = setPagen(data);
        let onSuccess = (response) => update(response);
        Ajax.html('', data, $sender.parent(), onSuccess);
    };

    let refresh = (sender, data = null, obj = "#catalog-section-list") => {
        let $sender = $(sender);

        App.waitStart();

        let onSucces = (response = null) => {
            if (response !== null) {
                $(obj).html(response);
            }
            App.waitStop();
            Filter.init();
            initEasyZoom();
            initAnalogs();
            setFilterUrl();
        };

        Ajax.html('', data, $sender, onSucces);
    };

    let update = (html, mode = 'append') => {
        let $catalog_section_list = $("#catalog-section-list");
        let $list = $catalog_section_list.find(".catalog-list");

        $catalog_section_list.find(".catalog-list-more").remove();
        $catalog_section_list.find(".catalog-list-pagination").remove();

        let $response = $(html);
        let $response_more = $response.filter(".catalog-list-more");
        let $response_pagination = $response.filter(".catalog-list-pagination");

        $response.find(".catalog-list").wrapInner('<div/>');
        $response.find('.catalog-list > .catalog-item').unwrap();
        $response.find(".catalog-list-count").remove();
        $response.find(".catalog-list-filter-labels").remove();


        if (mode === 'append') {
            $list.append($response.html());
        } else {
            $list.empty();
            $list.html($response.html());
        }
        $response_more.appendTo($list.parent());
        $response_pagination.appendTo($list.parent());

        initEasyZoom();
        initAnalogs();
    };

    let setActiveDetailTab = () => {
        let $catalog_detail = $("#catalog-detail");
        $catalog_detail.find(".js-detail-tabs").find("li:first-child > a").tab('show');
    };

    let getStoreAmount = (sender) => {
        let $sender = $(sender);

        let data = {
            AJAX: 'Y',
            ACTION: 'get_store_amount',
            XML_ID: $sender.attr('data-xml-id')
        };

        App.waitStart();

        let onSucces = (response) => {
            $(".stores").html(response);
            App.waitStop();
        };

        Ajax.html('', data, $sender, onSucces);
    };

    let flyDetailPicture = () => {
        let $wrap = $(".catalog-detail-info");
        let $flypicture = $('.catalog-detail-picture figure');

        if (!$wrap.length || !$flypicture.length) {
            return;
        }

        let init_top = 0,
                scrollTop = $(document).scrollTop(),
                offsetTop = $wrap.offset().top;

        let delta = scrollTop - offsetTop + init_top;
        let limit = offsetTop + $wrap.outerHeight() - $flypicture.outerHeight() - init_top - 75;

        if (delta > 0 && scrollTop <= limit) {
            $flypicture.css({
                top: delta
            });
        } else if (delta <= 0) {
            $flypicture.css({
                top: init_top
            });
        } else {
            $flypicture.css({
                top: limit - offsetTop
            });
        }
    };

    let getQuery = () => {
        let oldUrl = url.parse(window.location.href);
        return url.get(oldUrl.query, {array: true});
    };

    let setFilterUrl = () => {
        let oldUrl = url.parse(window.location.href);
        let scheme = oldUrl.scheme;
        let host = oldUrl.host;
        let path = oldUrl.path;
        let query = url.get(oldUrl.query, {array: true});

        let filterUrl = $("#catalog").find(".catalog-section").attr("data-filter-url");
        let newUrl;

        if (f.isEmpty(filterUrl)) {
            newUrl = url.build({scheme, host, path, get: ''});
        } else {
            newUrl = url.build({scheme, host, path, get: JSON.parse(decodeURIComponent(filterUrl))});
        }

        //History.pushState({state: newUrl}, "State 1", newUrl);
        window.history.pushState({href: newUrl}, document.title, newUrl);
    };

    let bindActions = () => {
        let $analogs_list = $("#analogs-list");
        let $analogs_list_wrap = $analogs_list.parent();

        $(window).bind("load scroll resize orientationchange", function () {
            flyDetailPicture();

            if ($(window).width() > 992) {
                $analogs_list_wrap.addClass("moved");
            } else {
                $analogs_list_wrap.removeClass("moved");
            }
        });

        History.Adapter.bind(window, 'statechange', function () { // Note: We are using statechange instead of popstate
            console.log('statechange');
            var State = History.getState(); // Note: We are using History.getState() instead of event.state
            console.log(State);
            window.location.href = State.url;
            return;
        });
    };

    let initEasyZoom = () => {
        $('.js-easyzoom').easyZoom({
            loadingNotice: 'Загрузка изображения...',
            errorNotice: 'Не удалось загрузить изображение',
            linkAttribute: 'data-detail'
        });
    };

    let fireClarify = () => {
        let $catalog_detail = $("#catalog-detail");

        $catalog_detail.find(".js-detail-tabs").find('#tab-stores > a').tab('show');
        $('html, body').animate({scrollTop: $('#tab-stores').offset().top}, 450, function () {
            $('.catalog-detail-button').trigger('click');
        });
    };

    let onloadHandler = () => {
        //setAnalogSliderIsLoaded();
    };

    let setAnalogSliderIsLoaded = () => {
        let $analogs_list = $("#analogs-list");
        let $analogs_list_wrap = $analogs_list.parent();
        let $analogs_slider = $('#analogs-list-slider');

        let min_height_delta = 500; //минимальная разница в высоте между умным фильтром каталогом, чтобы пренести аналоги под карточки товаров

        let $catalog = $("#catalog");
        let $smartfilter = $catalog.find("#smartfilter");
        let $list = $catalog.find("#catalog-section-list");

        let height_delta = $smartfilter.outerHeight() - $list.outerHeight();

        if (height_delta >= min_height_delta && $(window).width() > 991) {
            $analogs_list_wrap.appendTo($list.parent()).addClass("moved");
            initAnalogsSlider($analogs_slider, 2, true);
        } else {
            $analogs_list_wrap.removeClass("moved");
            initAnalogsSlider($analogs_slider, 1, true);
        }

        $analogs_list.removeClass("loading").addClass("loaded");
        $analogs_list.find(".slider-catalog-list-spinner").remove();
    };

    let initAnalogsSlider = ($analogs_slider, responsive_variant = 1, reinit = false) => {
        console.log('initAnalogsSlider');

        let responsive;

        if (responsive_variant === 1) {
            responsive = [
                {
                    breakpoint: 1200,
                    settings: {
                        slidesToShow: 3
                    }
                },
                {
                    breakpoint: 768,
                    settings: {
                        slidesToShow: 2
                    }
                },
                {
                    breakpoint: 575,
                    settings: {
                        slidesToShow: 1
                    }
                }
            ];
        } else {
            responsive = [
                {
                    breakpoint: 1400,
                    settings: {
                        slidesToShow: 3
                    }
                },
                {
                    breakpoint: 1200,
                    settings: {
                        slidesToShow: 2
                    }
                },
                {
                    breakpoint: 575,
                    settings: {
                        slidesToShow: 1
                    }
                }
            ];
        }

        if (!!reinit) {
            $analogs_slider.slick('unslick');
        }

        $analogs_slider.slick({
            dots: false,
            dotsClass: 'slick-dots circles',
            arrows: true,
            //appendArrows: $(".js-analogs-arrows"),
            prevArrow: '<button type="button" class="slick-prev"><i class="ion-ios-arrow-left"></i></button>',
            nextArrow: '<button type="button" class="slick-next"><i class="ion-ios-arrow-right"></i></button>',
            infinite: false,
            speed: 200,
            fade: false,
            adaptiveHeight: false,
            slidesToShow: 4,
            slidesToScroll: 1,
            draggable: false,
            responsive
        });

    };

    let getDetailNotes = function (adding_count = 0) {
        let $block = $("#detail-notes");

        if (!!$block && $block.length) {
            let data = {
                AJAX: 'Y',
                ACTION: 'get_detail_notes',
                ADD_COUNT: adding_count
            };

            let onSuccess = (response) => {
                $block.empty().html($(response).find("#detail-notes").html());
            };

            Ajax.html('', data, $block, onSuccess);
    }
    };

    let initAnalogs = () => {
        console.log('initAnalogs');
        let $analogs_slider = $('#analogs-list-slider');

        if (!!$analogs_slider) {
            initAnalogsSlider($analogs_slider, 1);
            setAnalogSliderIsLoaded();
        }
    };

    let init = () => {
        console.log('init catalog');
        bindActions();
        setActiveDetailTab();
        flyDetailPicture();

        $('.btn-clarify').on('click', function (e) {
            e.preventDefault();
            fireClarify();
        });

        if (window.location.hash) {
            let hash = window.location.hash.substring(1);

            if (hash === 'clarify') {
                fireClarify();
            }
        }

        initEasyZoom();
        initAnalogs();

        window.Catalog = Catalog;
    };

    return {
        init, dropdown, onpage, sort, showMore, refresh,
        getStoreAmount, setPagen, onloadHandler, getDetailNotes
    };
})();

$(() => Catalog.init());

$(window).load(() => {
    Catalog.onloadHandler();
});