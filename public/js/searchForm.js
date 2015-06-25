"use strict";

(function () {
    var filterPanel     = document.querySelector('#filterPanel'),
        form            = document.querySelector('#filterPanel > form'),
        selectorCurrent = document.querySelector('#filterPanel .nav-dropdown-current'),
        selectorOptions = document.querySelector('#filterPanel .nav-dropdown-options'),
        selected        = selectorOptions.querySelector('a.current'),
        chooseDates     = document.createElement('a'),
        toggleOptions   = function() {
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
    dropDownOptions.style.position = 'absolute';

    chooseDates.innerHTML = 'Choose Dates';
    if (!selected) {
        chooseDates.setAttribute('class', 'current');
        selected = chooseDates;
    }
    selectorOptions.appendChild(chooseDates);

    document.querySelector('#filterPanel .nav-dropdown-options').lastChild.addEventListener('click', function() {
        form.style.display = 'block';
        toggleOptions();
    });
})();
