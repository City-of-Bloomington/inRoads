<?php
/**
 * @copyright 2007-2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param array $this->errorMessages
 */
$this->template->addToAsset('scripts', BASE_URI.'/js/errorMessages.js');
?>
<div id="errorMessages">
	<h2><?= $this->_('error'); ?></h2>
	<?php
        $errorDir = "/blocks/html/errorMessages";

		foreach ($this->errorMessages as $e) {
			$error = $e->getMessage();
			$file = "$errorDir/$error.inc";

            if ($this->theme
                && is_file($this->theme.$file)) {
                include    $this->theme.$file;
            }
			elseif (is_file(APPLICATION_HOME.$file)) {
                include     APPLICATION_HOME.$file;
			}
			else {
				echo "<p>{$this->_($error, 'errors')}</p>";
			}
		}
	?>
	<button id="dismissButton"><?= $this->_('ok'); ?></button>
</div>
