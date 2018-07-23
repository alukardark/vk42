'use strict';
/* global f, Ajax, App, History, Form */

let Faq = (() => {

    let update = (html, mode = 'append', $container) => {
        let className = ".faq-list";

        let $list = $container.find(className);

        $container.find(".faq-questions-more").remove();
        $container.find(".faq-questions-pagination").remove();

        let $response = $(html);
        let $response_more = $response.filter(".faq-questions-more");
        let $response_pagination = $response.filter(".faq-questions-pagination");
        //let $response_wait = $response.filter(".actions-wait");

        $response.find(className).wrapInner('<div/>');
        $response.find(className + ' > .faq-questions-item').unwrap();

        if (mode === 'append') {
            //$response.find(".article-list-title").remove();
            //$response.find(".news-list-title").remove();
            $list.append($response.html());
        } else {
            $list.empty();
            $list.html($response.html());
        }

        $response_more.appendTo($container);
        $response_pagination.appendTo($container);
        //$response_tags_url.appendTo($list.parent());
    };

    let setPagen = (data, reset = false) => {
        let $container = $(".faq-questions-more").find("button");

        let pagen = 'PAGEN_' + $container.attr("data-navnum");
        let pagenomer = parseInt($container.attr("data-pagenomer"));

        data[pagen] = reset ? 1 : ++pagenomer;

        return data;
    };

    let showMore = function (self) {
        let $self = $(self);
        let $container = $(self).parents(".faq-questions");
        let $more = $container.find(".faq-questions-more");

        let data = {
            AJAX: 'Y',
            ACTION: 'get_list',
            SHOWMORE: 'Y'
        };
        data = setPagen(data);

        let onSuccess = (response) => {
            update(response, 'append', $container);
        };

        Ajax.html('', data, $more, onSuccess);
    };

    let setCategory = (sender, async = false) => {
        let $sender = $(sender);
        let $list = $sender.parent();
        let $block = $sender.parents(".form-question-fakeselect");
        let $title = $block.find(".form-question-fakeselect-current");
        let CATEGORY = $sender.attr("data-category-id");

        $list.find("li").removeClass("selected");
        $sender.addClass("selected");
        $title.find("span").text($sender.find("span").text());
        Form.closeDropdown(sender);

        let $container = $(".faq-questions");
        App.waitStart();

        if (async === false) {
            Form.setDropdown($('[data-dropdown-id="' + CATEGORY + '"]').get(0), true);
        }

        let data = {
            AJAX: 'Y',
            ACTION: 'get_list',
            CATEGORY
        };
        data = setPagen(data, true);

        let onSuccess = (response) => {
            update(response, 'replace', $container);
            App.waitStop();
        };

        Ajax.html("", data, $block, onSuccess);
    };

    let showMoreText = (sender) => {
        let $sender = $(sender);

        $sender.parent().next().show(0, function () {
            $(this).addClass("opened");
            //if ($(this).is(':visible'))
            //    $(this).css('display', 'inline');
        });
        $sender.parent().remove();
    };

    let bindActions = () => {

    };

    let onloadHandler = () => {

    };

    let init = () => {
        console.log('init faq');
        bindActions();

        window.Faq = Faq;
    };

    return {
        init, onloadHandler, setCategory, showMoreText, showMore
    };
})();

$(() => Faq.init());


$(window).load(() => {
    Faq.onloadHandler();
});