<?php
/**
 * @copyright 2014-2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Event $this->event
 */
use Application\Models\Event;
use Application\Models\EventType;
use Blossom\Classes\View;
use Recurr\Rule;
use Recurr\WeekDay;

$this->template->addToAsset('scripts', BASE_URI.'/js/events/updateForm.js');
?>
<div class="editPanel">
<form method="post" id="eventUpdateForm" action="<?php echo BASE_URI; ?>/events/update">
    <fieldset>
        <input name="id" type="hidden" value="<?php echo $this->event->getId(); ?>" />

        <legend><span><?php echo $this->_('event_info'); ?></span></legend>
        <dl>
            <dt><label   for="department"><?= $this->_('department'); ?></label></dt>
            <dd><select name="department"><option value=""></option>
                <?php
                    global $DEPARTMENTS;

                    foreach ($DEPARTMENTS as $code=>$d) {
                        $name = View::escape($d['name']);
                        $selected = $this->event->getDepartment()==$code
                            ? 'selected="selected"'
                            : '';
                        echo "<option value=\"$code\" $selected>$name</option>";

                    }
                ?>
                </select>
            </dd>
        </dl>
        <dl>
            <dt><label   for="type"><?= $this->_('type'); ?></label></dt>
            <dd><select name="type" id="type">
                <?php
                    foreach (EventType::types() as $type) {
                        $selected = $this->event->getType()->getCode()==$type->getCode()
                            ? 'selected="selected"'
                            : '';
                        $name = View::escape($type->getName());
                        echo "<option $selected value=\"{$type->getCode()}\">$name</option>";
                    }
                ?>
                </select>
            </dd>
        </dl>
        <dl>
            <dt><label  for="geography_description"><?= $this->_('geography_description'); ?></label></dt>
            <dd><input name="geography_description" id="geography_description"
                        value="<?= View::escape($this->event->getGeography_description()); ?>" />
                <input name="geography" id="geography" type="hidden"
                        value="<?= $this->event->getGeography(); ?>" />
            </dd>
        </dl>
        <dl>
            <dt>Details</dt>
            <dd><label>
                    <?php
                        $checked = $this->event->isAllDay() ? 'checked="checked"' : '';
                        echo "
                        <input name=\"allDay\" id=\"allDay\" type=\"checkbox\" $checked />
                        {$this->_('allDay')}
                        ";
                    ?>
                </label>
            </dd>
        </dl>

    </fieldset>

    <fieldset>
        <?php
            $dateHelp = View::translateDateString(DATE_FORMAT);
            $timeHelp = View::translateDateString(TIME_FORMAT);
            $dateSize = strlen($dateHelp);
            $timeSize = strlen($timeHelp);

            $recur = $this->event->getRRule();
            if (!$recur) { $recur = new Rule(); }
        ?>
        <dl class="dateRange">
            <dt><label  for="start"><?= $this->_('startDate'); ?></label></dt>
            <dd><input name="start[date]" id="start"
                    placeholder="<?= $dateHelp; ?>" size="<?= $dateSize; ?>" maxlength="<?= $dateSize; ?>"
                    value="<?= $this->event->getStart(DATE_FORMAT); ?>" />
                <input name="start[time]" id="startTime"
                    placeholder="<?= $timeHelp; ?>" size="<?= $timeSize; ?>" maxlength="<?= $timeSize; ?>"
                    value="<?= $this->event->getStart(TIME_FORMAT); ?>" />
            </dd>
        </dl>
        <dl class="dateRange">
            <dt><label  for="end"><?= $this->_('endDate'); ?></label></dt>
            <dd><input name="end[date]" id="end"
                    placeholder="<?= $dateHelp; ?>" size="<?= $dateSize; ?>" maxlength="<?= $dateSize; ?>"
                    value="<?= $this->event->getEnd(DATE_FORMAT); ?>" />
                <input name="end[time]" id="endTime"
                    placeholder="<?= $timeHelp; ?>" size="<?= $timeSize; ?>" maxlength="<?= $timeSize; ?>"
                    value="<?= $this->event->getEnd(TIME_FORMAT); ?>" />
            </dd>
        </dl>
        <dl>
            <dt><label   for="frequency"><?= $this->_('FREQ'); ?></label></dt>
            <dd><select name="frequency" id="frequency"><option value=""></option>
                <?php
                    foreach (['DAILY', 'WEEKLY', 'MONTHLY'] as $f) {
                        $selected = $recur->getFreqAsText()==$f
                            ? 'selected="selected"'
                            : '';
                        echo "<option value=\"$f\" $selected>{$this->_($f)}</option>";
                    }
                ?>
                </select>
            </dd>
        </dl>
    </fieldset>

    <fieldset id="DAILY"><legend><?= $this->_('DAILY'); ?></legend>
        <dl>
            <dt><?= $this->_('INTERVAL'); ?></dt>
            <dd><input name="DAILY[interval]" size="1" value="<?= $recur->getInterval(); ?>" /> days</dd>
        </dl>
    </fieldset>

    <fieldset id="WEEKLY">
        <legend><span><?= $this->_('WEEKLY'); ?></span></legend>
        <dl>
            <dt><?= $this->_('INTERVAL'); ?></dt>
            <dd><input name="WEEKLY[interval]" size="1" value="<?= $recur->getInterval(); ?>" /> weeks</dd>
        </dl>
        <dl>
            <dt><?= $this->_('BYDAY'); ?></dt>
            <dd><?php
                    $days  = ['SU', 'MO', 'TU', 'WE', 'TH', 'FR', 'SA'];
                    $byday = $recur->getByDay();

                    foreach ($days as $day) {
                        $checked = ($byday && in_array($day, $byday))
                            ? 'checked="checked"'
                            : '';
                        echo "
                        <label>
                            <input name=\"WEEKLY[byday][$day]\" type=\"checkbox\" $checked />
                            {$this->_($day)}
                        </label>
                        ";
                    }
                ?>
            </dd>
        </dl>
    </fieldset>

    <fieldset id="MONTHLY"><legend><?= $this->_('MONTHLY'); ?></legend>
        <dl>
            <dt>Monthly recursion</dt>
            <dd><?php
                    $bymonthday = $recur->getByMonthDay();
                    $checked = (count($bymonthday) || !count($byday))
                        ? 'checked="checked"'
                        : '';
                ?>
                <input name="MONTHLY[type]" type="radio" value="BYMONTHDAY" <?= $checked; ?> />
                <label>Day      <input name="MONTHLY[bymonthday][daylist]"  size="2" value="<?= !empty($bymonthday[0]) ? $bymonthday[0] : 30; ?>" /></label>
                <label>of every <input name="MONTHLY[bymonthday][interval]" size="1" value="<?= $recur->getInterval(); ?>" /> month</label>
            </dd>
            <dd><?php
                    $checked = count($byday) ? 'checked="checked"' : '';
                ?>
                <input name="MONTHLY[type]" type="radio" value="BYDAY" <?php $checked; ?> />
    			<label>The
    				<select name="MONTHLY['byday'][offset]">
                    <?php
                        $weekdays = $recur->getByDayTransformedToWeekdays();
                        $offset = count($weekdays) ? $weekdays[0]->num : null;
                        $offsets = [
                            1  => 'First',
                            2  => 'Second',
                            3  => 'Third',
                            4  => 'Fourth',
                            -2 => 'Second To Last',
                            -1 => 'Last'
                        ];

                        foreach ($offsets as $num => $label) {
                            $selected = $offset == $num
                                ? 'selected="selected"'
                                : '';
                            echo "<option value=\"$num\" $selected>$label</option>";
                        }
                    ?>
    				</select>
    			</label>
    			<label>
    				<select name="MONTHLY[byday][day]">
                    <?php
                        $byday = [];
                        foreach ($weekdays as $weekday) { $byday[] = $weekday->weekday; }

                        foreach ($days as $day) {
                            $selected = in_array($day, $byday)
                                ? 'selected="selected"'
                                : '';
                            echo "<option value=\"$day\" $selected>{$this->_($day)}</option>";
                        }
                    ?>
    				</select>
    				of every
    			</label>
    			<label><input name="MONTHLY[byday][interval]" size="1" value="<?= $recur->getInterval(); ?>" /> month</label>
            </dd>
        </dl>
    </fieldset>

    <fieldset id="RRULE_END">
        <?php
            $count = $recur->getCount();
            $until = $recur->getUntil();
            $type  = $count ? 'count' : 'until';

            if (!$count) { $count = 10; }
            if (!$until) { $until = new \DateTime('+2 weeks'); }

            $checked = 'checked="checked"';
        ?>
        <dl>
            <dt>End date</dt>
            <dd><label><input name="RRULE_END[type]" type="radio" value="count" <?= $type=='count' ? $checked : ''; ?> />End after</label>
                 <label><input name="RRULE_END[count]" size="2" value="<?= $count; ?>" />occurances</label>
            </dd>
            <dd><label><input name="RRULE_END[type]" type="radio" value="until" <?= $type=='until' ? $checked : ''; ?> />End by</label>
                 <input name="RRULE_END[until][date]" size="7" value="<?= $until->format(DATE_FORMAT); ?>" />
                 <input name="RRULE_END[until][time]" value="23:59:59" type="hidden" />
            </dd>
        </dl>
    </fieldset>

    <fieldset>
        <dl>
            <dt><label     for="description"><?= $this->_('description'); ?></label></dt>
            <dd><textarea name="description" id="description"><?= View::escape($this->event->getDescription()); ?></textarea></dd>
        </dl>

        <button type="submit" class="save"><?= $this->template->_('save'); ?></button>
    </fieldset>
</form>
</div>
