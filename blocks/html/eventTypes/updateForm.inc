<?php
/**
 * @copyright 2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param EventType $this->eventType
 */
use Blossom\Classes\View;

foreach (['code', 'name', 'description', 'color'] as $f) {
    $get = 'get'.ucfirst($f);
    $$f = View::escape($this->eventType->$get());
}
$isDefault = $this->eventType->isDefault();
$title = $this->eventType->getId() ? $this->_('eventType_edit') : $this->_('eventType_add');
?>
<section>
    <h1><?= $title; ?></h1>

    <form method="post" action="<?= BASE_URI; ?>/eventTypes/update">
        <fieldset>
            <input name="eventType_id" type="hidden" value="<?= $this->eventType->getId(); ?>" />

            <dl><dt><label  for="code"><?= $this->_('code'); ?></label></dt>
                <dd><input name="code" id="code" value="<?= $code; ?>" /></dd>
            </dl>
            <dl><dt><label  for="name"><?= $this->_('name'); ?></label></dt>
                <dd><input name="name" id="name" value="<?= $name; ?>" /></dd>
            </dl>
            <dl><dt><label  for="color"><?= $this->_('color'); ?></label></dt>
                <dd><input name="color" id="color" value="<?= $color; ?>" /></dd>
            </dl>
            <dl><dt><label  for="description"><?= $this->_('description'); ?></label></dt>
                <dd><input name="description" id="description" value="<?= $description; ?>" /></dd>
            </dl>
            <dl><dt><label  for="isDefault"><?= $this->_('isDefault'); ?></label></dt>
                <dd><select name="isDefault" id="isDefault">
                        <option value="1" <?= $isDefault ? 'selected="true"' : ''; ?>>Yes</option>
                        <option value="0" <?= $isDefault ? '' : 'selected="true"'; ?>>No </option>
                    </select>
                </dd>
            </dl>
            <?php
				$helper = $this->template->getHelper('saveAndCancelButtons');
				echo $helper->saveAndCancelButtons(BASE_URI.'/eventTypes');
            ?>
        </fieldset>
    </form>
</section>
