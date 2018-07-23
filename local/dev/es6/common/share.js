'use strict';

/* global f */

export default (function () {
    let service, url, share_url, share_image, share_title, share_text;
    let fb_app_id = '996945340386558';

    let setUrl = {
        vk: function () {
            url = 'https://vk.com/share.php?';
            if (!f.isEmpty(share_url)) url += 'url=' + encodeURIComponent(share_url);
            if (!f.isEmpty(share_image)) url += '&image=' + encodeURIComponent(share_image);
            if (!f.isEmpty(share_title)) url += '&title=' + encodeURIComponent(share_title);
            if (!f.isEmpty(share_text)) url += '&description=' + encodeURIComponent(share_text);
            url += '&noparse=true&no_vk_links=1';
        },
        ok: function () {
            url = 'http://www.ok.ru/dk?st.cmd=addShare&st.s=1';
            if (!f.isEmpty(share_url)) url += '&st._surl=' + encodeURIComponent(share_url);
            if (!f.isEmpty(share_title)) url += '&st.comments=' + encodeURIComponent(share_title);
        },
        fb: function () {
            url = 'http://www.facebook.com/dialog/feed?app_id=' + fb_app_id;
            if (!f.isEmpty(share_url)) url += '&link=' + encodeURIComponent(share_url);
            if (!f.isEmpty(share_image)) url += '&picture=' + encodeURIComponent(share_image);
            if (!f.isEmpty(share_title)) url += '&name=' + encodeURIComponent(share_title);
            if (!f.isEmpty(share_title)) url += '&caption=' + encodeURIComponent(share_title);
            if (!f.isEmpty(share_text)) url += '&description=' + encodeURIComponent(share_text);
        },
        tw: function () {
            url = 'http://twitter.com/share?';
            if (!f.isEmpty(share_url)) url += '&counturl=' + encodeURIComponent(share_url);
            if (!f.isEmpty(share_url)) url += '&url=' + encodeURIComponent(share_url);
            if (!f.isEmpty(share_title)) url += 'text=' + encodeURIComponent(share_title);
        },
        mr: function () {
            url = 'http://connect.mail.ru/share?';
            if (!f.isEmpty(share_url)) url += 'url=' + encodeURIComponent(share_url);
            if (!f.isEmpty(share_image)) url += '&imageurl=' + encodeURIComponent(share_image);
            if (!f.isEmpty(share_title)) url += '&title=' + encodeURIComponent(share_title);
            if (!f.isEmpty(share_text)) url += '&description=' + encodeURIComponent(share_text);
        },
        gp: function (purl, ptitle, pimg) {
            url = 'https://plus.google.com/share?';
            if (!f.isEmpty(share_url)) url += 'url=' + encodeURIComponent(share_url);
        }
    };

    let open = function (self) {
        let $self = $(self);
        let $parent = $self.parent();

        service = $self.attr("data-share-service");
        share_url = $parent.attr("data-share-url");
        share_image = $parent.attr("data-share-image");
        share_title = $parent.attr("data-share-title");
        share_text = $parent.attr("data-share-text");

        let host = 'http://' + window.location.hostname;

        setUrl[service]();
        window.open(url, '', 'toolbar=0,status=0,width=626,height=436');
    };

    let init = function () {
        console.log('init share');
    };

    return {
        init: init,
        open: open
    };
})();