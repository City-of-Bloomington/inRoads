<footer>
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

  ga('create', 'UA-9224894-6', 'auto');
  ga('send', 'pageview');

</script>