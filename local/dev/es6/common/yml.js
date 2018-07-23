'use strict';

/* global f, ymaps */

/*
 * Yandex map listener.
 * Объект-хелпер по работе с API Яндекс.Карт.
 * По сути, ждёт выполнения переданной функции только после полной загрузки API.
 */
window.yml = {
    ymapsIsLoaded: false,
    yamapsReadyGoFirstRun: true,    
    
    /*
     * Функция явно указывает, что API загружено.
     * Удобно использовать при подключении API через <script src="...">, передав
     * в адрес параметр onload=yml.loadedSetTrue
     */
    loadedSetTrue: () => {
        console.log('yamaps loaded');
        yml.ymapsIsLoaded = true;
    },
    
    /*
     * Главная функция.
     * Принимает коллбэк, который будет выполнен только того, как API будет загружено.
     * 
     * Если указан параметр includeYmapsReady, то функция выполнит коллбэк после
     * успешной загрузки указанного файла. При этом, пометка о загруженности API 
     * не ставится. Каждый вызов функции с данным параметром, будет загружать указанный
     * файл заново!
     * 
     * Если указан параметр mapId, то до момента загрузки API, контейнеру будет 
     * назначен класс-прелоадер "ymaps-loading". После загрузки API данный класс удаляется.
     * 
     * @param {function} callback - Коллбэк, который должен выполниться
     * @param {bool} includeYmapsReady - включать лт коллбэк в метод ready API. По умолчанию false
     * @param {string} scriptSrc - адрес подключения API. По умолчанию null.
     * @param {string} mapId - Id контейнера карты. Поумолчанию null.
     */
    readyGo: (callback, includeYmapsReady, scriptSrc, mapId) => {   
        // Значения по умолчанию
        callback = typeof callback !== 'undefined' ? callback : function(){};
        includeYmapsReady = typeof includeYmapsReady !== 'undefined' ? includeYmapsReady : false;
        scriptSrc = typeof scriptSrc !== 'undefined' ? scriptSrc : null;
        mapId = typeof mapId !== 'undefined' ? mapId : null;

        let map = document.getElementById(mapId);
        let loadingClass = 'ymaps-loading';

        if (map && !yml.ymapsIsLoaded) {
            yml._addClass(map, loadingClass);
        }

        let run = () => {
            if (typeof ymaps !== 'undefined') {
                yml._callbackRun(callback, includeYmapsReady, map, loadingClass);
                if (!yml.ymapsIsLoaded) {
                    yml.loadedSetTrue();
                }
            }
            else if (scriptSrc) {
                $.getScript(scriptSrc, (data, textStatus, jqxhr) => {
                    yml._callbackRun(callback, includeYmapsReady, map, loadingClass);
                    //yml.loadedSetTrue();
                });
            } 
            else {
                let ymapsTimer = setInterval(() => {
                    if (yml.ymapsIsLoaded) {
                        clearInterval(ymapsTimer);
                        yml._callbackRun(callback, includeYmapsReady, map, loadingClass);
                    }
                }, 10);
            }        
            yml.yamapsReadyGoFirstRun = false;
        };

        yml.yamapsReadyGoFirstRun
            ? window.document.onload = run()
            : run();
    },
    
    _callbackRun: (callback, includeYmapsReady, map, loadingClass) => {
        if (map) {
            yml._removeClass(map, loadingClass);
        }
        if (includeYmapsReady) {
            ymaps.ready(() => {
                callback();
            });
        } else {
            callback();
        }
    },

    _hasClass: (el, className) => {
        if (el.classList)
            return el.classList.contains(className);
        else
            return !!el.className.match(new RegExp('(\\s|^)' + className + '(\\s|$)'));
    },

    _addClass: (el, className) => {
        if (el.classList)
            el.classList.add(className);
        else if (!yml._hasClass(el, className))
            el.className += " " + className;
    },

    _removeClass: (el, className) => {
        if (el.classList)
            el.classList.remove(className);
        else if (yml._hasClass(el, className)) {
            let reg = new RegExp('(\\s|^)' + className + '(\\s|$)');
            el.className = el.className.replace(reg, ' ');
        }
    }
};