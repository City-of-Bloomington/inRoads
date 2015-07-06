<?php
	use Blossom\Classes\View;
	use Blossom\Classes\Url;
?>
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<?php $this->_include('partials/head.inc'); ?>
<body class="eventsMap">
	<?php $this->_include('partials/header.inc'); ?>
	<main>
        <?php $this->_include('partials/panel-one.inc'); ?>

		<div class="content-container">
			<header class="headerBar">
				<?= $this->includeBlocks('headerBar'); ?>
			</header>
	        <?php $this->_include('partials/panel-two.inc'); ?>
	        <div class="errorPanel">
	            <?php include BLOSSOM.'/errorMessages.php'; ?>
	        </div>
	        <div class="contentPanel" id="content-panel">
	            <?= $this->includeBlocks(); ?>
	        </div>
		</div>
	</main>
	<?php
        $this->_include('partials/footer.inc');
        $this->_include('partials/assets.inc');
    ?>
</body>
</html>
<?php $this->_include('partials/garbageCollection.inc'); ?>