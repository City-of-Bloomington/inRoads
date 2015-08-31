<?php
//---------------------------------------------------------------------
// Before we quit, handle any Garbage Collection that needs to be done
//---------------------------------------------------------------------
// If we're on a form, clean out any old data
if (isset($_SESSION['oldPostData'])) {
	unset($_SESSION['oldPostData']);
}

global $startTime;

# Calculate the process time
$endTime = microtime(1);
$processTime = $endTime - $startTime;
echo "<!-- Process Time: $processTime -->";
