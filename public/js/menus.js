"use strict";
(function () {
    var closeMenus = function () {
        var openMenus = document.querySelectorAll('.menuLinks.open'),
            len = openMenus.length,
            i   = 0;
            for (i=0; i<len; i++) {
                openMenus[i].classList.remove('open');
                (function (i) {
                    setTimeout(function() { openMenus[i].classList.add('closed'); }, 300);
                })(i);
            }
        },
        launcherClick = function(e) {
            var menu      = e.target.parentElement.querySelector('.menuLinks');

            closeMenus();
            menu.classList.remove('closed');
            menu.classList.add('open');
            e.stopPropagation();
        },
        menus = document.querySelectorAll('.menuLauncher'),
        len   = menus.length,
        i = 0;

    for (i=0; i<len; i++) {
        menus[i].addEventListener('click', launcherClick);
    }
    document.addEventListener('click', closeMenus);
})();
