(function (){
    "use strict";
    var appHeader        = document.querySelector("body > header > .container"),
        htmlMain         = document.querySelector("main"),
        panelOne         = document.getElementById("panel-one"),
        filterPanel      = document.getElementById("filterPanel"),
        listPanel        = document.getElementById("panel-two"),
        showFilterButton = document.createElement("button"),
        hideFilterButton = document.createElement("button"),

        suppressClick = function(e) {
            e.stopPropagation();
        },
        showFilters = function () {
            showFilterButton.setAttribute("aria-expanded", "true");
            htmlMain.setAttribute("class", "filters-displayed")
            window.setTimeout(function() {
                htmlMain.setAttribute("class", "filters-displayed filters-enabled midAnimation");
            }, 10)

            window.setTimeout(function () {
                htmlMain.setAttribute("class", "filters-enabled");
                document.addEventListener("click", hideFilters);
                panelOne.addEventListener("click", suppressClick);
                panelOne.focus();
            }, 250);
        },
        hideFilters = function () {
            showFilterButton.setAttribute("aria-expanded", "false")
            htmlMain.setAttribute("class", "filters-displayed midAnimation");

            window.setTimeout(function () {
                htmlMain.removeAttribute("class");
                document.removeEventListener("click", hideFilters);
                panelOne.removeEventListener("click", suppressClick);
                showFilterButton.focus();
            }, 250);
        };

    // Initialize the HTML element
    document.querySelector("html").setAttribute("class", "js");

    // Initialize the rest of the mobile UI
    showFilterButton.setAttribute("class", "showFilterButton");
    showFilterButton.setAttribute("id", "showFilterButton");
    showFilterButton.setAttribute("aria-controls", "panel-one");
    showFilterButton.setAttribute("aria-expanded", "false");
    showFilterButton.textContent = "Change filters";
    showFilterButton.addEventListener("click", showFilters);
    appHeader.insertBefore(showFilterButton, appHeader.lastChild);

    hideFilterButton.setAttribute("class", "hideFilterButton");
    hideFilterButton.setAttribute("id", "hideFilterButton");
    hideFilterButton.textContent = "Close filters";
    hideFilterButton.addEventListener("click", hideFilters);
    filterPanel.insertBefore(hideFilterButton, filterPanel.firstChild);

    if(matchMedia("(max-width: 59.375rem)").matches) {
        panelOne.tabIndex = -1;
        panelOne.setAttribute("role", "dialog");
    }

})();
