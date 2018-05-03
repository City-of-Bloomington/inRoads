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
$notify_updates   = $this->person->getNotify_updates  () ? $this->_('yes') : $this->_('no');
$notify_emergency = $this->person->getNotify_emergency() ? $this->_('yes') : $this->_('no');

$return_url = Url::current_url(BASE_HOST);
$h = $this->template->getHelper('buttonLink');

$username = parent::escape($this->person->getUsername());
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
        <h2><?= $username; ?></h2>
        <div class="tools"><?= $editButton; ?></div>
    </header>
    <?= $this->_include('people/partials/personalInfo.inc'); ?>
</section>
