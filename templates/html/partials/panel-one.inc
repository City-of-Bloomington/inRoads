<?php
use Blossom\Classes\View;
?>
<section id="panel-one">
    <?= $this->includeBlocks('panel-one'); ?>
    <footer>
        <a class="mapLinks disclaimer" title="Terms and Conditions" href="http://bloomington.in.gov/documents/viewDocument.php?document_id=709">Terms &amp; Conditions</a>
        <a class="mapLinks gCalendar" title="Google Calendar" href="https://www.google.com/calendar/embed?src=<?= GOOGLE_CALENDAR_ID ?>&ctz=America/New_York">Google Calendar</a>
        <a class="mapLinks calendar" title="iCal Feed" href="https://www.google.com/calendar/ical/<?= GOOGLE_CALENDAR_ID ?>/public/basic.ics">iCal</a>
        <a class="mapLinks feed" title="XML Feed" href="https://www.google.com/calendar/feeds/<?= GOOGLE_CALENDAR_ID ?>/public/basic">XML</a>
        <div>TODO: Feedback button</div>
    </footer>
</section>
