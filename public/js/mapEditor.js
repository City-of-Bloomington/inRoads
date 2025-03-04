"use strict";
(function () {
    var geography = document.getElementById('geography').value,
        features  = [];
    if (geography) {
        features[0] = MAPDISPLAY.wktFormatter.readFeature(geography, {
               dataProjection: 'EPSG:4326',
            featureProjection: 'EPSG:3857'
        });
    }
    if (features.length) { MAPDISPLAY.setFeatures(features); }
}());

MAPDISPLAY.modify = new ol.interaction.Modify({ source: MAPDISPLAY.featureSource });
MAPDISPLAY.map.addInteraction(MAPDISPLAY.modify);

MAPDISPLAY.draw = {};
MAPDISPLAY.activateDrawMode = function (geometryType) {
    MAPDISPLAY.map.removeInteraction(MAPDISPLAY.draw);

    MAPDISPLAY.draw = new ol.interaction.Draw({
        source: MAPDISPLAY.featureSource,
          type: geometryType
    });
    MAPDISPLAY.map.addInteraction(MAPDISPLAY.draw);
}

MAPDISPLAY.toggleDrawMode = function (e) {
    var button = e.target,
        currentButton = document.querySelector('#mapTools button.current');

    if (button.classList.contains('current')) {
        MAPDISPLAY.map.removeInteraction(MAPDISPLAY.draw);
        MAPDISPLAY.draw = {};
        button.classList.remove('current');
    }
    else {
        if (currentButton) { currentButton.classList.remove('current'); }
        MAPDISPLAY.activateDrawMode(button.getAttribute('id'));
        button.classList.add('current');
    }
}
document.getElementById('LineString').addEventListener('click', MAPDISPLAY.toggleDrawMode);
document.getElementById('Point')     .addEventListener('click', MAPDISPLAY.toggleDrawMode);

document.getElementById('clearFeaturesButton').addEventListener('click', function() {
    document.getElementById('geography').value = '';
    MAPDISPLAY.featureSource.clear();
});

document.getElementById('eventUpdateForm').addEventListener('submit', function () {
    document.getElementById('geography').value = MAPDISPLAY.getWkt();
}, false);
