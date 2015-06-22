<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<?php $this->_include('partials/head.inc'); ?>
<body>
	<?php $this->_include('partials/header.inc'); ?>
	<main>
        <div class="container two-column">
            <?php $this->_include('partials/panel-one.inc'); ?>
			<div class="errorPanel">
				<?php include BLOSSOM.'/errorMessages.php'; ?>
			</div>
            <div class="panel" id="content-panel">


                <?php echo $this->includeBlocks(); ?>
				<a class="mapLinks disclaimer" title="Terms and Conditions" href="http://bloomington.in.gov/documents/viewDocument.php?document_id=709">Terms &amp; Conditions</a>
				<a class="mapLinks calendar" title="iCal Feed" href="https://www.google.com/calendar/ical/bloomington.in.gov_rm22gcl2hatcmjk0boh75d6pec@group.calendar.google.com/public/basic.ics">iCal</a>
				<a class="mapLinks feed" title="XML Feed" href="https://www.google.com/calendar/feeds/bloomington.in.gov_rm22gcl2hatcmjk0boh75d6pec@group.calendar.google.com/public/basic">XML</a>
            </div>
        </div>
	</main>
	<?php $this-> _include('partials/footer.inc'); ?>
</body>
</html>
<?php $this->_include('partials/garbageCollection.inc'); ?>
