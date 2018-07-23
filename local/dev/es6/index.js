'use strict';
/* global f, App */

require('slick-js');
require('slick-css');
require("./common/yml.js");

let Index = (() => {
    let initSliders = () => {

        if (f.isExist($('#promo-slider'))) {

            //порядок инициализации промо-слайдеров не менять
            $("#promo-slider-m")
                    .html($("#promo-slider").html())
                    .promise()
                    .done(function () {
                        $('#promo-slider-m .promo-slider-list').slick({
                            dots: false,
                            arrows: false,
                            infinite: true,
                            slidesToShow: 1,
                            slidesToScroll: 1,
                            centerMode: true,
                            centerPadding: '25%',
                            speed: 200,
                            fade: false,
                            adaptiveHeight: false,
                            focusOnSelect: true,
                            responsive: [
                                {
                                    breakpoint: 700,
                                    settings: {
                                        centerPadding: '22%'
                                    }
                                },
                                {
                                    breakpoint: 650,
                                    settings: {
                                        centerPadding: '20%'
                                    }
                                },
                                {
                                    breakpoint: 600,
                                    settings: {
                                        centerPadding: '18%'
                                    }
                                },
                                {
                                    breakpoint: 576,
                                    settings: {
                                        centerMode: false,
                                        dotsClass: 'slick-dots circles',
                                        dots: true
                                    }
                                }
                            ]
                        });
                    });

            $('#promo-slider .promo-slider-list').slick({
                dots: true,
                dotsClass: 'slick-dots circles',
                arrows: false,
                infinite: true,
                speed: 200,
                fade: false,
                adaptiveHeight: false
            });
        }

        if (f.isExist($('#services-slider'))) {
            $('#services-slider').slick({
                dots: false,
                dotsClass: 'slick-dots circles',
                arrows: false,
                infinite: false,
                speed: 200,
                fade: false,
                adaptiveHeight: false,
                slidesToShow: 2,
                slidesToScroll: 1,
                focusOnSelect: true,
                appendDots: $(".services-list"),
                responsive: [
                    {
                        breakpoint: 768,
                        settings: {
                            slidesToShow: 1,
                            dots: true
                        }
                    }
                ]
            });
        }
    };

    let onloadHandler = () => {
        console.log('index onloadHandler');
    };

    let scrollToServices = () => App.scrollTo($("#index-services").offset().top);

    let setFilterForm = (sender) => {
        let $sender = $(sender);

        $sender.parent().find("button").removeClass("selected");
        $sender.addClass("selected");

        let type = $sender.attr("data-form-id");

        let $filter_wrap = $sender.parents(".row.active").find(".filter-wrap");
        $filter_wrap.removeClass("active");
        $('.filter-wrap[data-filter-type="' + type + '"]').addClass("active");
    };

    let setFilterContainer = (sender, code) => {
        $(".nav-sections-list button").removeClass("selected");
        $(sender).addClass("selected");

        $('[data-filter-container-code]').removeClass("active");
        $('[data-filter-container-code="' + code + '"]').addClass("active");
    };


    let bindActions = () => {
        $('.contacts-cities-buttons > button').on('click', function () {
            let $this = $(this),
                    city = $this.data('city');
            if ($this.hasClass('active')) {
                return false;
            }
            $this.parent().find('> button.active').removeClass('active');
            $this.addClass('active');
            redrawObjectsStoresMap(city);
        });
    };

    let init = () => {
        console.log('init index');
        initSliders();
        bindActions();

        window.Index = Index;
    };


    return {
        init, onloadHandler, scrollToServices, setFilterForm, setFilterContainer
    };
})();

$(() => Index.init());

$(window).load(() => {
    //Index.onloadHandler();
});