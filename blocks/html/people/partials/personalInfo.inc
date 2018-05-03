<?php
/**
 * @copyright 2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Person $this->person
 */
declare (strict_types=1);
use Application\Models\Person;

$fields = ['id', 'fullname', 'department', 'email', 'phone'];
foreach ($fields as $f) {
    $get = 'get'.ucfirst($f);
    $$f  = $this->person->$get();
}
$notify_updates   = $this->person->getNotify_updates  () ? $this->_('yes') : $this->_('no');
$notify_emergency = $this->person->getNotify_emergency() ? $this->_('yes') : $this->_('no');
?>
<table>
    <tr><th><?= $this->_('department'      ); ?></th><td><?= $department;       ?></th></tr>
    <tr><th><?= $this->_('email'           ); ?></th><td><?= $email;            ?></th></tr>
    <tr><th><?= $this->_('phone'           ); ?></th><td><?= $phone;            ?></th></tr>
    <tr><th><?= $this->_('notify_updates'  ); ?></th><td><?= $notify_updates;   ?></th></tr>
    <tr><th><?= $this->_('notify_emergency'); ?></th><td><?= $notify_emergency; ?></th></tr>
</table>
