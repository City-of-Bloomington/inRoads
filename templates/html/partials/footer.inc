<footer>
    <a href="#" class="button feedback">Feedback</a>
    <div class="open-data">
        <h2>Open Data</h2>
        <a class="footer-links" href="https://groups.google.com/forum/#!forum/cob_road_closings">Email subscription</a>
        <a class="footer-links" href="https://www.google.com/calendar/embed?src=<?= GOOGLE_CALENDAR_ID ?>&ctz=America/New_York">Google Calendar</a>
        <a class="footer-links" href="https://www.google.com/calendar/ical/<?= GOOGLE_CALENDAR_ID ?>/public/basic.ics">iCal</a>
        <a class="footer-links" href="https://www.google.com/calendar/feeds/<?= GOOGLE_CALENDAR_ID ?>/public/basic">XML</a>
    </div>
    <div class="open-source">
        <h2>Open Source</h2>
        <a class="github-button" href="https://github.com/City-of-Bloomington/road_closings" data-style="mega" data-count-href="/City-of-Bloomington/road_closings/watchers" data-count-api="/repos/City-of-Bloomington/road_closings#subscribers_count" data-count-aria-label="# watchers on GitHub" aria-label="Watch City-of-Bloomington/road_closings on GitHub">Watch</a>
    </div>
    <a class="footer-links disclaimer" title="Terms and Conditions" href="http://bloomington.in.gov/documents/viewDocument.php?document_id=709">Terms &amp; Conditions</a>
    <div class="copyright">&copy; <?= date('Y') ?> The City of Bloomington</div>

    <?php
        global $startTime;

        # Calculate the process time
        $endTime = microtime(1);
        $processTime = $endTime - $startTime;
        echo "<!-- Process Time: $processTime -->";
    ?>
</footer>
<?php
	if (   isset($this->assets['scripts'])) {
		foreach ($this->assets['scripts'] as $url) {
			echo "<script type=\"text/javascript\" src=\"$url\"></script>\n";
		}
	}
	if (   isset($this->assets['stylesheets'])) {
		foreach ($this->assets['stylesheets'] as $url) {
			echo "<link rel=\"stylesheet\" href=\"$url\" />\n";
		}
	}
?>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-9224894-7', 'auto');
  ga('send', 'pageview');

</script>
<!-- Place this tag right after the last button or just before your close body tag. -->
<script async defer id="github-bjs" src="https://buttons.github.io/buttons.js"></script>
