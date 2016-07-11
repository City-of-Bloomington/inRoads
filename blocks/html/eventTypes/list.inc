<?php
/**
 * @copyright 2015-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param array $this->eventTypes
 */
use Application\Models\Person;

$userCanEdit = Person::isAllowed('eventTypes', 'update');
?>
<section>
    <header>
        <h1><?= $this->_(['eventType', 'eventTypes', count($this->eventTypes)]); ?></h1>
        <div class="tools">
        <?php
            if ($userCanEdit) {
                $helper = $this->template->getHelper('buttonLink');
                echo $helper->buttonLink(
                    BASE_URI."/eventTypes/update",
                    $this->_('eventType_add'),
                    'add'
                );
            }
        ?>
        </div>
    </header>

    <form method="post" action="<?= BASE_URI; ?>/eventTypes/order">
    <table>
        <thead>
            <tr><th><?= $this->_('code'); ?></th>
                <th><?= $this->_('name'); ?></th>
                <th><?= $this->_('color'); ?></th>
                <th><?= $this->_('cifsType'); ?></th>
                <th><?= $this->_('description'); ?></th>
                <th><?= $this->_('isDefault'); ?></th>
                <?php
                    if ($userCanEdit) {
                        echo "<th>{$this->_('sortingNumber')}</th>";
                    }
                ?>
            </tr>
        </thead>
        <tbody>
        <?php
            foreach ($this->eventTypes as $t) {
                $code    = self::escape($t->getCode ());
                $name    = self::escape($t->getName ());
                $desc    = self::escape($t->getDescription());
                $cifs    = self::escape($t->getCifsType());
                $color   = $t->getColor();
                $checked = $t->isDefault() ? 'checked="true"' : '';

                $url = BASE_URI.'/eventTypes/view?eventType_id='.$t->getId();

                $sortingNumber = $userCanEdit
                    ? "<td><input name=\"sortingNumber[{$t->getId()}]\" size=\"2\" maxlength=\"2\" value=\"{$t->getSortingNumber()}\" /></td>"
                    : '';

                echo "
                <tr>
                    <td><a href=\"$url\">$code</a></td>
                    <td>$name</td>
                    <td>#$color</td>
                    <td>$cifs</td>
                    <td>$desc</td>
                    <td><input type=\"checkbox\" disabled=\"true\" $checked /></td>
                    $sortingNumber
                </tr>
                ";
            }
            if ($userCanEdit) {
                echo "
                <tr><td colspan=\"6\"></td>
                    <td><button type=\"submit\" class=\"save\">
                            {$this->_('save')}
                        </button>
                    </td>
                </tr>
                ";
            }
        ?>
        </tbody>
    </table>
    </form>
</section>
