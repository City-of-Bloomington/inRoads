<?php
/**
 * @copyright 2014-2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Event $this->event
 */
use Application\Models\Event;
use Blossom\Classes\View;
use iCal4PHP\Recur;
use iCal4PHP\WeekDay;
?>
<div class="editPanel">
<form method="post" id="eventUpdateForm" action="<?php echo BASE_URI; ?>/events/update">
    <fieldset>
        <input name="id" type="hidden" value="<?php echo $this->event->getId(); ?>" />

        <legend><?php echo $this->_('event_info'); ?></legend>
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
        <?php
            $dateHelp = View::translateDateString(DATE_FORMAT);
            $timeHelp = View::translateDateString(TIME_FORMAT);
            $dateSize = strlen($dateHelp);
            $timeSize = strlen($timeHelp);

            $recur = $this->event->getRecur();
            if (!$recur) { $recur = new Recur(); }
        ?>
        <dl><dt><label  for="start"><?= $this->_('startDate'); ?></label></dt>
            <dd><input name="start[date]" id="start"
                    placeholder="<?= $dateHelp; ?>" size="<?= $dateSize; ?>" maxlength="<?= $dateSize; ?>"
                    value="<?= $this->event->getStart(DATE_FORMAT); ?>" />
                <input name="start[time]"
                    placeholder="<?= $timeHelp; ?>" size="<?= $timeSize; ?>" maxlength="<?= $timeSize; ?>"
                    value="<?= $this->event->getStart(TIME_FORMAT); ?>" />
                <label>
                    <?php
                        $checked = $this->event->isAllDay() ? 'checked="checked"' : '';
                        echo "
                        <input name=\"allDay\" type=\"checkbox\" $checked />
                        {$this->_('allDay')}
                        ";
                    ?>
                </label>
            </dd>
        </dl>
        <dl><dt><label  for="end"><?= $this->_('endDate'); ?></label></dt>
            <dd><input name="end[date]" id="end"
                    placeholder="<?= $dateHelp; ?>" size="<?= $dateSize; ?>" maxlength="<?= $dateSize; ?>"
                    value="<?= $this->event->getEnd(DATE_FORMAT); ?>" />
                <input name="end[time]"
                    placeholder="<?= $timeHelp; ?>" size="<?= $timeSize; ?>" maxlength="<?= $timeSize; ?>"
                    value="<?= $this->event->getEnd(TIME_FORMAT); ?>" />
            </dd>
        </dl>
        <dl><dt><label   for="frequency"><?= $this->_('rrule_freq'); ?></label></dt>
            <dd><select name="frequency" id="frequency"><option value=""></option>
                <?php
                    foreach (['DAILY', 'WEEKLY', 'MONTHLY'] as $f) {
                        $selected = $recur->getFrequency()==$f
                            ? 'selected="selected"'
                            : '';
                        echo "<option value=\"$f\" $selected>{$this->_($f)}</option>";
                    }
                ?>
                </select>
            </dd>
        </dl>
    </fieldset>

    <fieldset id="DAILY">
        <dl><dt><?= $this->_('rrule_interval'); ?></dt>
            <dd><input name="DAILY[interval]" size="1" value="<?= $recur->getInterval(); ?>" /> days</dd>
        </dl>
    </fieldset>

    <fieldset id="WEEKLY">
        <dl><dt><?= $this->_('rrule_interval'); ?></dt>
            <dd><input name="WEEKLY[interval]" size="1" value="<?= $recur->getInterval(); ?>" /> weeks</dd>
        </dl>
        <dl><dt><?= $this->_('rrule_byday'); ?></dt>
            <dd><?php
                    $weekdays = $recur->getDayList();
                    $byday = [];
                    foreach ($weekdays as $day) { $byday[] = $day->getDay(); }

                    foreach (WeekDay::$names as $code=>$name) {
                        $checked = in_array($code, $byday)
                            ? 'checked="checked"'
                            : '';
                        echo "
                        <label>
                            <input name=\"WEEKLY[ByDay][$code]\" type=\"checkbox\" $checked />
                            {$this->_($name)}
                        </label>
                        ";
                    }
                ?>
            </dd>
        </dl>
    </fieldset>

    <fieldset id="MONTHLY">
        <div><?php
                $bymonthday = implode(',', $recur->getMonthDayList());
                $checked = ($bymonthday || !count($byday))
                    ? 'checked="checked"'
                    : '';
            ?>
            <input name="MONTHLY[type]" type="radio" value="BYMONTHDAY" <?= $checked; ?> />
            <label>Day      <input name="MONTHLY[ByMonthDay]" size="2" value="<?= $bymonthday ? $bymonthday : 30; ?>" /></label>
            <label>of every <input name="MONTHLY[interval]"   size="1" value="<?= $recur->getInterval(); ?>" /> month</label>
        </div>
        <div><?php
                $checked = count($byday) ? 'checked="checked"' : '';
            ?>
            <input name="MONTHLY[type]" type="radio" value="BYDAY" <?php $checked; ?> />
			<label>The
				<select name="MONTHLY[offset]">
                <?php
                    $offset = count($weekdays) ? $weekdays[0]->offset() : null;

                    foreach (WeekDay::$offsets as $num => $label) {
                        $selected = $offset == $num
                            ? 'selected="selected"'
                            : '';
                        echo "<option value=\"$num\" $selected>$label</option>";
                    }
                ?>
				</select>
			</label>
			<label>
				<select name="MONTHLY[ByDay]">
                <?php
                    foreach (WeekDay::$names as $code=>$name) {
                        $selected = in_array($code, $byday)
                            ? 'selected="selected"'
                            : '';
                        echo "<option value=\"$code\" $selected>{$this->_($name)}</option>";
                    }
                ?>
				</select>
				of every
			</label>
			<label><input name="MONTHLY[interval]" size="1" value="<?= $recur->getInterval(); ?>" /> month</label>
        </div>
    </fieldset>

    <fieldset>
        <dl><dt><label     for="description"><?= $this->_('description'); ?></label></dt>
            <dd><textarea name="description" id="description"><?= View::escape($this->event->getDescription()); ?></textarea></dd>
        </dl>

        <button type="submit" class="save"><?= $this->template->_('save'); ?></button>
    </fieldset>
</form>
</div>
