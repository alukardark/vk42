'use strict';
/* global f, VK, Ajax */

let OAuth = (() => {
    let AUTH_URLS = {};

    let popup = (url, title = 'Авторизация') => {
        window.open(url, title, "width=600,height=600,left=0,right=0,resizable=yes,scrollbars=yes");
    };

    let auth = (sender, e, socnet) => {
        popup(AUTH_URLS[socnet]);
    };

    let setVKAuthUrl = () => {
        let onSuccess = (response) => {
            AUTH_URLS['VK'] = response.urls.VK;
            AUTH_URLS['FB'] = response.urls.FB;
            AUTH_URLS['OK'] = response.urls.OK;
        };

        let data = {ACTION: 'AUTH_URLS'};
        Ajax.send('/ajax/ajax_oauth.php', data, $("body"), onSuccess);
    };

    let bindActions = () => {

    };

    let init = () => {
        console.log('init oauth');
        //bindActions();
        setVKAuthUrl();

        window.OAuth = OAuth;
    };

    return {
        init, auth
    };
})();

$(() => OAuth.init());