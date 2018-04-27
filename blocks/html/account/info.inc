<?php
/**
 * @copyright 2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Person $this->person
 */
declare (strict_types=1);
use Application\Models\Person;

$fields = ['firstname', 'lastname', 'department', 'email', 'phone', 'username'];
foreach ($fields as $f) {
    $get = 'get'.ucfirst($f);
    $$f  = parent::escape($this->person->$get());
}
$notifications = $this->person->getNotifications() ? $this->_('yes') : $this->_('no');
?>
<section>
    <header>
        <h1><?= $this->_('account'); ?></h1>
        <div class="tools">
        <?php
            if (Person::isAllowed('account', 'update')) {
                $h = $this->template->getHelper('buttonLink');
                echo $h->buttonLink(
                    BASE_URI.'/account/update',
                    $this->_('edit'),
                    'edit'
                );
            }
        ?>
        </div>
    </header>


    <table>
        <tr><th><?= $this->_('firstname'    ); ?></th><td><?= $firstname;     ?></th></tr>
        <tr><th><?= $this->_('lastname'     ); ?></th><td><?= $lastname;      ?></th></tr>
        <tr><th><?= $this->_('department'   ); ?></th><td><?= $department;    ?></th></tr>
        <tr><th><?= $this->_('email'        ); ?></th><td><?= $email;         ?></th></tr>
        <tr><th><?= $this->_('phone'        ); ?></th><td><?= $phone;         ?></th></tr>
        <tr><th><?= $this->_('username'     ); ?></th><td><?= $username;      ?></th></tr>
        <tr><th><?= $this->_('notifications'); ?></th><td><?= $notifications; ?></th></tr>
    </table>
    </table>

</section>
