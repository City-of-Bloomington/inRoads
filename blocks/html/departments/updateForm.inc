<?php
/**
 * @copyright 2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Department $this->department
 */
use Application\View;

foreach (['code', 'name', 'phone'] as $f) {
    $get = 'get'.ucfirst($f);
    $$f = View::escape($this->department->$get());
}
$title = $this->department->getId() ? $this->_('department_edit') : $this->_('department_add');
?>
<section>
    <h1><?= $title; ?></h1>

    <form method="post" action="<?= BASE_URI; ?>/departments/update">
        <fieldset>
            <input name="department_id" type="hidden" value="<?= $this->department->getId(); ?>" />

            <dl><dt><label  for="code"><?= $this->_('code'); ?></label></dt>
                <dd><input name="code" id="code" value="<?= $code; ?>" /></dd>
            </dl>
            <dl><dt><label  for="name"><?= $this->_('name'); ?></label></dt>
                <dd><input name="name" id="name" value="<?= $name; ?>" /></dd>
            </dl>
            <dl><dt><label  for="phone"><?= $this->_('phone'); ?></label></dt>
                <dd><input name="phone" id="phone" value="<?= $phone; ?>" /></dd>
            </dl>
            <?php
				$helper = $this->template->getHelper('saveAndCancelButtons');
				echo $helper->saveAndCancelButtons(BASE_URI.'/departments');
            ?>
        </fieldset>
    </form>
</section>
