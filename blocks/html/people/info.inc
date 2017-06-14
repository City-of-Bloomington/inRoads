<?php
/**
 * @copyright 2009-2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Person $this->person
 */
use Application\Models\Person;
use Blossom\Classes\View;
use Blossom\Classes\Url;

$return_url = Url::current_url(APPLICATION_HOST);
$h = $this->template->getHelper('buttonLink');

$username = $this->person->getUsername();
if (!$username && Person::isAllowed('users')) {
	$username = $h->buttonLink(
		BASE_URI."/users/update?user_id={$this->person->getId()};return_url=$return_url",
		$this->translate('user_add'),
		'add'
	);
}

$editButton = '';
if (Person::isAllowed('people', 'edit')) {
    $editButton = $h->buttonLink(
        BASE_URI."/people/update?person_id={$this->person->getId()};return_url=$return_url",
        $this->translate('edit'),
        'edit'
    );
}
$name  = View::escape($this->person->getFullname());
$dept  = View::escape($this->person->getDepartment());
$phone = View::escape($this->person->getPhone());
echo "
<section>
	<h1>$name</h1>
	<div>$editButton</div>
	<table>
        <tr><th>{$this->_('department')}</th>
            <td>$dept</td>
        </tr>
		<tr><th>{$this->_('email')}</th>
			<td>{$this->person->getEmail()}</td>
		</tr>
		<tr><th>{$this->_('phone')}</th>
            <td>$phone</td>
        </tr>
		<tr><th>{$this->_('username')}</th>
			<td>$username</td>
		</tr>
	</table>
</section>
";
