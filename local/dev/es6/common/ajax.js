'use strict';
/* global f */

export default (() => {
    let send = (url, data, $container = $("body"), onSucces = null, OnError = null, OnFatal = null, dataType = 'json', type = 'POST', OnComplete = null) => {

        $.ajax({
            type: type,
            url: url,
            dataType: dataType,
            data: data,
            beforeSend: (xhr) => {
                if ($container.hasClass("loading")) {
                    xhr.abort();
                    return false;
                }
                $container.addClass('loading');
            },
            success: (response) => {
                if (dataType === 'json') {
                    if (response.success && response.redirect && response.redirect.length) {
                        document.location.href = response.redirect;
                        return true;
                    }

                    if (response.success) {
                        if (onSucces !== null) {
                            onSucces(response.result);
                        } else {
                            console.log(response.result);
                        }
                    } else {
                        if (OnError !== null) {
                            OnError(response.result);
                        } else {
                            console.error(response);

                            if (!f.isEmpty(response) && !f.isEmpty(response.result) && !f.isEmpty(response.result.alert))
                            {
                                App.showAlert(response.result.alert);
                            }
                            //location.reload();
                        }
                    }
                } else {
                    if (onSucces !== null) {
                        onSucces(response);
                    } else {
                        console.log(response);
                    }
                }
            },
            error: (response) => {
                if (OnFatal !== null) {
                    OnFatal(response);
                } else {
                    console.error('FATAL: ' + response.statusText + response.responseText);
                    //location.reload();
                }
            },
            complete: () => {
                //console.log('ajax complete');

                if (OnComplete !== null) {
                    OnComplete();
                }
                $container.removeClass('loading');
            }
        });
        
    };

    let html = (url, data, $container, onSucces = null, OnError = null, OnFatal = null, type = 'POST', OnComplete = null) => {
        send(url, data, $container, onSucces, OnError, OnFatal, 'html', type, OnComplete);
    };

    let init = () => {
        console.log('init ajax');
    };

    return {
        init, send, html
    };
})();