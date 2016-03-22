var mapsInit = (function ($) {

    var public_methods = {

        Init: function (locations) {
            var bounds = new google.maps.LatLngBounds();
            var mapOptions = {
                zoom: 6
            }

            var map = new google.maps.Map(document.getElementById("map"), mapOptions);
            for (i = 0; i < locations.length; i++) {

                var marker = new google.maps.Marker({
                    position: new google.maps.LatLng(locations[i][3], locations[i][4]),
                    title: locations[i][0]
                });
                bounds.extend(marker.position);
                var contentString = '<div id="content">';
                    if (locations[i][0]) {contentString = contentString + Translator.translate('name: ') + locations[i][0] + '</br>' }
                    if (locations[i][2]) {contentString = contentString + Translator.translate('phone: ') + locations[i][2] + '</br>' }
                    if (locations[i][1]) {contentString = contentString + Translator.translate('address: ') + locations[i][1] + '</br>' }

                    if ((locations[i][5]) !== undefined) {
                        contentString = contentString +
                        '<img src="' + locations[i][5] + '"/>' +
                        '<div>';
                    } else {
                        contentString = contentString +
                            '<div>';
                    }

                var infowindow = new google.maps.InfoWindow({
                    content: contentString
                });

                if ($(window).width() < 479) {
                    infowindow = new google.maps.InfoWindow({
                        content: contentString,
                        maxWidth: 150
                    });
                }

                mapsInit.makeInfoWindowEvent(map, infowindow, marker);

                marker.setMap(map);

                //Resize Function
                google.maps.event.addDomListener(window, 'load', mapsInit);
                google.maps.event.addDomListener(window, "resize", function() {
                    var center = map.getCenter();
                    google.maps.event.trigger(map, "resize");
                });
            }
            map.fitBounds(bounds);
        },
        makeInfoWindowEvent: function (map, infowindow, marker) {
            google.maps.event.addListener(marker, 'click', function () {
                infowindow.open(map, marker);
            });
        }
    }

    return public_methods;
})(jQuery);