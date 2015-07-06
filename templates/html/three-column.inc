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
				<?php echo $this->includeBlocks('headerBar'); ?>
				<div class="pillNav">
					<?php

						$c = (!empty($_GET['view']) && $_GET['view'] === 'schedule')
				            ? 'schedule'
				            : 'map';

				        $url = new Url(BASE_URL.'/events');
				        $url->start   = !empty($_GET['start'])   ? $_GET['start']   : null;
				        $url->end     = !empty($_GET['end'])     ? $_GET['end']     : null;
				        $url->filters = !empty($_GET['filters']) ? $_GET['filters'] : null;

				        $current = $c === 'map' ? 'class="current"' : '';
				        echo "<a href=\"$url\" $current>{$this->_('mapView')}</a>\n";

				        $current = $c === 'schedule' ? 'class="current"' : '';
				        $url->view = 'schedule';
				        echo "<a href=\"$url\" $current>{$this->_('scheduleView')}</a>\n";




					?>
				</div>
				<?php
					if ($this->headerToolsButton) {
						echo "<div class=\"headerBar-tools\">{$this->headerToolsButton}</div>";
					}
				?>
			</header>
	        <?php $this->_include('partials/panel-two.inc'); ?>
	        <div class="errorPanel">
	            <?php include BLOSSOM.'/errorMessages.php'; ?>
	        </div>
	        <div class="contentPanel" id="content-panel">
	            <?php echo $this->includeBlocks(); ?>
	        </div>
		</div>
	</main>
	<?php $this-> _include('partials/footer.inc'); ?>
</body>
</html>
<?php $this->_include('partials/garbageCollection.inc'); ?>
