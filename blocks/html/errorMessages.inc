<?php
/**
 * @copyright 2007-2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param array $this->errorMessages
 */
use Blossom\Classes\Block;
?>
<div id="errorMessages">
	<h2>Sorry</h2>
	<?php
		foreach ($this->errorMessages as $class=>$fields) {
            foreach ($fields as $field=>$errors) {
                foreach ($errors as $e) {

                    if (file_exists(APPLICATION_HOME."/blocks/html/errorMessages/$e.inc")) {
                        $file = "errorMessages/$e.inc";
                    }
                    elseif (file_exists(APPLICATION_HOME."/blocks/html/errorMessages/$class/$e.inc")) {
                        $file = "errorMessages/$class/$e.inc";
                    }
                    else {
                        $file = 'errorMessages/default.inc';
                    }

                    $block = new Block($file, ['class'=>$class, 'field'=>$field, 'error'=>$e]);
                    echo $block->render('html');
                }
            }
		}
	?>
</div>
