<?php
/**
 * @copyright 2009-2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param int    $this->id
 * @param string $this->title
 */
$return_url = $this->id ? BASE_URI.'/people/view?person_id='.$this->id : BASE_URI.'/people';
?>
<section>
    <header>
        <h1><?= $this->title; ?></h1>
    </header>
	<form method="post">
		<fieldset><legend><?= $this->_('person_info'); ?></legend>
			<input name="id" type="hidden" value="<?= $this->id ?>" />
			<?php
                $this->_include('people/partials/personInfoFields.inc');

				$h = $this->template->getHelper('saveAndCancelButtons');
				echo $h->saveAndCancelButtons($return_url);
			?>
		</fieldset>
	</form>
</section>
