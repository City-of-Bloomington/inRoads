<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<?php $this->_include('partials/head.inc'); ?>
<body>
	<main>
        <div id="content-panel">
            <?= $this->includeBlocks(); ?>
        </div>
	</main>
	<?php $this->_include('partials/assets.inc'); ?>
</body>
</html>
<?php $this->_include('partials/garbageCollection.inc'); ?>
