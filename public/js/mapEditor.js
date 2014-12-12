"use strict";

MAPDISPLAY.loadWkt(document.getElementById('geography').value);

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

MAPDISPLAY.draw = new ol.interaction.Draw({
    features: MAPDISPLAY.featureOverlay.getFeatures(),
    type: 'LineString'
});
MAPDISPLAY.map.addInteraction(MAPDISPLAY.draw);


document.getElementById('eventUpdateForm').addEventListener('submit', function () {
    document.getElementById('geography').value = MAPDISPLAY.getWkt();
}, false);