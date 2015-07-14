<?php
/**
 * @copyright 2009-2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Person $this->person
 */
use Blossom\Classes\View;

$fields = array('firstname','lastname','email');
foreach ($fields as $field) {
	$get = 'get'.ucfirst($field);
	$$field = View::escape($this->person->$get());
}
$title = $this->person->getId() ? View::escape('Edit '.$this->person->getFirstname()) : 'Add Someone';
?>
<section>
	<h1><?php echo $title; ?></h1>
	<form method="post" action="<?php echo BASE_URI; ?>/people/update">
		<fieldset><legend><?php echo $this->_('person_info'); ?></legend>
			<input name="person_id" type="hidden" value="<?php echo $this->person->getId(); ?>" />

			<dl><dt><label for="firstname"><?php echo $this->_('firstname'); ?></label></dt>
                <dd><input name="firstname" id="firstname" value="<?php echo $firstname; ?>"
                            required="required" inputmode="latin-name" />
                </dd>
            </dl>

            <dl><dt><label for="lastname"><?php echo $this->_('lastname'); ?></label></dt>
                <dd><input name="lastname" id="lastname" value="<?php echo $lastname; ?>"
                        required="required" inputmode="latin-name" />
                </dd>
            </dl>

            <dl><dt><label for="email"><?php echo $this->_(['email', 'emails', 1]); ?></label></dt>
                <dd><input name="email" id="email" type="email" value="<?php echo $email; ?>" /></dd>
            </dl>
			<?php
				$h = $this->template->getHelper('saveAndCancelButtons');
				echo $h->saveAndCancelButtons(BASE_URI.'/people');
			?>
		</fieldset>
	</form>
</section>
