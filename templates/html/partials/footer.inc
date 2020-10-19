<?php
use Application\Url;
global $AUTH0;
?>
<footer>
    <a href="https://bloomington.in.gov/ureport"       class="button">Feedback</a>
    <a href="https://bloomington.in.gov/about-inroads" class="button">About</a>
    <div class="open-data">
        <h2>Open Data</h2>
        <a href="https://groups.google.com/forum/#!forum/cob_road_closings">Email subscription</a>,
        <a href="https://www.google.com/calendar/embed?src=<?= GOOGLE_CALENDAR_ID ?>&amp;ctz=America/New_York">Google Calendar</a>,
        <a href="https://www.google.com/calendar/ical/<?= GOOGLE_CALENDAR_ID ?>/public/basic.ics">iCal</a>,
        <?php
            $url = new Url(Url::current_url(BASE_HOST));
            $url->format = 'json';
            echo "<a href=\"$url\">JSON</a>, ";

            $url->format = 'csv';
            echo "<a href=\"$url\">CSV</a>";
        ?>
    </div>
    <div class="open-source">
        <h2>Open Source</h2>
        <a href="https://github.com/City-of-Bloomington/inRoads">See the source code on Github</a>
    </div>
    <a href="https://bloomington.in.gov/web">Terms &amp; Conditions</a>
    <div class="copyright">&copy;<?= date('Y') ?> The City of Bloomington</div>
    <?php
        if (isset($AUTH0)) {
            echo "
            <div>
                <a width=\"150\" height=\"50\" href=\"https://auth0.com/?utm_source=oss&utm_medium=gp&utm_campaign=oss\" alt=\"Single Sign On & Token Based Authentication - Auth0\">
                    <img width=\"150\" height=\"50\" alt=\"JWT Auth for open source projects\" src=\"//cdn.auth0.com/oss/badges/a0-badge-dark.png\"/>
                </a>
            </div>
            ";
        }
    ?>
</footer>
