<?php
/**
 * @copyright 2009-2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Person $this->person
 */
use Blossom\Classes\View;

$fields = array('firstname','lastname','email', 'phone');
foreach ($fields as $field) {
	$get = 'get'.ucfirst($field);
	$$field = View::escape($this->person->$get());
}
$title = $this->person->getId() ? View::escape('Edit '.$this->person->getFirstname()) : 'Add Someone';
?>
<section>
	<h1><?= $title; ?></h1>
	<form method="post" action="<?= BASE_URI; ?>/people/update">
		<fieldset><legend><?= $this->_('person_info'); ?></legend>
			<input name="person_id" type="hidden" value="<?= $this->person->getId(); ?>" />

			<dl><dt><label  for="firstname"><?= $this->_('firstname'); ?></label></dt>
                <dd><input name="firstname" id="firstname" value="<?= $firstname; ?>"
                        required="required" inputmode="latin-name" />
                </dd>
            </dl>

            <dl><dt><label  for="lastname"><?= $this->_('lastname'); ?></label></dt>
                <dd><input name="lastname" id="lastname" value="<?= $lastname; ?>"
                        required="required" inputmode="latin-name" />
                </dd>
            </dl>

            <dl><dt><label  for="email"><?= $this->_(['email', 'emails', 1]); ?></label></dt>
                <dd><input name="email" id="email" type="email" value="<?= $email; ?>" /></dd>
            </dl>

            <dl><dt><label  for="phone"><?= $this->_('phone'); ?></label>
                <dd><input name="phone" id="phone" type="tel" value="<?= $phone; ?>" /></dd>
            </dl>
			<?php
				$h = $this->template->getHelper('saveAndCancelButtons');
				echo $h->saveAndCancelButtons(BASE_URI.'/people');
			?>
		</fieldset>
	</form>
</section>
