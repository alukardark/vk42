'use strict';

/* global google, ymaps, STORES */

export function  initStoresMap(initialCity) {

    var
            STORES_MAP,
            points,
            myCollection,
            myPlacemarks;

    STORES_MAP = new ymaps.Map('map', {
        center: [0, 0],
        zoom: 14,
        controls: ['smallMapDefaultSet']
    });

    // Создадим пользовательский макет ползунка масштаба.
    var ZoomLayout = ymaps.templateLayoutFactory.createClass(
            "<div>" +
            "<div id='zoom-in' class='zoom-btn'><i class='icon-plus'></i></div>" +
            "<div id='zoom-out' class='zoom-btn'><i class='icon-minus'></i></div>" +
            "</div>",
            {
                // Переопределяем методы макета, чтобы выполнять дополнительные действия
                // при построении и очистке макета.
                build: function () {
                    // Вызываем родительский метод build.
                    ZoomLayout.superclass.build.call(this);

                    // Привязываем функции-обработчики к контексту и сохраняем ссылки
                    // на них, чтобы потом отписаться от событий.
                    this.zoomInCallback = ymaps.util.bind(this.zoomIn, this);
                    this.zoomOutCallback = ymaps.util.bind(this.zoomOut, this);

                    // Начинаем слушать клики на кнопках макета.
                    $('#zoom-in').bind('click', this.zoomInCallback);
                    $('#zoom-out').bind('click', this.zoomOutCallback);
                },

                clear: function () {
                    // Снимаем обработчики кликов.
                    $('#zoom-in').unbind('click', this.zoomInCallback);
                    $('#zoom-out').unbind('click', this.zoomOutCallback);

                    // Вызываем родительский метод clear.
                    ZoomLayout.superclass.clear.call(this);
                },

                zoomIn: function () {
                    var map = this.getData().control.getMap();
                    // Генерируем событие, в ответ на которое
                    // элемент управления изменит коэффициент масштабирования карты.
                    this.events.fire('zoomchange', {
                        oldZoom: map.getZoom(),
                        newZoom: map.getZoom() + 1
                    });
                },

                zoomOut: function () {
                    var map = this.getData().control.getMap();
                    this.events.fire('zoomchange', {
                        oldZoom: map.getZoom(),
                        newZoom: map.getZoom() - 1
                    });
                }
            }
    );


    // Создание макета балуна
    var MyBalloonLayout = ymaps.templateLayoutFactory.createClass(
            '<div class="balloon-container">' +
            '<div class="balloon-container__title">$[properties.title]</div>' +
            '<div class="balloon-container__address">$[properties.address]</div>' +
            '[if properties.phone]<div class="balloon-container__phone" style="white-space:nowrap;">'+
                '$[properties.phone]'+
                '[if properties.phone2] (магазин, сервис)[endif]' +
            '</div>[endif]' +
            '[if properties.phone2]<div class="balloon-container__phone">$[properties.phone2] (автомойка)</div>[endif]' +
            '[if properties.button]<button class="balloon-container__button">Записаться на услуги</button>[endif]' +
            '<div class="arrow"></div>' +
            '<button class="close" href="#">&times;</button>' +
            '</div>',
            {
                /**
                 * Строит экземпляр макета на основе шаблона и добавляет его в родительский HTML-элемент.
                 * @see https://api.yandex.ru/maps/doc/jsapi/2.1/ref/reference/layout.templateBased.Base.xml#build
                 * @function
                 * @name build
                 */
                build: function () {
                    this.constructor.superclass.build.call(this);
                    this._$element = $('.balloon-container', this.getParentElement());
                    this.applyElementOffset();
                    this._$element.find('.close').on('click', $.proxy(this.onCloseClick, this));
                },
                /**
                 * Удаляет содержимое макета из DOM.
                 * @see https://api.yandex.ru/maps/doc/jsapi/2.1/ref/reference/layout.templateBased.Base.xml#clear
                 * @function
                 * @name clear
                 */
                clear: function () {
                    this._$element.find('.close').off('click');
                    this.constructor.superclass.clear.call(this);
                },
                /**
                 * Метод будет вызван системой шаблонов АПИ при изменении размеров вложенного макета.
                 * @see https://api.yandex.ru/maps/doc/jsapi/2.1/ref/reference/IBalloonLayout.xml#event-userclose
                 * @function
                 * @name onSublayoutSizeChange
                 */
                onSublayoutSizeChange: function () {
                    MyBalloonLayout.superclass.onSublayoutSizeChange.apply(this, arguments);
                    if (!this._isElement(this._$element)) {
                        return;
                    }
                    this.applyElementOffset();
                    this.events.fire('shapechange');
                },
                /**
                 * Сдвигаем балун, чтобы "хвостик" указывал на точку привязки.
                 * @see https://api.yandex.ru/maps/doc/jsapi/2.1/ref/reference/IBalloonLayout.xml#event-userclose
                 * @function
                 * @name applyElementOffset
                 */
                applyElementOffset: function () {
                    this._$element.css({
                        left: -(this._$element.find('.arrow')[0].offsetWidth),
                        top: -(this._$element[0].offsetHeight + 100)
                    });
                },
                /**
                 * Закрывает балун при клике на крестик, кидая событие "userclose" на макете.
                 * @see https://api.yandex.ru/maps/doc/jsapi/2.1/ref/reference/IBalloonLayout.xml#event-userclose
                 * @function
                 * @param {
                 type} e
                 * @name onCloseClick
                 */
                onCloseClick: function (e) {
                    e.preventDefault();
                    this.events.fire('userclose');
                },
                /**
                 * Используется для автопозиционирования (balloonAutoPan).
                 * @see https://api.yandex.ru/maps/doc/jsapi/2.1/ref/reference/ILayout.xml#getClientBounds
                 * @function
                 * @name getClientBounds
                 * @returns {Number[][]} Координаты левого верхнего и правого нижнего углов шаблона относительно точки привязки.
                 */
                getShape: function () {
                    if (!this._isElement(this._$element)) {
                        return MyBalloonLayout.superclass.getShape.call(this);
                    }

                    var position = this._$element.position();
                    var coordinates = new ymaps.geometry.pixel.Rectangle([
                        [position.left, position.top], [
                            position.left + this._$element[0].offsetWidth,
                            position.top + this._$element[0].offsetHeight + this._$element.find('.arrow')[0].offsetHeight
                        ]
                    ]);

                    return new ymaps.shape.Rectangle(coordinates);
                },
                /**
                 * Проверяем наличие элемента (в ИЕ и Опере его еще может не быть).
                 * @function
                 * @private
                 * @name _isElement
                 * @param {
                 jQuery} [element] Элемент.
                 * @returns {Boolean} Флаг наличия.
                 */
                _isElement: function (element) {
                    return element && element[0] && element.find('.arrow')[0];
                }
            }
    );

    /**
     * Функция возвращает объект, содержащий опции метки.
     * Все опции, которые поддерживают геообъекты, можно посмотреть в документации.
     * @see https://api.yandex.ru/maps/doc/jsapi/2.1/ref/reference/GeoObject.xml
     */
    var getPointOptions = function () {
        return {
            //iconLayout: 'default#image',
            //iconImageHref: '/images/placemark.png',
            //iconImageSize: [79, 69],
            //iconImageOffset: [-40, -35],
            hideIconOnBalloonOpen: false,
            balloonLayout: MyBalloonLayout
                    //balloonContentLayout: MyBalloonContentLayout,
                    //balloonPanelMaxMapArea: 0
        };
    };

    /**
     * Функция перерисовывет существующие склады на карте.
     * @param {
     string} city
     * @returns {void}
     */
    window.redrawObjectsStoresMap = function (city) {
        if (!!city) {
            city = city.toString().toLowerCase();
        }

        points = STORES[city];
        myCollection = new ymaps.GeoObjectCollection({}, getPointOptions());
        myPlacemarks = [];

        for (var i = 0; i < points.length; i++) {
            var placemark = new ymaps.Placemark(
                    points[i].coords,
                    $.extend({}, points[i], {
                        hintContent: points[i].address
                    }),
                    {
                        iconLayout: 'default#image',
                        iconImageHref: '/images/placemark_ct.png',
                        iconImageSize: [56, 80],
                        iconImageOffset: [-28, -80]
                    }
            );

            myPlacemarks[i] = placemark;
            myCollection.add(placemark);
        }

        STORES_MAP.geoObjects.add(myCollection);
        STORES_MAP.setBounds(myCollection.getBounds(), {
            checkZoomRange: true
        });
    };

    window.openBaloonMap = function (id) {
        var placemark = myPlacemarks[id];
        placemark.balloon.open();
    };

    window.openBaloonMapByXmlId = function (xmlId, city) {
        console.log('openBaloonMapByXmlId ' + xmlId + city);
        city = city.toString().toLowerCase();

        var curPoints = STORES[city],
                ident = 0,
                center;

        for (var i = 0; i < curPoints.length; i++) {
            if (curPoints[i].XML_ID === xmlId) {
                ident = i;
                break;
            }
        }

        var placemark = myPlacemarks[ident];

        console.log(placemark);

        placemark.balloon.open();
        placemark.balloon.autoPan();
    };

    window.destroyMap = function () {
        STORES_MAP.destroy();
    };

    // Итоговые опции для карты
    var zoomControl = new ymaps.control.ZoomControl({
        options: {
            layout: ZoomLayout,
            position: {bottom: '35px', right: '35px'}
        }
    });
    STORES_MAP.controls.add(zoomControl);
    STORES_MAP.behaviors.disable('scrollZoom');

    var isMobile = {
        Android: function () {
            return navigator.userAgent.match(/Android/i);
        },
        BlackBerry: function () {
            return navigator.userAgent.match(/BlackBerry/i);
        },
        iOS: function () {
            return navigator.userAgent.match(/iPhone|iPad|iPod/i);
        },
        Opera: function () {
            return navigator.userAgent.match(/Opera Mini/i);
        },
        Windows: function () {
            return navigator.userAgent.match(/IEMobile/i);
        },
        any: function () {
            return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows());
        }
    };

    if (isMobile.any()) {
        STORES_MAP.behaviors.disable('drag');
    }

    // Показываем склады текущего города
    if (initialCity) {
        redrawObjectsStoresMap(initialCity);
    }
}