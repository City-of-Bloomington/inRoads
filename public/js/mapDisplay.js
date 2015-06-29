"use strict";
var MAPDISPLAY = {
    map: new ol.Map({
        target: 'map',
        view: new ol.View({
            center: ol.proj.transform([PHP.DEFAULT_LONGITUDE, PHP.DEFAULT_LATITUDE], 'EPSG:4326', 'EPSG:3857'),
            zoom: 14
        })
    }),
    /**
     * The features are not added to a regular vector layer/source,
     * but to a feature overlay which holds a collection of features.
     * This collection is passed to the modify and also the draw
     * interaction, so that both can add or modify features.
     */
    featureOverlay: new ol.FeatureOverlay(),
    wktFormatter: new ol.format.WKT(),
    styles: {
        default: new ol.style.Style({
            stroke: new ol.style.Stroke({color:'#ee0000', width:8})
        }),
        hover: new ol.style.Style({
            stroke: new ol.style.Stroke({color:'#ff0000', width:16})
        }),
        selected: new ol.style.Style({
            stroke: new ol.style.Stroke({color:'#0000ff', width:8})
        })
    },
    marker: new ol.Overlay({
        element: document.getElementById('marker'),
        positioning: 'bottom-center'
    }),
    /**
     * Adds features to the map
     *
     * This function handles rezooming and centering the map
     * after adding the features.
     *
     * @param array features
     */
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
    },
    /**
     * Gets a reference to a feature in the map
     *
     * @param string event_id
     */
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
    currentlySelectedEventId: null,
    selectEvent: function (event_id, feature) {
        var event  = document.getElementById(event_id),
            coords = [];

        event.classList.add('current');
        MAPDISPLAY.currentlySelectedEventId = event_id;

        if (!feature) {
            feature = MAPDISPLAY.findFeature(event_id);
            if (!feature) {
                return;
            }
        }
        feature.setStyle(MAPDISPLAY.styles.selected);

        coords = ol.extent.getCenter(feature.getGeometry().getExtent());
        MAPDISPLAY.marker.setPosition(coords);
    },
    deselectEvents: function () {
        var event   = document.querySelector('#events .current'),
            feature = {};

        if (event) {
            event.classList.remove('current');

            feature = MAPDISPLAY.findFeature(event.getAttribute('id'));
            if (feature) {
                feature.setStyle(null);
            }
        }

        MAPDISPLAY.currentlySelectedEventId = null;
        MAPDISPLAY.marker.setPosition([0,0]);
    },
    highlightEvent: function (e) {
        var id = e.currentTarget.getAttribute('id'),
             f = MAPDISPLAY.findFeature(id);

        if (f && id != MAPDISPLAY.currentlySelectedEventId) {
            f.setStyle(MAPDISPLAY.styles.hover);
        }
    },
    unhighlightEvent: function (e) {
        var id = e.currentTarget.getAttribute('id'),
             f = MAPDISPLAY.findFeature(id);

        if (f && id != MAPDISPLAY.currentlySelectedEventId) {
            f.setStyle(null);
        }
    },
    /**
     * Responds to clicks on the map
     *
     * Draws the popup bubble for any feature that's clicked
     */
    handleMapClick: function (e) {
        var feature = MAPDISPLAY.map.forEachFeatureAtPixel(e.pixel, function (feature, layer) { return feature; });

        MAPDISPLAY.deselectEvents();
        if (feature && feature.event_id) {
            MAPDISPLAY.selectEvent(feature.event_id, feature);
        }
    },
    handleListClick: function (e) {
        e.preventDefault();
        MAPDISPLAY.deselectEvents();
        MAPDISPLAY.selectEvent(e.currentTarget.getAttribute('id'));
        return false;
    }
};

MAPDISPLAY.map.addOverlay(MAPDISPLAY.marker);
MAPDISPLAY.featureOverlay.setMap(MAPDISPLAY.map);
MAPDISPLAY.featureOverlay.setStyle(MAPDISPLAY.styles.default);
MAPDISPLAY.map.on('click', MAPDISPLAY.handleMapClick);

// Load any initial data the webpage specifies.
(function () {
    var events = document.querySelectorAll('#events a.panelItem'),
        len    = 0,
        i      = 0,
        id        = '',
        f         = 0,
        geography = '',
        features  = [];

    // Maplayers are defined in site_config.
    // We have to remember to write the PHP variables out as Javascript,
    // so we can reference them here.
    // See: blocks/html/events/map.inc
    len = PHP.maplayers.length;
    for (i=0; i<len; i++) {
        MAPDISPLAY.map.addLayer(new ol.layer.Tile({
            source: new ol.source[PHP.maplayers[i].source](PHP.maplayers[i].options)
        }));
    }

    len = events.length;
    for (i=0; i<len; i++) {
        id = events[i].getAttribute('id');
        events[i].addEventListener('click', MAPDISPLAY.handleListClick);

        geography = events[i].querySelector('.geography');
        if (geography && geography.innerHTML) {
            f = features.length;
            features[f] = MAPDISPLAY.wktFormatter.readFeature(geography.innerHTML);
            features[f].getGeometry().transform('EPSG:4326', 'EPSG:3857');
            features[f].event_id = id;

            // Override the event link and have it open the popup on the map
            document.getElementById(id).addEventListener('mouseenter', MAPDISPLAY.highlightEvent);
            document.getElementById(id).addEventListener('mouseleave', MAPDISPLAY.unhighlightEvent);
        }
    }
    if (features.length) { MAPDISPLAY.setFeatures(features); }
}());
