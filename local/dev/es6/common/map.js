'use strict';

/* global google */

export default (function () {
    let $map = $("#map");
    let map = undefined;
    let marker = undefined;

    let init_map = function () {
        var default_lat = $map.attr("data-default-lat");
        var default_lng = $map.attr("data-default-lng");
        var default_center_lat = default_lat;
        var default_center_lng = default_lng * 0.99997;
        var styles = [{
                stylers: [{
                    }]
            }];
        var styledMap = new google.maps.StyledMapType(styles, {
            name: "Styled Map"
        });

        var mapProp = {
            center: new google.maps.LatLng(default_center_lat, default_center_lng),
            zoom: 17,
            panControl: true,
            zoomControl: true,
            mapTypeControl: false,
            scaleControl: true,
            streetViewControl: false,
            overviewMapControl: false,
            rotateControl: true,
            scrollwheel: false,
            //mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        map = new google.maps.Map(document.getElementById("map"), mapProp);
        marker = new google.maps.Marker({
            position: new google.maps.LatLng(default_lat, default_lng),
            animation: google.maps.Animation.DROP,
            icon: '/images/map_point.png'
        });
        marker.setMap(map);
        map.mapTypes.set('map_style', styledMap);
        map.setMapTypeId('map_style');
        map.panTo(new google.maps.LatLng(default_center_lat, default_center_lng));
        google.maps.event.addListener(marker, "click", function () {

        });
        //$();
    };

    let map_relocate = function (lat, lng) {
        var center_lat = lat;
        var center_lng = lng * 0.99997;
        marker.setPosition(new google.maps.LatLng(lat, lng));
        map.panTo(new google.maps.LatLng(center_lat, center_lng));
    };

    let toggle = function (self) {
        var $self = $(self);
        var id = $self.attr('data-switch');
        var lat = $self.attr('data-lat');
        var lng = $self.attr('data-lng');
        $(".switch_content").css({"display": "none"});
        $("#swit_content-" + id).fadeIn();
        $(".switcher .item").removeClass("active");
        $self.addClass("active");
        map_relocate(lat, lng);
    };

    let init = function () {
        console.log('init map');
        google.maps.event.addDomListener(window, 'load', init_map);
    };

    return {
        init: init
    };
})();