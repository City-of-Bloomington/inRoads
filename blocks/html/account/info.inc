<?php
/**
 * @copyright 2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Person $this->person
 */
declare (strict_types=1);
use Application\Models\Person;

$username = parent::escape($this->person->getUsername());
?>
<section>
    <header>
        <h1><?= $this->_('account'); ?></h1>
        <h2><?= $username; ?></h2>
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
    <?= $this->_include('people/partials/personalInfo.inc'); ?>
</section>
