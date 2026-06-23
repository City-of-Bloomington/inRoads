<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param array $this->history  An array of EventHistory objects
 */
declare (strict_types=1);
use Application\Auth;

if (empty($this->history) || !Auth::isAllowed('events', 'history')) {
    return;
}
?>
<button class="audit-trail-button" onclick="document.getElementById('auditTrailModal').showModal();">
    <?= $this->_('eventHistory'); ?>
</button>

<dialog id="auditTrailModal" class="audit-trail-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2><?= $this->_('eventHistory'); ?></h2>
            <button class="modal-close" onclick="document.getElementById('auditTrailModal').close();" aria-label="Close">×</button>
        </div>
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
    </div>
</dialog>
