"use strict";

(function () {
    var form     = document.querySelector('#searchPanel form'),
        selectorCurrent = document.querySelector('#searchPanel .nav-dropdown-current'),
        selectorOptions = document.querySelector('#searchPanel .nav-dropdown-options'),
        selected = selectorOptions.querySelector('a.current'),
        chooseDates   = document.createElement('a'),
        toggleOptions = function() {
            if (selectorCurrent.getAttribute('aria-expanded') === 'true') {
                selectorCurrent.setAttribute('aria-expanded', 'false');
            }
            else {
                selectorCurrent.setAttribute('aria-expanded', 'true');
            }
        };

    form.style.display = 'none';
    selectorCurrent.setAttribute('aria-expanded', 'false');
    selectorCurrent.addEventListener('click', toggleOptions);

    chooseDates.innerHTML = 'Choose Dates';
    if (!selected) {
        custom.setAttribute('class', 'current');
        selected = chooseDates;
    }
    selectorOptions.appendChild(chooseDates);

    document.querySelector('#searchPanel .nav-dropdown-options').lastChild.addEventListener('click', function() {
        form.style.display = 'block';
        toggleOptions();
    });
})();