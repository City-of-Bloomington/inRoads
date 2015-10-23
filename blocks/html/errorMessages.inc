<?php
/**
 * @copyright 2007-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param array $this->errorMessages
 */
$this->template->addToAsset('scripts', BASE_URI.'/js/errorMessages.js');
?>
<div id="errorMessages" class="errorPanel">
	<h2>Sorry</h2>
	<?php
		foreach ($this->errorMessages as $e) {
			$error = $e->getMessage();
			if (file_exists(APPLICATION_HOME."/blocks/html/errorMessages/$error.inc")) {
				$this->_include("html/errorMessages/$error.inc");
			}
			else {
				echo "<p>$error</p>";
			}
		}
	?>
	<div class="errorPanel-buttons">
		<button id="dismissButton">Okay</button>
	</div>
</div>
