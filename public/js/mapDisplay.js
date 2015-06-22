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
    handleClick: function (e) {
        var feature = MAPDISPLAY.map.forEachFeatureAtPixel(e.pixel, function (feature, layer) { return feature; });

        MAPDISPLAY.closePopup();
        if (feature && feature.event_id) {
            MAPDISPLAY.displayPopup(feature.event_id, feature);
        }
    },
    findFeature: function (event_id) {
        var features = MAPDISPLAY.featureOverlay.getFeatures().getArray(),
        len = features.length,
        i   = 0;

        for (i=0; i<len; i++) {
            if (features[i].event_id === event_id) {
                return features[i];
            }
        }
    },
    displayPopup: function (event_id, feature) {
        var event  = document.getElementById(event_id),
            link   = {},
            coords = [];

        if (!feature) {
            feature = MAPDISPLAY.findFeature(event_id);
            if (!feature) {
                return;
            }
        }

        event.classList.add('current');

        link = document.createElement('a');
        link.setAttribute('href', event.getAttribute('href'));
        link.innerHTML = event.innerHTML;

        coords = ol.extent.getCenter(feature.getGeometry().getExtent());
        MAPDISPLAY.popup.getElement().appendChild(link);
        MAPDISPLAY.popup.setPosition(coords);
    },
    closePopup: function () {
        var event = document.querySelector('#events .current');
        if (event) {
            event.classList.remove('current');
        }
        MAPDISPLAY.popup.getElement().innerHTML = '';
        MAPDISPLAY.popup.setPosition([0,0]);
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
MAPDISPLAY.map.on('click', MAPDISPLAY.handleClick);

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
            f = features.length;
            features[f] = MAPDISPLAY.wktFormatter.readFeature(geography);
            features[f].getGeometry().transform('EPSG:4326', 'EPSG:3857');

            id = events[i].parentElement.getAttribute('id');
            if (id) {
                features[f].event_id = id;
                // Override the event link and have it open the popup on the map
                document.getElementById(id).addEventListener('click', function (e) {
                    e.preventDefault();
                    MAPDISPLAY.closePopup();
                    MAPDISPLAY.displayPopup(e.currentTarget.getAttribute('id'));
                    return false;
                });
            }
        }
    }
    if (features.length) { MAPDISPLAY.setFeatures(features); }
}());
