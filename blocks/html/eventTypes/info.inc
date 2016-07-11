<?php
/**
 * @copyright 2015-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param EventType $this->eventType
 */
use Application\Models\Person;

$fields = ['name', 'code', 'description', 'cifsType'];
foreach ($fields as $f) {
    $get = 'get'.ucfirst($f);
    $$f = self::escape($this->eventType->$get())   ;
}

$editButton = '';
if (Person::isAllowed('eventTypes', 'edit')) {
    $h = $this->template->getHelper('buttonLink');
    $editButton = $h->buttonLink(
        BASE_URI."/eventTypes/update?eventType_id={$this->eventType->getId()}",
        $this->_('edit'),
        'edit'
    );
}
?>
<section>
    <header>
        <h1><?= $name; ?></h1>
        <div class="tools"><?= $editButton; ?></div>
    </header>
    <table>
        <?php
            foreach ($fields as $f) {
                echo "<tr><th>{$this->_($f)}</th><td>{$$f}</td></tr>";
            }

            $checked = $this->eventType->isDefault() ? 'checked="true"' : '';
        ?>
        <tr><th><?= $this->_('isDefault'); ?></th>
            <td><input type="checkbox" disabled="true" <?= $checked; ?> /></td>
        </tr>
    </table>
</section>
