<?php
/**
 * @copyright 2014-2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Event $this->event
 */
use Application\Models\Event;
use Blossom\Classes\View;
?>
<form method="post" id="eventUpdateForm" action="<?php echo BASE_URI; ?>/events/update">
    <fieldset>
        <input name="id" type="hidden" value="<?php echo $this->event->getId(); ?>" />

        <legend><?php echo $this->_('event_info'); ?></legend>
        <?php
            $format = $this->event->isAllDay() ? DATE_FORMAT : DATETIME_FORMAT;
        ?>
        <dl><dt><label   for="department"><?= $this->_('department'); ?></label></dt>
            <dd><select name="department"><option value=""></option>
                <?php
                    foreach (Event::$departments as $code=>$name) {
                        $name = View::escape($name);
                        $selected = $this->event->getDepartment()==$code
                            ? 'selected="selected"'
                            : '';
                        echo "<option value=\"$code\" $selected>$name</option>";

                    }
                ?>
                </select>
            </dd>
        </dl>
        <dl><dt><label   for="type"><?= $this->_('type'); ?></label></dt>
            <dd><select name="type" id="type">
                <?php
                    foreach (Event::$types as $type=>$language) {
                        $selected = $this->event->getType()==$type
                            ? 'selected="selected"'
                            : '';
                        echo "<option>$type</option>";
                    }
                ?>
                </select>
            </dd>
        </dl>
        <dl><dt><label  for="geography_description"><?= $this->_('geography_description'); ?></label></dt>
            <dd><input name="geography_description" id="geography_description"
                        value="<?= View::escape($this->event->getGeography_description()); ?>" />
                <input name="geography" id="geography" type="hidden"
                        value="<?= $this->event->getGeography(); ?>" />
            </dd>
        </dl>
    </fieldset>

    <fieldset>
        <dl><dt><label  for="start"><?= $this->_('startDate'); ?></label></dt>
            <dd><?= $this->event->getStart($format); ?>
            </dd>
        </dl>
        <dl><dt><label  for="end"><?= $this->_('endDate'); ?></label></dt>
            <dd><?= $this->event->getEnd($format); ?>
            </dd>
        </dl>
    </fieldset>

    <fieldset><legend><?= $this->_('description'); ?></legend>
        <textarea name="description" id="description"><?= View::escape($this->event->getDescription()); ?></textarea>

        <button type="submit" class="save"><?= $this->template->_('save'); ?></button>
    </fieldset>
</form>
