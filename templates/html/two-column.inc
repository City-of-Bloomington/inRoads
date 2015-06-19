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
				<a class="mapLinks calendar" title="Calendar Feed" href="http://bloomington.in.gov/documents/viewDocument.php?document_id=709">Calendar</a>
				<a class="mapLinks feed" title="JSON Feed" href="http://bloomington.in.gov/documents/viewDocument.php?document_id=709">Feed</a>
            </div>
        </div>
	</main>
	<?php $this-> _include('partials/footer.inc'); ?>
</body>
</html>
<?php $this->_include('partials/garbageCollection.inc'); ?>
