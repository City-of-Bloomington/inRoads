"use strict";
(function () {
    const resultsDiv     = document.getElementById('streetResults'   ),
          searchForm     = document.getElementById('streetSearchForm'),
          segmentForm    = document.getElementById('segmentForm'     ),
          streetFrom     = document.getElementById('streetFrom'      ),
          streetTo       = document.getElementById('streetTo'        ),
          startLatitude  = document.getElementById('startLatitude'   ),
          startLongitude = document.getElementById('startLongitude'  ),
          endLatitude    = document.getElementById('endLatitude'     ),
          endLongitude   = document.getElementById('endLongitude'    );

    let searchStreets = function (query) {
            let req = new XMLHttpRequest();

            req.addEventListener('load', searchResultsHandler);
            req.open('GET', ADDRESS_SERVICE + '/streets?format=json;street=' + query);
            req.send();
        },

        /**
         * @param int street_id
         */
        intersectingStreets = function (street_id) {
            let req = new XMLHttpRequest();

            req.addEventListener('load', intersectingStreetsResultsHandler);
            req.open('GET', ADDRESS_SERVICE + '/streets/intersectingStreets/' + street_id + '?format=json');
            req.send();

        },

        /**
         * Calls the webservice then calls the callback with data from the response
         *
         * @param int      street_id_1
         * @param int      street_id_2
         * @param function callback     Function to send the intersection data
         */
        intersections = function (street_id_1, street_id_2, callback) {
            let req = new XMLHttpRequest();

            req.addEventListener('load', function (event) {
                let results = [];
                try {
                    results = JSON.parse(event.target.responseText);
                    if (results.length) { callback(results[0]); }
                }
                catch (e) { alert(e.message); }
            });
            req.open('GET', ADDRESS_SERVICE + '/streets/intersections?format=json'
                                            + ';street_id_1=' + street_id_1
                                            + ';street_id_2=' + street_id_2);
            req.send();
        },

        /**
         * Displays the street search results
         *
         * @param XMLHttpRequest.load event
         */
        searchResultsHandler = function (event) {
            let results  = [];

            try {
                results = JSON.parse(event.target.responseText);
                resultsDiv.innerHTML = results.length
                    ? resultsToHTML(results)
                    : 'No results found';

                attachStreetChoiceListener(resultsDiv.getElementsByTagName('A'));
            }
            catch (e) { resultsDiv.innerHTML = e.message; }
        },

        /**
         * Populates the From and To dropdowns
         *
         * Populates the select options using data from the json response
         * for a given street.
         *
         * @param XMLHttpRequest.load event
         */
        intersectingStreetsResultsHandler = function (event) {
            let results = [],
                options = '';
            try {
                results = JSON.parse(event.target.responseText);
                options = streetOptionsHtml(results);
            }
            catch (e) { alert(e.message); }

            streetFrom.innerHTML = options;
              streetTo.innerHTML = options;
        },

        attachStreetChoiceListener = function (elements) {
            let len, i;

            len = elements.length;
            for (i=0; i<len; i++) {
                elements[i].addEventListener('click', chooseStreet, false);
            }
        },

        resultsToHTML = function (results) {
            let html = '<ul>';

            results.forEach(function (street, i, array) {
                html += '<li><a href="#" data-street_id="' + street.id + '">'
                     +   street.streetName
                     +  '</a></li>';
            });
            html += '</ul>';
            return html;
        },

        streetOptionsHtml = function (results) {
            let html = '';

            results.forEach(function (street, i, array) {
                html += '<option data-street_id="' + street.id +'">'
                     +  street.streetName
                     +  '</option>';
            });
            return html;
        },

        chooseStreet = function(event) {
            const street_id   = event.target.dataset.street_id,
                  street_name = event.target.innerHTML;

            document.getElementById('street_id').value = street_id;
            document.getElementById('street'   ).value = street_name;
            segmentForm.getElementsByTagName('H3')[0].innerHTML = street_name;
            intersectingStreets(street_id);
            resultsDiv.innerHTML = '';
        },

        clearStreet = function() {
            document.getElementById('street_id').value = '';
            segmentForm.getElementsByTagName('H3')[0].innerHTML = '';
            streetFrom.innerHTML = '';
            streetTo  .innerHTML = '';

            startLatitude .value = '';
            startLongitude.value = '';
            endLatitude   .value = '';
            endLongitude  .value = '';
        },

        intersectionDataReady = function () {
            return startLatitude .value
                && startLongitude.value
                && endLatitude   .value
                && endLongitude  .value;
        },

        streetSearchSubmitHandler = function (event) {
            event.preventDefault();
            clearStreet();
            searchStreets(document.getElementById('streetQuery').value);
        },

        segmentFormSubmitHandler = function (event) {
            const street_id = document.getElementById('street_id').value,
                    from_id = streetFrom.options[streetFrom.selectedIndex].dataset.street_id,
                      to_id = streetTo  .options[streetTo  .selectedIndex].dataset.street_id;

            event.preventDefault();
            intersections(street_id, from_id, function (intersection) {
                startLatitude .value = intersection.latitude;
                startLongitude.value = intersection.longitude;
                if (intersectionDataReady()) { event.target.submit(); }

            });
            intersections(street_id, to_id, function (intersection) {
                endLatitude .value = intersection.latitude;
                endLongitude.value = intersection.longitude;
                if (intersectionDataReady()) { event.target.submit(); }
            });
        };

    searchForm .addEventListener('submit', streetSearchSubmitHandler, false);
    segmentForm.addEventListener('submit', segmentFormSubmitHandler,  false);
})();
