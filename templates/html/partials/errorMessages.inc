<?php
/**
 * @copyright 2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
declare (strict_types=1);
use Application\Block;

if (isset($_SESSION['errorMessages'])) {
	$errorBlock = new Block('errorMessages.inc', ['errorMessages'=>$_SESSION['errorMessages']]);
	echo $errorBlock->render($this->outputFormat, $this);
	unset($_SESSION['errorMessages']);
}
