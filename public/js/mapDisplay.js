"use strict";
let MAPDISPLAY = {
    map: new ol.Map({
        target: 'map',
        view: new ol.View({
            center: ol.proj.transform([PHP.DEFAULT_LONGITUDE, PHP.DEFAULT_LATITUDE], 'EPSG:4326', 'EPSG:3857'),
            zoom: 14,
            minZoom: 1,
            maxZoom: 20
        })
    }),
    featureSource: new ol.source.Vector(),
    featureLayer:  {},
    wktFormatter: new ol.format.WKT(),
    styles: {
        default: {
            normal: new ol.style.Style({
                image:  new ol.style.Circle({fill:new ol.style.Fill(
                                            {color:[215, 0, 0, .6]}), radius:3}),
                stroke: new ol.style.Stroke({color:[215, 0, 0, .6], width:6, lineCap:'square'}),
                fill:   new ol.style.Fill(  {color:[215, 0, 0, .6]})
            }),
            hover: new ol.style.Style({
                image:  new ol.style.Circle({fill:new ol.style.Fill(
                                            {color:[215, 0, 0, 1]}), radius:4}),
                stroke: new ol.style.Stroke({color:[215, 0, 0, 1], width:8, lineCap:'square'}),
                fill:   new ol.style.Fill(  {color:[215, 0, 0, 1]})
            }),
            selected: new ol.style.Style({
                image:  new ol.style.Circle({fill:new ol.style.Fill(
                                            {color:[215, 0, 0, 1]}), radius:4}),
                stroke: new ol.style.Stroke({color:[215, 0, 0, 1], width:8, lineCap:'square'}),
                fill:   new ol.style.Fill(  {color:[215, 0, 0, 1]})
            })
        }
    },
    /**
     * Colors for features are defined in site_conf.
     * We need to remember to write the PHP variables out as Javascript,
     * so we can load them here
     * See: blocks/html/events/map.inc
     */
    loadEventTypeStyles: function (types) {
        let len = types.length,
            i   = 0,
            c   = [];

        for (i=0; i<len; i++) {
            c = types[i].color;

            MAPDISPLAY.styles[types[i].code] = {
                normal: new ol.style.Style({
                    image:  new ol.style.Circle({fill:new ol.style.Fill(
                                                {color:[c[0], c[1], c[2], .6]}), radius:3}),
                    stroke: new ol.style.Stroke({color:[c[0], c[1], c[2], .6], width:6, lineCap:'square'}),
                    fill:   new ol.style.Fill(  {color:[c[0], c[1], c[2], .6]}),
                }),
                hover: new ol.style.Style({
                    image:  new ol.style.Circle({fill:new ol.style.Fill(
                                                {color:[c[0], c[1], c[2], 1]}), radius:4}),
                    stroke: new ol.style.Stroke({color:[c[0], c[1], c[2], 1], width:8, lineCap:'square'}),
                    fill:   new ol.style.Fill(  {color:[c[0], c[1], c[2], 1]}),
                }),
                selected: new ol.style.Style({
                    image:  new ol.style.Circle({fill:new ol.style.Fill(
                                                {color:[c[0], c[1], c[2], 1]}), radius:4}),
                    stroke: new ol.style.Stroke({color:[c[0], c[1], c[2], 1], width:8, lineCap:'square'}),
                    fill:   new ol.style.Fill(  {color:[c[0], c[1], c[2], 1]}),
                }),
            }
        }
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
        let extent = ol.extent.createEmpty(),
            len    = features.length,
            i      = 0;

        for (i=0; i<len; i++) {
            ol.extent.extend(extent, features[i].getGeometry().getExtent());
        }
        MAPDISPLAY.featureSource.clear();
        MAPDISPLAY.featureSource.addFeatures(features);
        MAPDISPLAY.featureSource.changed();

        MAPDISPLAY.map.getView().fit(extent, MAPDISPLAY.map.getSize());
    },
    /**
     * Reads features out of the FeatureOverlay and converts them to WSG84 WKT
     *
     * @return string
     */
    getWkt: function () {
        let clones    = [],
            features  = MAPDISPLAY.featureSource.getFeatures(),
            len       = features.length,
            i         = 0,
            wkt       = '';

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
        let features = MAPDISPLAY.featureSource.getFeatures(),
            len      = features.length,
            i        = 0;

        for (i=0; i<len; i++) {
            if (features[i].event_id === event_id) {
                return features[i];
            }
        }
    },
    currentlySelectedEventId: null,
    selectEvent: function (event_id, feature) {
        let details = document.getElementById(event_id),
            coords  = [];

        MAPDISPLAY.deselectEventsExcept(event_id);

        if (feature) {
            coords = ol.extent.getCenter(feature.getGeometry().getExtent());

            MAPDISPLAY.currentlySelectedEventId = event_id;
            MAPDISPLAY.enableStyle(feature, 'selected');
            MAPDISPLAY.marker.setPosition(coords);
        }

        if (!details.getAttribute('open')) {
             details.setAttribute('open', '');
        }
    },
    /**
     * Close all details elements except for the ID passed in
     * @param string event_id
     */
    deselectEventsExcept: function (event_id) {
        let details = document.querySelectorAll('#eventsList details[open]'),
            feature = {},
            len     = details.length,
            i       = 0;

        for (i=0; i<len; i++) {
            if (details[i].getAttribute('id') != event_id) {
                feature = MAPDISPLAY.findFeature(details[i].getAttribute('id'));
                if (feature) { MAPDISPLAY.resetStyle(feature); }
                MAPDISPLAY.currentlySelectedEventId = null;
                MAPDISPLAY.marker.setPosition([0,0]);
                details[i].removeAttribute('open');
            }
        }
    },
    highlightEvent: function (e) {
        let id = e.currentTarget.getAttribute('id'),
             f = MAPDISPLAY.findFeature(id);

        if (f && id != MAPDISPLAY.currentlySelectedEventId) {
            MAPDISPLAY.enableStyle(f, 'hover');
        }
    },
    unhighlightEvent: function (e) {
        let id = e.currentTarget.getAttribute('id'),
             f = MAPDISPLAY.findFeature(id);

        if (f && id != MAPDISPLAY.currentlySelectedEventId) {
            MAPDISPLAY.resetStyle(f);
        }
    },
    enableStyle: function (feature, style) {
        if (feature.type) {
            feature.setStyle(MAPDISPLAY.styles[feature.type][style]);
        }
        else {
            feature.setStyle(MAPDISPLAY.styles.default[style]);
        }
    },
    resetStyle: function (feature) {
        if (feature.type) {
            feature.setStyle(MAPDISPLAY.styles[feature.type].normal);
        }
        else {
            feature.setStyle(null);
        }
    },
    /**

     * Responds to clicks on the map
     *
     * Draws the popup bubble for any feature that's clicked
     */
    handleMapClick: function (e) {
        let feature = MAPDISPLAY.map.forEachFeatureAtPixel(e.pixel, function (feature, layer) { return feature; });

        if (feature && feature.event_id) {
            MAPDISPLAY.selectEvent(feature.event_id, feature);
        }
    },
    /**
     * Responds to clicks in the events list
     *
     * Closes all the other details elements and highlights the selected
     * feature on the map.
     */
    handleListClick: function (e) {
        let details  = e.currentTarget,
            event_id = details.getAttribute('id'),
            feature  = MAPDISPLAY.findFeature(event_id);

        if (e.target.tagName == 'SUMMARY' || e.target.parentElement.tagName == 'SUMMARY') {
            e.preventDefault();
        }

        MAPDISPLAY.selectEvent(event_id, feature);
    }
};

MAPDISPLAY.map.addOverlay(MAPDISPLAY.marker);
MAPDISPLAY.map.on('click', MAPDISPLAY.handleMapClick);

// Load any initial data the webpage specifies.
(function () {
    let events          = [],
        len             = 0,
        i               = 0,
        id              = '',
        type            = '',
        f               = 0,
        geography       = '',
        features        = [],
        noscriptMessage = document.getElementById('pleaseEnableJavascript'),
        extractType     = function (classList) {
            let clen = classList.length,
                tlen = PHP.eventTypes.length,
                i    = 0,
                j    = 0;

            for (i=0; i<clen; i++) {
                for (j=0; j<tlen; j++) {
                    if (PHP.eventTypes[j].code == classList[i]) { return classList[i]; }
                }
            }
            return false;
        };

    // Remove the prompt to enable Javascript before we begin to render the map
    if (noscriptMessage) {
        noscriptMessage.parentElement.removeChild(noscriptMessage);
    }

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

    // Colors for features are also defined in site_conf.
    // We need to remember to write the PHP variables out as Javascript,
    // so we can load them here
    // See: blocks/html/events/map.inc
    if (PHP.eventTypes) {
        MAPDISPLAY.loadEventTypeStyles(PHP.eventTypes);
    }

    // Event data can be in either the eventList or the single event view.
    events = document.querySelectorAll('#eventsList details');
    if (!events.length) {
        events = document.querySelectorAll('#event article');
    }
    len = events.length;
    for (i=0; i<len; i++) {
        id   = events[i].getAttribute('id');
        type = extractType(events[i].classList);

        // Register the event listener for older, desktop-centric browsers
        events[i].addEventListener('click', MAPDISPLAY.handleListClick);
        // For newer, mobile-centric browsers, remove the event listener on small devices
        if (matchMedia("(max-width: 37.5rem)").matches) {
            events[i].removeEventListener('click', MAPDISPLAY.handleListClick);
        }

        geography = events[i].querySelector('.geography');
        if (geography && geography.innerHTML) {
            f = features.length;
            features[f] = MAPDISPLAY.wktFormatter.readFeature(geography.innerHTML, {
                   dataProjection: 'EPSG:4326',
                featureProjection: 'EPSG:3857'
            });
            features[f].event_id = id;
            if (type) {
                features[f].type = type;
                if (MAPDISPLAY.styles[type]) {
                    features[f].setStyle(MAPDISPLAY.styles[type].normal);
                }
            }

            // Override the event link and have it open the popup on the map
            document.getElementById(id).addEventListener('mouseenter', MAPDISPLAY.highlightEvent);
            document.getElementById(id).addEventListener('mouseleave', MAPDISPLAY.unhighlightEvent);
        }
    }
    if (features.length) { MAPDISPLAY.setFeatures(features); }
    MAPDISPLAY.featureLayer = new ol.layer.Vector({
        source: MAPDISPLAY.featureSource,
         style: MAPDISPLAY.styles.default.normal
    });
    MAPDISPLAY.map.addLayer(MAPDISPLAY.featureLayer);
}());
