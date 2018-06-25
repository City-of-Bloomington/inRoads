<?php
/**
 * @copyright 2009-2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param string $this->id
 * @param string $this->name
 * @param string $this->username
 * @param string $this->department
 * @param string $this->email
 * @param string $this->phone
 */
use Application\Auth;
use Blossom\Classes\Url;

$return_url = Url::current_url(BASE_HOST);
$h = $this->template->getHelper('buttonLink');

if (!$this->username && Auth::isAllowed('users', 'update')) {
	 $this->username = $h->buttonLink(
		BASE_URI."/users/update?user_id={$this->id};return_url=$return_url",
		$this->_('user_add'),
		'add'
	);
}

$editButton = '';
if (Auth::isAllowed('people', 'update')) {
    $editButton = $h->buttonLink(
        BASE_URI."/people/update?id={$this->id}",
        $this->_('edit'),
        'edit'
    );
}
?>
<section>
    <header>
        <h1><?= $this->name;     ?></h1>
        <h2><?= $this->username; ?></h2>
        <div class="tools"><?= $editButton; ?></div>
    </header>
    <table>
        <tr><th><?= $this->_('department'); ?></th><td><?= $this->department; ?></th></tr>
        <tr><th><?= $this->_('email'     ); ?></th><td><?= $this->email;      ?></th></tr>
        <tr><th><?= $this->_('phone'     ); ?></th><td><?= $this->phone;      ?></th></tr>
    </table>
</section>
