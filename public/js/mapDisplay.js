"use strict";
var MAPDISPLAY = {
    map: new ol.Map({
        target: 'map',
        layers: [ new ol.layer.Tile({source: new ol.source.OSM()}) ],
        view: new ol.View({
            center: ol.proj.transform([PHP.DEFAULT_LONGITUDE, PHP.DEFAULT_LATITUDE], 'EPSG:4326', 'EPSG:3857'),
            zoom: 14
        })
    }),
    popup: new ol.Overlay({
        element: document.getElementById('popup'),
        positioning: 'bottom-center'
    }),
    displayPopup: function (e) {
        var feature = MAPDISPLAY.map.forEachFeatureAtPixel(e.pixel, function (feature, layer) { return feature; });
        if (feature) {
            var coords = ol.extent.getCenter(feature.getGeometry().getExtent());
            MAPDISPLAY.popup.getElement().innerHTML = '<p>Feature Clicked</p>';
            MAPDISPLAY.popup.setPosition(coords);
        }
        else {
            MAPDISPLAY.popup.getElement().innerHTML = '';
            MAPDISPLAY.popup.setPosition([0,0]);
        }
    },
    /**
     * The features are not added to a regular vector layer/source,
     * but to a feature overlay which holds a collection of features.
     * This collection is passed to the modify and also the draw
     * interaction, so that both can add or modify features.
     */
    featureOverlay: new ol.FeatureOverlay({
        style: new ol.style.Style({
            fill:   new ol.style.Fill({color: 'rgba(255,255,255,0.2)'}),
            stroke: new ol.style.Stroke({color:'#ff0000', width:8}),
            image:  new ol.style.Circle({radius:7, fill: new ol.style.Fill({color:'#ff0000'})})
        })
    }),
    wktFormatter: new ol.format.WKT(),
    setFeatures: function (features) {
        var extent = ol.extent.createEmpty(),
            len = features.length,
            i   = 0;

        for (i=0; i<len; i++) {
            ol.extent.extend(extent, features[i].getGeometry().getExtent());
        }
        MAPDISPLAY.featureOverlay.setFeatures(new ol.Collection(features));
        MAPDISPLAY.map.getView().fitExtent(extent, MAPDISPLAY.map.getSize());
    },
    /**
     * Reads features out of the FeatureOverlay and converts them to WSG84 WKT
     *
     * @return string
     */
    getWkt: function () {
        var clones    = [],
            features  = MAPDISPLAY.featureOverlay.getFeatures().getArray(),
            len = features.length,
            i   = 0,
            wkt = '';

        if (len) {
            for (i=0; i<len; i++) {
                clones[i] = features[i].clone();
                clones[i].getGeometry().transform('EPSG:3857', 'EPSG:4326');
            }
            wkt = MAPDISPLAY.wktFormatter.writeFeatures(clones);
        }
        return wkt;
    }
};
MAPDISPLAY.map.addOverlay(MAPDISPLAY.popup);
MAPDISPLAY.featureOverlay.setMap(MAPDISPLAY.map);
MAPDISPLAY.map.on('click', MAPDISPLAY.displayPopup);

// Load any initial data the webpage specifies.
//if (PHP.mapdata) { MAPDISPLAY.loadWkt(PHP.mapdata); }
(function () {
    var events = document.querySelectorAll('.geography'),
        len    = events.length,
        i      = 0,
        id        = '',
        f         = 0,
        geography = '',
        features  = [];

    for (i=0; i<len; i++) {
        geography = events[i].innerHTML;
        if (geography) {
            id = events[i].parentElement.getAttribute('id');

            f = features.length;
            features[f] = MAPDISPLAY.wktFormatter.readFeature(geography);
            features[f].getGeometry().transform('EPSG:4326', 'EPSG:3857');
        }
    }
    if (features.length) { MAPDISPLAY.setFeatures(features); }
}());
