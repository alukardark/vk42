'use strict';
/* global f, App, Ajax */

require("./common/yml.js");
require("mousewheel");
require("scrollbox-js");
require("scrollbox-css");

let Services = (() => {
    let className = 'opened';

    let setCity = (sender) => {
        let $sender = $(sender);

        if ($sender.hasClass('active')) {
            return false;
        }

        let city = $sender.attr('data-city');

        $sender.parent().find('> button.active').removeClass('active');
        $sender.addClass('active');

        $('.services-row').each((i, el) => $(el).removeClass("active"));
        $('.services-row[data-city="' + city + '"]').each((i, el) => $(el).addClass("active"));
        redrawObjectsStoresMap(city);

    };

    let openBaloon = (id) => {
        openBaloonMap(id);
        App.scrollTo($("#map").position().top);
    };

    let bindActions = () => {

    };

    let toggleStores = (sender) => {
        let $sender = $(sender);
        let $list = $("#uslugi-stores");

        $list.hasClass(className) ? closeStores(sender) : openStores(sender);
    };

    let openStores = (sender) => {
        let $sender = $(sender);
        let $list = $("#uslugi-stores");
        let eventName = f.getEventNameByAlias("dropdown.stores");

        $list.addClass(className).scrollbox();

        let onUnbind = () => closeStores(sender);
        App.bindClick($sender, $list, eventName, className, onUnbind);
    };

    let closeStores = (sender = null) => {
        let $sender = $(sender);
        let $list = $("#uslugi-stores");
        let eventName = f.getEventNameByAlias("dropdown.stores");

        $list.removeClass(className).scrollbox("destroy");
        $(document).unbind(eventName);
    };

    let toggleDescription = (sender) => {
        let $sender = $(sender);
        let $block = $sender.parents(".uslugi-card").find(".uslugi-card-description");

        $block.hasClass(className) ? closeDescription(sender) : openDescription(sender);
    };

    let openDescription = (sender) => {
        let $sender = $(sender);
        let $card = $sender.parents(".uslugi-card");

        let $block = $card.find(".uslugi-card-description");
        let $arrow = $card.find(".uslugi-card-arrow");

        $block.addClass(className).slideDown();
        $arrow.addClass(className);
    };

    let closeDescription = (sender = null) => {
        let $sender = $(sender);
        let $card = $sender.parents(".uslugi-card");

        let $block = $card.find(".uslugi-card-description");
        let $arrow = $card.find(".uslugi-card-arrow");

        $block.removeClass(className).slideUp();
        $arrow.removeClass(className);
    };

    let setMode = (mode, update = true) => {
        let $fakeselect = $("#uslugi-stores-fakeselect");
        let $list = $("#uslugi-stores");
        let $store = $(".uslugi-stores-info");

        if (update === true) {
            if (mode === 'ALL') {
                updateVitrina();
            } else {
                let storeXmlId = $list.find("[data-store-xml-id].selected").attr("data-store-xml-id");
                updateVitrina(storeXmlId);
            }
        }

        closeStores();
        $fakeselect.find("button").removeClass("selected");
        $fakeselect.find('button[data-mode="' + mode + '"]').addClass("selected");
    };

    let selectStore = (sender) => {
        let $sender = $(sender);
        let $list = $("#uslugi-stores");
        let storeXmlId = $sender.attr("data-store-xml-id");

        closeStores(sender);
        setMode('STORE', false);

        $("#uslugi-stores-current").html($sender.html());

        $list.find('[data-store-xml-id]').removeClass('selected');
        $list.find('[data-store-xml-id="' + storeXmlId + '"]').addClass('selected');

        updateVitrina(storeXmlId);
    };

    let updateVitrina = (storeXmlId = null) => {
        let $wrap = $(".uslugi-inner");
        let $store = $(".uslugi-stores-info");
        let className = 'inactive';
        let $spinner = $("#uslugi-wait");

        App.waitStart($spinner);

        let onSucces = (responce) => {
            let SECTIONS = responce.result.SECTIONS;
            let ELEMENTS = responce.result.ELEMENTS;
            let STORE = responce.result.STORE;

            $wrap.find('[data-section-id]').addClass(className);
            $wrap.find('[data-element-id]').addClass(className);

            for (let key in SECTIONS) {
                let SECTION_ID = SECTIONS[key];
                $wrap.find('[data-section-id="' + SECTION_ID + '"]').removeClass(className);
            }

            for (let key in ELEMENTS) {
                let ELEMENT_ID = ELEMENTS[key];
                $wrap.find('[data-element-id="' + ELEMENT_ID + '"]').removeClass(className);
            }

            if (!!$store && !!STORE) {
                let $shedule = $store.find(".uslugi-stores-info-content-shedule");
                let $phone = $store.find(".uslugi-stores-info-content-phone");
                let $maplink = $store.find(".uslugi-stores-info-content-maplink");

                $phone.find("span").text(STORE.PHONE);
                $shedule.find("span").text(STORE.SCHEDULE);
                $maplink.find("button").attr("data-store-xml_id", STORE.XML_ID);
                $maplink.find("button").attr("data-store-city", STORE.UF_STORE_CITY);
            }

            App.waitStop($spinner);
        };

        let url = '/ajax/ajax_service.php';
        let ACTION = 'set_store';
        let STRORE_XML_ID = storeXmlId;
        let data = {ACTION, STRORE_XML_ID};
        Ajax.send(url, data, $wrap, onSucces);
    };

    let SERVICES = null;
    let onOpenServiceEntryForm = () => {
        console.log('onOpenServiceEntryForm');

        if (SERVICES === null) {
            console.log('loading services');

            let $form = $("#form-service-entry").find("form");
            let $CITY = $form.find('[data-question-code="CITY"]');
            let $SERVICE_CATEGORY = $form.find('[data-question-code="SERVICE_CATEGORY"]');

            let CITY = $CITY.find(".js-value").text();


            let url = '/ajax/ajax_service.php';
            let ACTION = 'GET_OPERATIONS';
            let data = {
                ACTION, CITY
            };

            let onSucces = (responce) => {
                console.log(responce);
                SERVICES = responce;

                fillServicesForm();

                $form.removeClass("loading");
            };

            Ajax.send(url, data, $SERVICE_CATEGORY, onSucces);
        }
    };


    let fillServicesForm = () => {
        console.log('fillservicesForm');

        let operations = SERVICES.OPERATIONS;
        //let services = SERVICES.SERVICES;

        fillServicesFormDropdown('SERVICE_CATEGORY');
        fillServicesFormDropdown('SERVICE_ITEM');
        fillServicesFormDropdown('SERVICE_POINT');
        //fillServicesFormDropdown('SERVICE_ITEM');
    };

    let fillServicesFormDropdown = (code) => {
        console.log('fillServicesFormDropdown ' + code);

        let $form = $("#form-service-entry").find("form");
        let $container = $form.find('[data-question-code="' + code + '"]');

        let $variants = $container.find(".js-variants");
        let $value = $container.find(".js-value");
        let $input = $container.find("input");

        $variants.empty();

        let items;
        let parent = null;
        if (code === 'SERVICE_CATEGORY') {
            items = SERVICES.GROUPS;
        } else if (code === 'SERVICE_ITEM') {
            parent = 'SERVICE_CATEGORY';

            let $parent = $form.find('[data-question-code="' + parent + '"]');
            let parentValue = $parent.find(".js-value").text();

            items = SERVICES.ITEMS[parentValue];
        } else if (code === 'SERVICE_POINT') {
            parent = 'SERVICE_ITEM';

            let $parent = $form.find('[data-question-code="' + parent + '"]');
            let parentValue = $parent.find(".js-value").text();

            items = SERVICES.ITEMS[parentValue];
        }

        for (let i in items)
        {
            let item = items[i];
            let selected = '';

            if (i === '0' || i === 0) {
                selected = 'selected';
                $value.text(item);
                $input.val(item);
            }

            $variants.append('<li' +
                    ' class="form-question-fakeselect-variants-item js-item ' + selected + '"' +
                    ' data-dropdown-id="' + item + '"' +
                    ' onclick="Form.setDropdown(this)"' +
                    '><span>' + item + '</span></li>');
        }
    };

    let init = () => {
        console.log('init services');
        //bindActions();

        window.Services = Services;
    };


    return {
        init, setCity, openBaloon, toggleStores, selectStore, setMode, toggleDescription,
        onOpenServiceEntryForm
    };
})();

$(() => Services.init());

$(window).load(() => {
    //Index.onloadHandler();
});