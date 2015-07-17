(function(){
    "use strict";
    var appHeader      = document.querySelector("body > header > .container"),
        mapFilterPanel = document.querySelector(".eventsMap > main > #panel-one"),
        filterPanel    = document.getElementById("filterPanel"),
        initMobileUi = function() {
            var showFilterButton = document.createElement("div"),
                hideFilterButton   = document.createElement("div");

            showFilterButton.setAttribute("class", "showFilterButton");
            showFilterButton.setAttribute("id", "showFilterButton");
            showFilterButton.textContent = "See more";
            showFilterButton.addEventListener("click", showFilters);
            appHeader.insertBefore(showFilterButton, appHeader.lastChild);

            hideFilterButton.setAttribute("class", "hideFilterButton");
            hideFilterButton.setAttribute("id", "hideFilterButton");
            hideFilterButton.textContent = "Close menu";
            hideFilterButton.addEventListener("click", hideFilters);
            filterPanel.insertBefore(hideFilterButton, filterPanel.firstChild);

            
        },
        showFilters              = function(e) {
            var button = e.target;
            mapFilterPanel.setAttribute("data-enabled", "true");
        },
        hideFilters              = function(e) {
            var button = e.target;
            mapFilterPanel.setAttribute("data-enabled", "false");
        };

    // Initialize the HTML element
    initMobileUi();
    document.querySelector("html").setAttribute("class", "js");
})();
