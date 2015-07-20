(function (){
    "use strict";
    var appHeader        = document.querySelector("body > header > .container"),
        htmlMain         = document.querySelector("main"),
        filterPanel      = document.getElementById("filterPanel"),
        listPanel        = document.getElementById("panel-two"),
        initMobileUi     = function () {
            var showFilterButton = document.createElement("div"),
                hideFilterButton = document.createElement("div");

            showFilterButton.setAttribute("class", "showFilterButton");
            showFilterButton.setAttribute("id", "showFilterButton");
            showFilterButton.textContent = "Change filters";
            showFilterButton.addEventListener("click", showFilters);
            appHeader.insertBefore(showFilterButton, appHeader.lastChild);

            hideFilterButton.setAttribute("class", "hideFilterButton");
            hideFilterButton.setAttribute("id", "hideFilterButton");
            hideFilterButton.textContent = "Close filters";
            hideFilterButton.addEventListener("click", hideFilters);
            filterPanel.insertBefore(hideFilterButton, filterPanel.firstChild);

        },
        showFilters = function (e) {
            var button = e.target;
            htmlMain.setAttribute("class", "filters-enabled midAnimation");

            window.setTimeout(function () {
                htmlMain.setAttribute("class", "filters-enabled");
            }, 250);
        },
        hideFilters      = function (e) {
            var button = e.target;
            htmlMain.setAttribute("class", "midAnimation");

            window.setTimeout(function () {
                htmlMain.removeAttribute("class");
            }, 250);
        };

    // Initialize the HTML element
    initMobileUi();
    document.querySelector("html").setAttribute("class", "js");
})();
