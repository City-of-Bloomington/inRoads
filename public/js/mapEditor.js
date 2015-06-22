"use strict";
(function () {
    var geography = document.getElementById('geography').value,
        features  = [];
    if (geography) {
        features[0] = MAPDISPLAY.wktFormatter.readFeature(geography);
        features[0].getGeometry().transform('EPSG:4326', 'EPSG:3857');
    }
    if (features.length) { MAPDISPLAY.setFeatures(features); }
}());

/**
 * the SHIFT key must be pressed to delete vertices, so
 * that new vertices can be drawn at the same position
 * of existing vertices
 */
MAPDISPLAY.modify = new ol.interaction.Modify({
    features: MAPDISPLAY.featureOverlay.getFeatures(),
    deleteCondition: function(e) {
        return ol.events.condition.shiftKeyOnly(e)
            && ol.events.condition.singleClick(e);
    }
});
MAPDISPLAY.map.addInteraction(MAPDISPLAY.modify);

MAPDISPLAY.draw = {};
MAPDISPLAY.activateDrawMode = function (geometryType) {
    MAPDISPLAY.map.removeInteraction(MAPDISPLAY.draw);

    MAPDISPLAY.draw = new ol.interaction.Draw({
        features: MAPDISPLAY.featureOverlay.getFeatures(),
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

document.getElementById('clearFeaturesButton').addEventListener('click', function(e) {
    document.getElementById('geography').value = '';
    MAPDISPLAY.featureOverlay.getFeatures().clear();
});



document.getElementById('eventUpdateForm').addEventListener('submit', function () {
    document.getElementById('geography').value = MAPDISPLAY.getWkt();
}, false);
