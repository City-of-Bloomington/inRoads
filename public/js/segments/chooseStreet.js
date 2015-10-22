"use strict";
var STREET_CHOOSER = {
    popup: {},
    setStreet: function(street) {
        var regex       = /event_id=\d+/,
            event_param = regex.exec(document.location.href);

        STREET_CHOOSER.popup.close();
        document.location.href = PHP.BASE_URI + '/segments/update?' + event_param + ';street=' + street;
    }
};
document.getElementById('findStreetButton').addEventListener('click', function (e) {
    var street = document.getElementById('street').value;
    e.preventDefault();
    if (street) {
        STREET_CHOOSER.popup = window.open(
            PHP.BASE_URI + '/streets/search?popup=1;callback=STREET_CHOOSER.setStreet;street=' + document.getElementById('street').value,
            'popup',
            'menubar=no,location=no,status=no,toolbar=no,width=600,height=480,resizeable=yes,scrollbars=yes'
        );
        STREET_CHOOSER.popup.focus();
        // Make sure to pass the setLocation function to the popup window, so
        // that window can callback when the user chooses a place on the map.
        // See: /js/locations/mapChooser.js
        STREET_CHOOSER.popup.setStreet = STREET_CHOOSER.setStreet;
    }
    return false;
}, false);
