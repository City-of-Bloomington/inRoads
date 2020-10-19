<?php
/**
 * @copyright 2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Event  $this->event
 * @param string $this->type
 * @param string $this->comments
 */
declare (strict_types=1);
use Application\Block;
use Application\Template;
?>
<section>
    <form method="post">
        <fieldset>
            <pre>
            <?php
                $template = new Template('default', 'txt');
                $block    = new Block("notifications/{$this->type}.inc", ['event'=>$this->event]);
                echo $block->render('txt', $template);
            ?>
            </pre>
            <input name="event_id" type="hidden" value="<?= $this->event->getId(); ?>" />
            <input name="type"     type="hidden" value="<?= $this->type; ?>" />

            <button type="submit" class="notify"><?= $this->_('send'); ?></button>
            <a class="cancel button" href="<?= BASE_URI; ?>/events/view?id=<?= $this->event->getId(); ?>"><?= $this->_('cancel'); ?></a>
        </fieldset>
    </form>
</section>
