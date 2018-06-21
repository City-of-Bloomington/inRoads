<?php
/**
 * @copyright 2014-2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Event $this->event
 */
use Application\Models\Event;
use Application\Models\EventType;
use Application\Templates\Helpers\Field;

use Recurr\Rule;
use Recurr\WeekDay;

$this->template->addToAsset('scripts', BASE_URI.'/js/events/updateForm.js');

$department_id = $this->event->getDepartment_id() ? $this->event->getDepartment_id() : $_SESSION['USER']->getDepartment_id();
$eventType_id  = $this->event->getEventType_id();
$title                 = parent::escape($this->event->getTitle());
$primaryContact        = parent::escape($this->event->getPrimaryContact());
$description           = parent::escape($this->event->getDescription());
$geography_description = parent::escape($this->event->getGeography_description());
?>
<div class="editPanel">
<form method="post" id="eventUpdateForm" action="<?= BASE_URI; ?>/events/update">
    <fieldset>
        <legend><?= $this->_('event_info'); ?></legend>
        <p><?= $this->_('event_disclaimer', 'messages'); ?></p>

        <input name="id"              type="hidden" value="<?= $this->event->getId(); ?>" />
        <input name="google_event_id" type="hidden" value="<?= $this->event->getGoogle_event_id(); ?>" />
        <input name="geography"       type="hidden" value="<?= $this->event->getGeography(); ?>" id="geography" />
        <?php
            $h = $this->template->getHelper('field');


            $options = [];
            foreach (Event::validDepartments($_SESSION['USER']) as $d) {
                $options[] = ['value'=>$d->getId(), 'label'=>parent::escape($d->getName())];
            }
            echo $h->field([
                'name'     => 'department_id',
                'id'       => 'department_id',
                'label'    => $this->_('department'),
                'value'    => $department_id,
                'type'     => 'select',
                'options'  => $options,
                'required' => true
            ]);

            $options = [];
            foreach (EventType::types() as $t) {
                $options[] = ['value'=>$t->getId(), 'label'=>parent::escape($t->getName())];
            }
            echo $h->field([
                'name'     => 'eventType_id',
                'id'       => 'eventType_id',
                'label'    => $this->_('eventType'),
                'value'    => $eventType_id,
                'type'     => 'select',
                'options'  => $options,
                'required' => true
            ]);

        ?>
        <dl>
            <dd><label>
                    <input name="constructionFlag" type="checkbox"
                        <?= $this->event->isConstruction() ? 'checked="true"' : ''; ?> />
                    <?= $this->_('constructionFlag'); ?>
                </label>
            </dd>
        </dl>
        <?php
            echo $h->field([
                'name'     => 'title',
                'id'       => 'title',
                'label'    => $this->_('title'),
                'value'    => $title,
                'help'     => $this->_('title_help', 'messages'),
                'type'     => 'textarea',
                'required' => true
            ]);
            echo $h->field([
                'name'     => 'geography_description',
                'id'       => 'geography_description',
                'label'    => $this->_('geography_description'),
                'value'    => $geography_description,
                'help'     => $this->_('geography_description_help', 'messages'),
                'type'     => 'textarea',
                'required' => true
            ]);
        ?>
    </fieldset>

    <fieldset>
        <legend><span><?= $this->_('dateTimeInfo') ?></span></legend>
        <?php
            $date = $this->event->getStartDate();
            $start_date = $date ? $date->format('Y-m-d') : '';

            $date = $this->event->getEndDate();
            $end_date = $date ? $date->format('Y-m-d') : '';

            $time = $this->event->getStartTime();
            $start_time = $time ? $time->format('H:i') : '';

            $time = $this->event->getEndTime();
            $end_time = $time ? $time->format('H:i') : '';

            $recur = $this->event->getRRule();
            if (!$recur) { $recur = new Rule(); }
        ?>
        <dl>
            <dt class="visuallyHidden">Details</dt>
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

        <dl class="dateRange required">
            <dt><label  for="start"><?= $this->_('startDate'); ?></label></dt>
            <dd><input name="start[date]" id="start"     type="date" value="<?= $start_date; ?>" required="true" pattern="<?= Field::DATE_REGEX; ?>" placeholder="<?= Field::DATE_PLACEHOLDER; ?>" />
                <input name="start[time]" id="startTime" type="time" value="<?= $start_time; ?>" />
            </dd>
        </dl>
        <dl class="dateRange required">
            <dt><label  for="end"><?= $this->_('endDate'); ?></label></dt>
            <dd><input name="end[date]" id="end"     type="date" value="<?= $end_date; ?>" required="true" pattern="<?= Field::DATE_REGEX; ?>" placeholder="<?= Field::DATE_PLACEHOLDER; ?>" />
                <input name="end[time]" id="endTime" type="time" value="<?= $end_time; ?>" />
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
                        $checked = ($bymonthday || !$byday)
                            ? 'checked="checked"'
                            : '';
                    ?>
                    <input name="MONTHLY[type]" type="radio" value="BYMONTHDAY" <?= $checked; ?> />
                    <label>Day      <input name="MONTHLY[bymonthday][daylist]"  size="2" value="<?= !empty($bymonthday[0]) ? $bymonthday[0] : 30; ?>" /></label>
                    <label>of every <input name="MONTHLY[bymonthday][interval]" size="1" value="<?= $recur->getInterval(); ?>" /> month</label>
                </dd>
                <dd><?php
                        $checked = $byday ? 'checked="checked"' : '';
                    ?>
                    <input name="MONTHLY[type]" type="radio" value="BYDAY" <?php $checked; ?> />
        			<label>The
        				<select name="MONTHLY['byday'][offset]">
                        <?php
                            $weekdays = $recur->getByDayTransformedToWeekdays();
                            $offset   = $weekdays ? $weekdays[0]->num : null;
                            $offsets  = [
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
            <dl><dt><?= $this->_('RRULE_END'); ?></dt>
                <dd><label>
                        <input name="RRULE_END[type]" type="radio" value="count" <?= $type=='count' ? $checked : ''; ?> />
                        <?= $this->_('RRULE_COUNT'); ?>
                    </label>
                    <label><input name="RRULE_END[count]" size="2" value="<?= $count; ?>" />occurances</label>
                </dd>
                <dd><label>
                        <input name="RRULE_END[type]" type="radio" value="until" <?= $type=='until' ? $checked : ''; ?> />
                        <?= $this->_('RRULE_UNTIL'); ?>
                    </label>
                     <input name="RRULE_END[until][date]" size="7" value="<?= $until->format(DATE_FORMAT); ?>" />
                     <input name="RRULE_END[until][time]" value="23:59:59" type="hidden" />
                </dd>
            </dl>
        </fieldset>

    </fieldset>

    <fieldset>
        <?php
            $cur_length = strlen($description);
            $max_length = Event::MAX_DESCRIPTION_LENGTH;

            echo $h->field([
                'name'     => 'description',
                'id'       => 'description',
                'label'    => $this->_('description'),
                'value'    => $description,
                'required' => true,
                'type'     => 'textarea',
                'help'     => "<div><span id=\"description_length\">$cur_length</span> / $max_length</div>",
                'attr'     => [
                    'maxlength' => $max_length
                ]
            ]);
            echo $h->field([
                'name'     => 'primaryContact',
                'id'       => 'primaryContact',
                'label'    => $this->_('primaryContact'),
                'value'    => $primaryContact,
                'type'     => 'textarea'
            ]);
        ?>

        <button type="submit" class="save"><?= $this->template->_('save'); ?></button>
    </fieldset>
</form>
</div>
