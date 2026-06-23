<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param array $this->history  An array of EventHistory objects
 */
declare (strict_types=1);

if (empty($this->history)) {
    return;
}
?>
<section class="audit-trail">
    <header><h3><?= $this->_('eventHistory'); ?></h3></header>
    <table class="audit-trail-table">
        <thead>
            <tr>
                <th><?= $this->_('date'); ?></th>
                <th><?= $this->_('person'); ?></th>
                <th><?= $this->_('action'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
                foreach ($this->history as $h) {
                    $date   = $h->getAction_date()->format(DATE_FORMAT);
                    $person = parent::escape($h->getPerson()->getFullname());
                    $action = parent::escape($h->getAction());
                    echo "
                    <tr>
                        <td>$date</td>
                        <td>$person</td>
                        <td>$action</td>
                    </tr>
                    ";
                }
            ?>
        </tbody>
    </table>
</section>
