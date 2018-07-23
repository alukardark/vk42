'use strict';
/* global f, Catalog, Ajax, History, url, App */

require("mousewheel");
require("scrollbox-js");
require("scrollbox-css");
require("ionrangeslider-js");
require("ionrangeslider-css");
require("ionrangeslider-skin");

let Filter = (() => {
    const PATH_OILS = '/masla/';

    let blockClass = ".filter-block";
    let blockTitleClass = ".filter-block-title";
    let className = 'opened';
    let shiftedClass = 'shifted';
    let syncRangeTimer;

    let toggleDropdown = (sender) => {
        let $sender = $(sender);
        let $block = $sender.parent();

        if ($block.hasClass("active")) {
            $block.hasClass(className) ? closeDropdown(sender) : openDropdown(sender);
        }
    };

    let openDropdown = (sender) => {
        let $sender = $(sender);
        let $block = $sender.parent();
        let $list = $block.find("ul");
        let eventName = f.getEventNameByAlias("dropdown." + $block.attr("data-property"));

        closeDropdownAll();

        let $selected = $list.find("li.selected");

        let startAt = 0;
        if ($selected !== undefined) {
            startAt = $selected.index() * 28 - 56;
            if (startAt < 0)
                startAt = 0;
        }

        $block.addClass(className).find("ul").scrollbox({startAt: {y: startAt}});

        let onUnbind = () => closeDropdown(sender);
        App.bindClick($sender, $block, eventName, className, onUnbind);
    };

    let closeDropdown = (sender) => {
        let $sender = $(sender);
        let $block = $sender.parent();
        let eventName = f.getEventNameByAlias("dropdown." + $block.attr("data-property"));

        $block.removeClass(className).find("ul").scrollbox("destroy");
        $(document).unbind(eventName);
    };

    let closeDropdownAll = () => {
        let $container_cars = $(".js-filter-container-cars");
        let $container_size = $(".js-filter-container-size");
        let $container_oils = $(".js-filter-container-oils");

        $container_cars.find(blockTitleClass).each((index, element) => closeDropdown(element));
        $container_size.find(blockTitleClass).each((index, element) => closeDropdown(element));
        $container_oils.find(blockTitleClass).each((index, element) => closeDropdown(element));
    };

    let getFilterByCar = (sender) => {
        let $sender = $(sender);
        let FILTER = {}, $block, property, value;

        let $container = $sender.parents(".filter");
        let $wrap = $sender.parents(".filter-wrap");
        let $spinner = $container.find(".js-filter-spinner");
        let $container_cars = $container.find(".js-filter-container-cars");

        closeDropdownAll();
        $spinner.fadeIn();

        //получаем значения выбранных свойств
        $container_cars.find(blockClass).each((index, item) => {
            $block = $(item);
            property = $block.attr("data-property");
            value = $block.attr("data-value");

            FILTER[property] = value;
        });

        let onSucces = (response = null) => {
            console.log(response);

            //обновляем фильтр по авто
            let items = response.items;
            let ul_html = '';

            for (let i = 0, l = items.length; i < l; ++i) {
                ul_html += '<li data-value="' + items[i] + '" title="' + items[i] + '">' + items[i] + '</li>';
            }

            $block = $container_cars.find(blockClass + '[data-property="' + response.property + '"]');
            $block.addClass("active").find("ul").html(ul_html);

            //если в response пришло refresh==Y, то обновляем каталог
            if ($("#body").hasClass('index-page') === false && response.refresh && response.refresh === "Y") {
                let data = {
                    AJAX: 'Y',
                    ACTION: 'get_section',
                    FILTER_SET: $wrap.attr("data-filter-type"),
                    FILTER: JSON.parse(response.FILTER)
                };

                data = Catalog.setPagen(data, true);
                Catalog.refresh(sender, data, $("#catalog"));
            }

            $spinner.fadeOut();
        };

        let url = '/ajax/ajax_filter.php';
        let ACTION = 'get_filter_car';
        let IB = $container.find(".filter-container").data("ib");
        let SC = $container.find(".filter-container").data("sc");
        let data = {ACTION, FILTER, IB, SC};
        Ajax.send(url, data, $container_cars.parent(), onSucces);
    };

    let getOilFilterParams = (sender, SECTION_CODE) => {
        let $sender = $(sender);
        let FILTER = {}, $block, property, value;

        let $spinner = $(".js-filter-spinner");
        let $container = $(".js-filter-container-oils");

        closeDropdownAll();
        $spinner.fadeIn();

        //получаем значения выбранных свойств
        $container.find(blockClass).each((index, item) => {
            $block = $(item);
            property = $block.attr("data-property");
            value = $block.attr("data-value");

            FILTER[property] = value;
        });

        let onSucces = (response = null) => {
            $container.parents(".promo-filter").first().html(response);
            $spinner.fadeOut();
        };

        //let url = '/ajax/ajax_filter.php';
        let ACTION = 'get_filter_oils_params';
        let AJAX = 'Y';
        let data = {AJAX, ACTION, SECTION_CODE};
        Ajax.html('', data, $container.parent(), onSucces);
    };

    let getDiscsFilterParams = (sender, SECTION_CODE) => {
        let $sender = $(sender);
        let FILTER = {}, $block, property, value;

        let $spinner = $(".js-filter-spinner");
        let $container = $(".js-filter-container-discssize");

        closeDropdownAll();
        $spinner.fadeIn();

        //получаем значения выбранных свойств
        $container.find(blockClass).each((index, item) => {
            $block = $(item);
            property = $block.attr("data-property");
            value = $block.attr("data-value");

            FILTER[property] = value;
        });

        //FILTER['TUNING'] = $container.attr("data-tuning");

        let onSucces = (response = null) => {
            $container.parents(".promo-filter").find('[data-filter-type="discs_size"]').parent().html(response);
            $spinner.fadeOut();
        };

        //let url = '/ajax/ajax_filter.php';
        let ACTION = 'get_filter_discs_params';
        let AJAX = 'Y';
        let data = {AJAX, ACTION, SECTION_CODE};
        Ajax.html('', data, $container.parent(), onSucces);
    };

    let setSize = (sender) => {
        let $sender = $(sender);
        let $container = $sender.parents(".filter");
        let $wrap = $sender.parents(".filter-wrap");
        let $spinner = $container.find(".js-filter-spinner");
        let $block = $sender.parents(blockClass);

        let property = $block.attr("data-property");
        let value = $sender.attr("data-value");

        $block.find("span").text($sender.text());

        closeDropdownAll();

        if ($("#body").hasClass('index-page') === false) {
            let data = {
                AJAX: 'Y',
                ACTION: 'get_section',
                FILTER_SET: $wrap.attr("data-filter-type"),
                FILTER: {[property]: value, 'TUNING': $container.find(".filter-container").attr("data-tuning")}
            };

            data = Catalog.setPagen(data, true);
            Catalog.refresh(sender, data, $("#catalog"));
        }
    };

    //переключатель фильтра по авто/размеру
    let choose = (sender) => {
        let $sender = $(sender);

        let ACTION = 'SET_FILTER_TYPE';
        let FILTER_TYPE = $sender.attr("data-button-type");

        Ajax.send("/ajax/ajax_common.php", {ACTION, FILTER_TYPE}, $(sender));

        if ($sender.hasClass("active")) {
            return false;
        }

        $("[data-button-type]").toggleClass("active");
        $("[data-filter-type]").toggleClass("active");
    };

    //умный фильтр, кроме цены
    let smart = (sender) => {
        let $sender = $(sender);
        let $block = $sender.parent();

        let filter_type = $block.attr("data-filter-type");
        let property = $block.attr("data-property");
        let value = $sender.attr("data-value");

        //несмотря на то, что в ajax приходит html, все же сразу установим чекбоксы. для красоты
        if (filter_type === 'checkbox') {
            $sender.toggleClass("selected");
        } else {
            $block.find("button").removeClass("selected");
            $sender.addClass("selected");
        }

        let data = {
            AJAX: 'Y',
            ACTION: 'get_section',
            FILTER: {[property]: value}
        };

        data = Catalog.setPagen(data, true);
        Catalog.refresh(sender, data, $("#catalog"));
    };

    let clear = (sender) => {
        clearTimeout(syncRangeTimer);

        let data = {
            AJAX: 'Y',
            ACTION: 'get_section',
            FILTER: {'CLEAR_ALL': '1'}
        };

        data = Catalog.setPagen(data, true);
        Catalog.refresh(sender, data, $("#catalog"));
    };
    
    let clearCar = (sender) => {
        clearTimeout(syncRangeTimer);

        let data = {
            AJAX: 'Y',
            ACTION: 'get_section',
            FILTER: {'CLEAR_CAR': '1'}
        };

        data = Catalog.setPagen(data, true);
        Catalog.refresh(sender, data, $("#catalog"));
    };
    
    let clearSize = (sender) => {
        clearTimeout(syncRangeTimer);

        let data = {
            AJAX: 'Y',
            ACTION: 'get_section',
            FILTER: {'CLEAR_SIZE': '1'}
        };

        data = Catalog.setPagen(data, true);
        Catalog.refresh(sender, data, $("#catalog"));
    };

    let clearFilterItem = (sender) => {
        let $sender = $(sender);

        let data = {
            AJAX: 'Y',
            ACTION: 'get_section',
            FILTER: {'CLEAR_PROPERTY': '1'},
            FILTER_PROPERTY: $sender.attr("data-item-property"),
            FILTER_PROPERTY_VALUE: $sender.attr("data-item-value")
        };

        data = Catalog.setPagen(data, true);
        Catalog.refresh(sender, data, $("#catalog"));
    };

    let redirectToFilter = (sender) => {
        let $sender = $(sender);
        let filterType = $sender.attr("data-button-filter");
        let $promoFilter = $("#promo").find(".promo-filter");
        let $filter = $promoFilter.find('.filter-wrap[data-filter-type="' + filterType + '"]');
        let params;

        if (filterType === 'car' || filterType === 'size') {
            params = '/katalog/legkovye/?FILTER[CLEAR_BEFORE]=1&FILTER_SET=' + filterType;
        } else if (filterType === 'discs_car' || filterType === 'discs_size') {
            let section = $filter.find('.filter-block[data-property="SECTION_CODE"]').attr("data-value");
            if (section === undefined) {
                section = 'legkosplavnye_diski';
            }
            params = '/diski/' + section + '/?FILTER[CLEAR_BEFORE]=1&FILTER_SET=' + filterType;
        }

        $filter.find(".filter-block").each(function (index, item) {
            let $item = $(item);
            let property = $item.attr("data-property");
            if (property === "SECTION_CODE") {
                return true;
            }
            let value = $item.find("li.selected").attr("data-value");

            if (!f.isEmpty(value)) {
                params += '&FILTER[' + property + ']=' + value;
            }
        });

        window.location.replace(params);
    };

    let redirectToFilterOils = (sender) => {
        let $sender = $(sender);
        let filterType = $sender.attr("data-button-filter");
        let $promoFilter = $("#promo").find(".promo-filter");
        let $filter = $promoFilter.find('.filter-wrap[data-filter-type="' + filterType + '"]');
        let filterCode = $filter.attr("data-filter-code");

        let params = PATH_OILS + filterCode + '/?FILTER[CLEAR_BEFORE]=1&FILTER_SET=' + filterType;

        $filter.find(".filter-block").each(function (index, item) {
            let $item = $(item);
            let property = $item.attr("data-property");
            let value = $item.find("li.selected").attr("data-value");

            if (!f.isEmpty(value)) {
                params += '&FILTER[' + property + ']=' + value;
            }
        });

        window.location.replace(params);
    };

    /**
     * 
     * BUTTON FILTERS TOGGLE
     * z
     */
    let eventToggleSmartFilters = f.getEventNameByAlias("toggle_smart_filters");

    let toggleSmartFilters = (sender) => {
        let $sender = $(sender);
        let $filters = $(".catalog-filterleft");
        $filters.hasClass(className) ? closeSmartFilters() : openSmartFilters(sender);
    };

    let openSmartFilters = (sender) => {
        let $sender = $(sender);
        let $filters = $(".catalog-filterleft");

        $filters.addClass(className);
        $("#body").addClass(shiftedClass);

        let onUnbind = () => closeSmartFilters();
        App.bindClick($sender, $filters, eventToggleSmartFilters, className, onUnbind);
    };

    let closeSmartFilters = () => {
        let $filters = $(".catalog-filterleft");

        $filters.removeClass(className);
        $("#body").removeClass(shiftedClass);
        $(document).unbind(eventToggleSmartFilters);
    };

    let syncRangeSlider = ($slider, from, to) => {
        let $container = $slider.parent();
        let $input_min = $container.find(".input-min");
        let $input_max = $container.find(".input-max");

        clearTimeout(syncRangeTimer);
        $input_min.val(from);
        $input_max.val(to);

        let property = $slider.attr("data-property");
        let min = parseInt($slider.attr("data-min"));
        let max = parseInt($slider.attr("data-max"));

        console.log(from, to);

        if (from === null || from === undefined || from === '' || from < min) {
            from = min;
        }
        if (to === null || to === undefined || to === '' || to > max) {
            to = max;
        }

        console.log(from, to);

        syncRangeTimer = setTimeout(() => {
            let data = {
                AJAX: 'Y',
                ACTION: 'get_section',
                FILTER: {
                    [property]: {'FROM': from, 'TO': to}
                }
            };

            data = Catalog.setPagen(data, true);
            Catalog.refresh(null, data, $("#catalog"));
        }, 1400);
    };

    let initRangeSlider = ($slider) => { //new Method
        let $container = $slider.parent();
        let $input_min = $container.find(".input-min");
        let $input_max = $container.find(".input-max");

        let step = !!$slider.attr("data-step") ? $slider.attr("data-step") : 1;

        $slider.ionRangeSlider({
            type: "double",
            step: step,
            keyboard: true,
            keyboard_step: 1,
            hide_min_max: true,
            hide_from_to: true,
            onChange: function (data) {
                console.log("ionRangeSlider onChange");
                syncRangeSlider($slider, data.from, data.to);
            },
            onUpdate: function (data) {
                console.log("ionRangeSlider onUpdate");
                syncRangeSlider($slider, data.from, data.to);
            }
        });

        let slider = $slider.data("ionRangeSlider");
        let name = $slider.attr("name");
        let property = $slider.attr("data-property");

        let eventNameSliderClick = "click." + name;
        let eventNameSliderChange = "change." + name;
        let eventNameSliderKeyDown = "keydown." + name;
        let className = ".js-range-slider-input";

        $container.off(eventNameSliderClick, className);
        $container.on(eventNameSliderClick, className, function () {
            this.select();
        });

        $container.off(eventNameSliderChange, className);
        $container.on(eventNameSliderChange, className, function () {
            let from = $input_min.val();
            let to = $input_max.val();

            let min = parseInt($slider.attr("data-min"));
            let max = parseInt($slider.attr("data-max"));

            if (from === null || from === undefined || from === '' || from < min) {
                from = min;
            }
            if (to === null || to === undefined || to === '' || to > max) {
                to = max;
            }

            let range = {from, to};
            console.log("eventNameSliderChange", range);

            slider.update(range);
        });

        $container.off(eventNameSliderKeyDown, className);
        $container.on(eventNameSliderKeyDown, className, function (e) {
            let $sender = $(this);
            let key_step = slider.options.step * slider.options.keyboard_step;
            let key_code = e.keyCode;

            let value = parseInt($sender.val());
            let range = null;

            if (key_code === 38) {//up
                $sender.val(value + key_step);
                range = {from: $input_min.val(), to: $input_max.val()};
            } else if (key_code === 40) {//down
                $sender.val(value - key_step);
                range = {from: $input_min.val(), to: $input_max.val()};
            } else if (key_code === 13 || key_code === 38 || key_code === 40) {
                range = {from: $input_min.val(), to: $input_max.val()};
            }

            if (!!range) {
                console.log("eventNameSliderKeyDown", range);
                slider.update(range);
            }
        });
    };

    let bindActions = () => {
        let moved = false, timer;
        let eventName = "click.filter_li touchend.filter_li touchmove.filter_li";

        $(document).on(eventName, ".filter-container li", function (e) {
            if (e.type === "touchmove") {
                moved = true;
                clearTimeout(timer);
            }

            if (!moved) {
                let $sender = $(this);
                let $container = $sender.parents(".filter-container");
                let $wrap = $sender.parents(".filter-wrap");

                if ($container.hasClass("js-filter-container-cars")) {
                    console.log('js-filter-container-cars');
                    let $block = $sender.parents(blockClass);

                    $block.find("li").removeClass("selected");
                    $sender.addClass("selected");
                    $block.attr("data-value", $sender.text());
                    $block.find(".filter-block-title").find("span").text($sender.text());

                    //очищаем следующие за этим блоки
                    let $nextAllBlocks = $block.nextAll();

                    $nextAllBlocks.each((index, item) => {
                        let $item = $(item);
                        let $title = $item.find(".filter-block-title");

                        $item.removeClass("active").attr("data-value", "").find("li").remove();
                        $title.find("span").text($title.attr("title"));
                    });

                    if ($nextAllBlocks.length === 0) {
                        $wrap.find('[data-button-filter]').removeClass("disabled");
                    } else {
                        $wrap.find('[data-button-filter]').addClass("disabled");
                    }

                    //получаем список значений следуещго свойства
                    getFilterByCar(this);
                } else {
                    $sender.parent("ul").find("li").removeClass("selected");
                    $sender.addClass("selected");
                }

                if ($container.hasClass("js-filter-container-size") || $container.hasClass("js-filter-container-oils")) {
                    console.log('js-filter-container-size');
                    setSize(this);
                }
            } else {
                timer = setTimeout(() => moved = false, 700);
            }
        });
    };

    let initElements = () => {
        console.log('filter initElements()');

        $(".js-range-slider").each(function (index, item) {
            initRangeSlider($(item));
        });

        $(".js-block-scrollbox").each(function (index, item) {
            $(item).scrollbox();
        });
    };

    let init = () => {
        console.log('init filter');

        bindActions();
        initElements();
        closeSmartFilters();

        window.Filter = Filter;
    };

    return {
        init, initElements, bindActions, toggleDropdown, choose, smart,
        clear, clearFilterItem, redirectToFilter, redirectToFilterOils,
        toggleSmartFilters, getOilFilterParams, getDiscsFilterParams,
        clearCar, clearSize
    };
})();

$(() => Filter.init());