"use strict";

(function () {
    var form     = document.querySelector('#searchPanel form'),
        nav      = document.querySelector('#searchPanel nav'),
        selected = nav.querySelector('selected'),
        custom   = document.createElement('a');

    form.style.display = 'none';

    custom.innerHTML = 'Custom';
    if (!selected) {
        custom.setAttribute('class', 'selected');
        selected = custom;
    }
    nav.appendChild(custom);

    nav.outerHTML = '<div class="dropdown"><span class="title">' + selected.innerHTML + '</span><nav>' + nav.innerHTML + '</nav></div>';

    document.querySelector('#searchPanel nav').lastChild.addEventListener('click', function() {
        form.style.display = 'block';
    });
})();