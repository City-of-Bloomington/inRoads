<?php
/**
 * @copyright 2014-2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Event $this->event
 */
use Application\Models\Event;
use Blossom\Classes\View;

$title = $this->event->getId() ? $this->_('event_edit') : $this->_('event_add');
?>
<section>
    <h1><?php echo $title; ?></h1>
    <form method="post" id="eventUpdateForm" action="<?php echo BASE_URI; ?>/events/update">
        <fieldset>
            <input name="id" type="hidden" value="<?php echo $this->event->getId(); ?>" />

            <legend><?php echo $this->_('event_info'); ?></legend>
            <?php
                $format = $this->event->isAllDay() ? DATE_FORMAT : DATETIME_FORMAT;
            ?>
            <dl><dt><label  for="start"><?= $this->_('startDate'); ?></label></dt>
                <dd><?= $this->event->getStart($format); ?>
                </dd>
            </dl>
            <dl><dt><label  for="end"><?= $this->_('endDate'); ?></label></dt>
                <dd><?= $this->event->getEnd($format); ?>
                </dd>
            </dl>
            <dl><dt><label  for="geography_description"><?= $this->_('geography_description'); ?></label></dt>
                <dd><input name="geography" id="geography" value="<?= $this->event->getGeography(); ?>" />
                    <p><?= View::escape($this->event->getSummary()); ?></p>
                </dd>
            </dl>
            <div>
                <div><label for="description"><?php echo $this->_('description'); ?></label></div>
                <textarea name="description" id="description"><?= View::escape($this->event->getDescription()); ?></textarea>
            </div>
            <?php
                $helper = $this->template->getHelper('saveAndCancelButtons');
                echo $helper->saveAndCancelButtons(BASE_URI.'/events');
            ?>
        </fieldset>
    </form>
</section>
