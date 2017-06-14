<?php
use Blossom\Classes\Url;
?>
<footer>
    <a href="https://bloomington.in.gov/webmaster" class="button feedback">Feedback</a>
    <div class="open-data">
        <h2>Open Data</h2>
        <a class="footer-links" href="https://groups.google.com/forum/#!forum/cob_road_closings">Email subscription</a>,
        <a class="footer-links" href="https://www.google.com/calendar/embed?src=<?= GOOGLE_CALENDAR_ID ?>&amp;ctz=America/New_York">Google Calendar</a>,
        <a class="footer-links" href="https://www.google.com/calendar/ical/<?= GOOGLE_CALENDAR_ID ?>/public/basic.ics">iCal</a>,
        <?php
            $url = new Url(Url::current_url(APPLICATION_HOST));
            $url->format = 'json';
            echo "<a href=\"$url\" class=\"footer-links\">JSON</a>, ";

            $url->format = 'csv';
            echo "<a href=\"$url\" class=\"footer-links\">CSV</a>";
        ?>
    </div>
    <div class="open-source">
        <h2>Open Source</h2>
        <a class="footer-links" href="https://github.com/City-of-Bloomington/inRoads">See the source code on Github</a>
    </div>
    <a class="footer-links disclaimer" title="Terms and Conditions" href="http://bloomington.in.gov/documents/viewDocument.php?document_id=709">Terms &amp; Conditions</a>
    <div class="copyright">&copy;<?= date('Y') ?> The City of Bloomington</div>

</footer>
