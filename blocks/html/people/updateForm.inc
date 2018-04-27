<?php
/**
 * @copyright 2009-2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Person $this->person
 * @param string $this->return_url
 */
$person_id = $this->person->getId();
$title     = $person_id ? $this->_('person_edit') : $this->_('person_add');
?>
<section>
    <header>
        <h1><?= $title; ?></h1>
    </header>
	<form method="post">
		<fieldset><legend><?= $this->_('person_info'); ?></legend>
			<input name="person_id" type="hidden" value="<?= $person_id ?>" />
			<?php
                $this->_include('people/partials/personInfoFields.inc');

				$h = $this->template->getHelper('saveAndCancelButtons');
				echo $h->saveAndCancelButtons($this->return_url);
			?>
		</fieldset>
	</form>
</section>
