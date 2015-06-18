(function () {
    "use strict";
    var startTime = document.getElementById('startTime'),
        endTime   = document.getElementById('endTime'),
        allDay    = document.getElementById('allDay'),
        frequency = document.getElementById('frequency'),
        toggleTimes = function () {
            if (allDay.checked) {
                startTime.style.display = 'none';
                endTime  .style.display = 'none';
            }
            else {
                startTime.style.display = '';
                endTime  .style.display = '';
            }
        },
        activateFieldsets = function () {
            document.getElementById('DAILY')  .style.display = 'none';
            document.getElementById('WEEKLY') .style.display = 'none';
            document.getElementById('MONTHLY').style.display = 'none';

            if (frequency.value) {
                document.getElementById(frequency.value).style.display = 'block';
                document.getElementById('RRULE_END')    .style.display = 'block';
            }
            else {
                document.getElementById('RRULE_END').style.display = 'none';
            }
        };

    // The Event model will ignore the time component when allDay is checked
    // So, here, we only need hide and display the inputs.  There's no need to
    // remove the values from them.
    allDay   .addEventListener('click', toggleTimes);
    frequency.addEventListener('change', activateFieldsets);
    toggleTimes();
    activateFieldsets();
}());
