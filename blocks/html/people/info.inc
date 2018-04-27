<?php
/**
 * @copyright 2009-2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Person $this->person
 */
use Application\Models\Person;
use Blossom\Classes\Url;

$fields = ['id', 'fullname', 'department', 'email', 'phone', 'username'];
foreach ($fields as $f) {
    $get = 'get'.ucfirst($f);
    $$f  = $this->person->$get();
}
$notifications = $this->person->getNotifications() ? $this->_('yes') : $this->_('no');

$return_url = Url::current_url(BASE_HOST);
$h = $this->template->getHelper('buttonLink');

if (!$username && Person::isAllowed('users')) {
	$username = $h->buttonLink(
		BASE_URI."/users/update?user_id=$id;return_url=$return_url",
		$this->_('user_add'),
		'add'
	);
}

$editButton = '';
if (Person::isAllowed('people', 'update')) {
    $editButton = $h->buttonLink(
        BASE_URI."/people/update?person_id=$id;return_url=$return_url",
        $this->_('edit'),
        'edit'
    );
}
?>
<section>
    <header>
        <h1><?= $fullname; ?></h1>
        <div class="tools"><?= $editButton; ?></div>
    </header>
    <table>
        <tr><th><?= $this->_('department'   ); ?></th><td><?= $department;    ?></th></tr>
        <tr><th><?= $this->_('email'        ); ?></th><td><?= $email;         ?></th></tr>
        <tr><th><?= $this->_('phone'        ); ?></th><td><?= $phone;         ?></th></tr>
        <tr><th><?= $this->_('username'     ); ?></th><td><?= $username;      ?></th></tr>
        <tr><th><?= $this->_('notifications'); ?></th><td><?= $notifications; ?></th></tr>
    </table>
</section>
