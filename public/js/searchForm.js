"use strict";

(function () {
    var filterPanel     = document.getElementById('filterPanel'),
        dateFieldSet    = document.getElementById('dateFieldSet'),
        selectorCurrent = filterPanel.querySelector('.nav-dropdown-current'),
        selectorOptions = filterPanel.querySelector('.nav-dropdown-options'),
        selected        = selectorOptions.querySelector('a.current'),
        chooseDates     = document.createElement('a'),
        dropDownOptions = document.querySelector('.nav-dropdown-options'),
        toggleOptions   = function() {
            if (selectorCurrent.getAttribute('aria-expanded') === 'true') {
                selectorCurrent.setAttribute('aria-expanded', 'false');
            }
            else {
                selectorCurrent.setAttribute('aria-expanded', 'true');
            }
        };

    dateFieldSet.style.display = 'none';
    selectorCurrent.setAttribute('aria-expanded', 'false');
    selectorCurrent.addEventListener('click', toggleOptions);
    dropDownOptions.style.position = 'absolute';

    chooseDates.innerHTML = selectorCurrent.innerHTML;
    if (!selected) {
        chooseDates.setAttribute('class', 'current');
        dateFieldSet.style.display = 'block';
        selected = chooseDates;
    }
    selectorOptions.appendChild(chooseDates);

    selectorOptions.lastChild.addEventListener('click', function() {
        dateFieldSet.style.display = 'block';
        toggleOptions();
    });
})();
