<?php
/**
 * @copyright 2017-2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param string $this->firstname
 * @param string $this->lastname
 * @param string $this->email
 * @param array  $this->people         Array of people search results
 */
declare (strict_types=1);
?>
<section>
    <header>
        <h1><?= $this->_('people_search'); ?></h1>
    </header>
    <form method="get">
        <fieldset>
        <?php
            $helper = $this->template->getHelper('field');
            $fields = ['firstname', 'lastname', 'email'];
            foreach ($fields as $f) {
                echo $helper->field([
                    'name'  => $f,
                    'id'    => $f,
                    'label' => $this->_($f),
                    'value' => $this->$f
                ]);
            }
        ?>
        <button type="submit" class="search"><?= $this->_('search'); ?></button>
        </fieldset>
    </form>
    <?php
        if ($this->people) { $this->_include('people/list.inc'); }
    ?>
</section>
