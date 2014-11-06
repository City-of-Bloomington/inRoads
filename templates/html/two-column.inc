<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<?php
	$this->layout = 'two-column';
	$this->_include('partials/header.inc');
?>
<body>
	<?php
		$this->_include('partials/banner.inc');
	?>
	<div id="panel-container">
		<?php
			$this->_include('partials/panel-one.inc');
		?>
		<div id="content-panel">
			<?php
				include BLOSSOM.'/errorMessages.php';
				echo $this->includeBlocks();
			?>
		</div>
	</div>
	<?php
		$this->_include('partials/footer.inc');
	?>
</body>
</html>
<?php
	$this->_include('partials/garbageCollection.inc');
?>
