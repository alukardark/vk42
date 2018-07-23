'use strict';
/* global f, Ajax, App, History */

require('slick-js');
require('slick-css');
require('bootstrap-collapse');
require('history-js');
require("expose-loader?url!url-js");

let Actions = (() => {
    let initSliders = () => {
        let $analogs_slider = $('#catalog-list-slider');

        if (!!$analogs_slider) {
            $analogs_slider.slick({
                dots: false,
                dotsClass: 'slick-dots circles',
                arrows: true,
                prevArrow: '<button type="button" class="slick-prev"><i class="ion-ios-arrow-left"></i></button>',
                nextArrow: '<button type="button" class="slick-next"><i class="ion-ios-arrow-right"></i></button>',
                infinite: false,
                speed: 200,
                fade: false,
                adaptiveHeight: false,
                slidesToShow: 3,
                slidesToScroll: 1,
                draggable: false,
                responsive: [
                    {
                        breakpoint: 1200,
                        settings: {
                            slidesToShow: 2
                        }
                    },
                    {
                        breakpoint: 576,
                        settings: {
                            slidesToShow: 1
                        }
                    }
                ]
            });
        }
    };

    let showMore = function (self) {
        let $self = $(self);
        let $container = $(self).parents(".actions-wrap");
        let $more = $container.find(".actions-list-more");

        let pagen = 'PAGEN_' + $self.attr("data-navnum");
        let pagenomer = parseInt($self.attr("data-pagenomer"));

        let data = {
            AJAX: 'Y',
            ACTION: 'get_list',
            SHOWMORE: 'Y'
        };
        data[pagen] = ++pagenomer;

        let onSuccess = (response) => update(response, 'append', $container);
        Ajax.html('', data, $more, onSuccess);
    };

    let update = (html, mode = 'append', $container) => {
        let $list = $container.find(".actions-list");

        $container.find(".actions-list-more").remove();
        $container.find(".actions-list-pagination").remove();
        $container.find("#tags-url").remove();

        let $response = $(html);
        let $response_more = $response.filter(".actions-list-more");
        let $response_pagination = $response.filter(".actions-list-pagination");
        let $response_wait = $response.filter(".actions-wait");
        let $response_tags_url = $response.filter("#tags-url");

        $response.find(".actions-list").wrapInner('<div/>');
        $response.find('.actions-list > .actions-list-item').unwrap();

        if (mode === 'append') {
            $response.find(".article-list-title").remove();
            $response.find(".news-list-title").remove();
            $list.append($response.html());
        } else {
            $list.empty();
            $list.html($response.html());
        }
        $response_more.appendTo($list.parent());
        $response_pagination.appendTo($list.parent());
        $response_wait.appendTo($list.parent());
        $response_tags_url.appendTo($list.parent());

    };

    let doMenu = (sender, e) => {
        e.preventDefault();

        console.log('click');
        let $container = $(".actions-wrap");
        let $list = $(".actions-list");
        let $menu = $("#articles-menu");

        let $sender = $(sender);
        let link = $sender.attr("href");
        let $item = $sender.parent();
        let $node = $item.next(".node");

        let isParent = $sender.attr("data-menu-item") === "parent";
        let isTitle = $sender.attr("data-menu-item") === "title";
        let isItemSelected = $item.hasClass("selected");
        let isNodeIn = $node.hasClass("in");

        if (isItemSelected === true) {
            return;
        }

        if (isParent === true && isItemSelected === true && isNodeIn === true) {
            return;
        }

        $menu.find(".item").removeClass("selected");
        $item.addClass("selected");

        App.waitStart($list, false);

        let data = {
            AJAX: 'Y',
            ACTION: 'get_list'
        };
        let onSuccess = (response) => {
            //History.pushState({state: link}, document.title, link);
            window.history.pushState({href: link}, document.title, link);

            update(response, 'replace', $container);
            App.waitStop($list, false);
        };

        Ajax.html(link, data, $menu, onSuccess);
    };

    let setPagen = (data, reset = false) => {
        let $container = $(".actions-list-more").find("button");

        let pagen = 'PAGEN_' + $container.attr("data-navnum");
        let pagenomer = parseInt($container.attr("data-pagenomer"));

        data[pagen] = reset ? 1 : ++pagenomer;

        return data;
    };

    let setFilterUrl = () => {
        let oldUrl = url.parse(window.location.href);
        let scheme = oldUrl.scheme;
        let host = oldUrl.host;
        let path = oldUrl.path;
        let query = url.get(oldUrl.query, {array: true});

        let filterUrl = $("#tags-url").attr("data-tags-url");
        let newUrl;

        if (f.isEmpty(filterUrl)) {
            newUrl = url.build({scheme, host, path, get: ''});
        } else {
            newUrl = url.build({scheme, host, path, get: JSON.parse(decodeURIComponent(filterUrl))});
        }

        //History.pushState({state: newUrl}, "State 1", newUrl);
        window.history.pushState({href: newUrl}, document.title, newUrl);
    };

    let setTag = (sender, e) => {
        e.preventDefault();

        let $sender = $(sender);
        let $container = $(".actions-wrap");
        let $list = $(".actions-list");
        let $menu = $("#news-menu");

        App.waitStart($list, false);

        $sender.toggleClass("selected");

        let TAGS = [];
        $menu.find("button.selected").each(function (index, item) {
            TAGS.push($(item).attr("data-tag-code"));
        });

        let data = {
            AJAX: 'Y',
            ACTION: 'get_list',
            TAGS
        };
        //data = setPagen(data);

        let onSuccess = (response) => {
            //History.pushState({state: link}, document.title, link);
            //window.history.pushState({href: link}, document.title, link);

            update(response, 'replace', $container);
            setFilterUrl();
            App.waitStop($list, false);
        };

        Ajax.html('', data, $menu, onSuccess);
    };

    let bindActions = () => {
        History.Adapter.bind(window, 'statechange', function () { // Note: We are using statechange instead of popstate
            console.log('statechange');
            var State = History.getState(); // Note: We are using History.getState() instead of event.state
            console.log(State);
            window.location.href = State.url;
            return;
        });
    };

    let onloadHandler = () => {
        $("#catalog-list").removeClass("loading").addClass("loaded");
        $(".slider-catalog-list-spinner").remove();

        if ($(window).width() > 576) {
            $("#articles-menu").find(".root").addClass("in");
        }
    };

    let init = () => {
        console.log('init actions');
        bindActions();
        initSliders();

        window.Actions = Actions;
    };

    return {
        init, showMore, onloadHandler, doMenu, setTag
    };
})();

$(() => Actions.init());


$(window).load(() => {
    Actions.onloadHandler();
});