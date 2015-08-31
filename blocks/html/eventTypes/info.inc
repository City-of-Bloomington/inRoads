<?php
/**
 * @copyright 2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param EventType $this->eventType
 */
use Application\Models\Person;
use Blossom\Classes\View;

foreach (['name', 'code', 'description'] as $f) {
    $get = 'get'.ucfirst($f);
    $$f = View::escape($this->eventType->$get())   ;
}

$editButton = '';
if (Person::isAllowed('eventTypes', 'edit')) {
    $h = $this->template->getHelper('buttonLink');
    $editButton = $h->buttonLink(
        BASE_URI."/eventTypes/update?eventType_id={$this->eventType->getId()}",
        $this->translate('edit'),
        'edit'
    );
}
?>
<section>
    <h1><?= $name; ?></h1>
    <div><?= $editButton; ?></div>
    <table>
        <tr><th><?= $this->_('code'); ?></th>
            <td><?= $code; ?></td>
        </tr>
        <tr><th><?= $this->_('color'); ?></th>
            <td><?= $this->eventType->getColor(); ?></td>
        </tr>
        <tr><th><?= $this->_('description'); ?></th>
            <td><?= $description; ?></th>
        </tr>
        <?php
            $checked = $this->eventType->isDefault() ? 'checked="true"' : '';
        ?>
        <tr><th><?= $this->_('isDefault'); ?></th>
            <td><input type="checkbox" disabled="true" <?= $checked; ?> /></td>
        </tr>
    </table>
</section>
