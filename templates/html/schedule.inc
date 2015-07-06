<?php use Blossom\Classes\View; ?>
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<?php $this->_include('partials/head.inc'); ?>
<body class="schedule">
	<?php $this->_include('partials/header.inc'); ?>
	<main>
        <?php $this->_include('partials/panel-one.inc'); ?>
        <div class="errorPanel">
            <?php include BLOSSOM.'/errorMessages.php'; ?>
        </div>
		<div class="content-container">
			<header class="headerBar">
				<div class="headerBar-title"><?= View::escape($this->title); ?></div>
				<?php
					if ($this->headerToolsButton) {
						echo "<div class=\"headerBar-tools\">{$this->headerToolsButton}</div>";
					}
				?>
			</header>
        	<div class="contentPanel" id="content-panel">
	            <?php echo $this->includeBlocks(); ?>
			</div>
        </div>
	</main>
	<?php $this-> _include('partials/footer.inc'); ?>
</body>
</html>
<?php $this->_include('partials/garbageCollection.inc'); ?>
