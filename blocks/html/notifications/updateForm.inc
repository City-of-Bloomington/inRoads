<?php
/**
 * @copyright 2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param string $this->id
 * @param string $this->type
 * @param string $this->email
 */
declare (strict_types=1);
?>
<section>
    <header>
        <h1><?= $this->type; ?></h1>
    </header>
    <form method="post">
        <fieldset>
            <input type="hidden" name="id"   value="<?= $this->id;   ?>" />
            <input type="hidden" name="type" value="<?= $this->type; ?>" />
            <?php
                $h = $this->template->getHelper('field');
                echo $h->field([
                    'name'     => 'email',
                    'id'       => 'email',
                    'label'    => $this->_('email'),
                    'value'    => $this->email,
                    'type'     => 'email',
                    'attr'     => ['inputmode'=>'email']
                ]);

                $h = $this->template->getHelper('saveAndCancelButtons');
                echo $h->saveAndCancelButtons(BASE_URI.'/notifications');
            ?>
        </fieldset>
    </form>
</section>
