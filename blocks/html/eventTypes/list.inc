<?php
/**
 * @copyright 2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param array $this->eventType
 */
use Application\Models\Person;
use Blossom\Classes\View;

$userCanEdit = Person::isAllowed('eventTypes', 'update');
?>
<section>
    <h1><?= $this->_(['eventType', 'eventTypes', 2]); ?></h1>
    <div>
    <?php
        if ($userCanEdit) {
            $helper = $this->template->getHelper('buttonLink');
            echo $helper->buttonLink(
                BASE_URI."/eventTypes/update",
                $this->translate('eventType_add'),
                'add'
            );
        }
    ?>
    </div>

    <form method="post" action="<?= BASE_URI; ?>/eventTypes/order">
    <table>
        <thead>
            <tr><th><?= $this->_('code'); ?></th>
                <th><?= $this->_('name'); ?></th>
                <th><?= $this->_('color'); ?></th>
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
                $code    = View::escape($t->getCode ());
                $name    = View::escape($t->getName ());
                $desc    = View::escape($t->getDescription());
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
                    <td>$desc</td>
                    <td><input type=\"checkbox\" disabled=\"true\" $checked /></td>
                    $sortingNumber
                </tr>
                ";
            }
            if ($userCanEdit) {
                echo "
                <tr><td colspan=\"5\"></td>
                    <td><button type=\"submit\" class=\"save\">
                            {$this->template->_('save')}
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
