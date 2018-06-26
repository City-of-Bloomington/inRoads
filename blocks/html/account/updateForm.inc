<?php
/**
 * @copyright 2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param string $this->firstname
 * @param string $this->lastname
 * @param string $this->email
 * @param string $this->phone
 */
declare (strict_types=1);
?>
<section>
    <header><h1><?= $this->_('account_edit'); ?></h1></header>
    <form method="post">
        <fieldset><legend><?= $this->_('person_info'); ?></legend>
        <?php
            $this->_include('people/partials/personInfoFields.inc');

            $h = $this->template->getHelper('saveAndCancelButtons');
            echo $h->saveAndCancelButtons(BASE_URI.'/account');
        ?>
        </fieldset>
    </form>
</section>
