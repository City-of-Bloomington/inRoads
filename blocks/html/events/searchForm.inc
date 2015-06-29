<?php
/**
 * @copyright 2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param DateTime $this->start
 * @param DateTime $this->end
 * @param array $this->filters
 */
use Application\Models\Event;

$periods = [
    'today'     => new \DateTime(),
    'tomorrow'  => new \DateTime('+1 day'),
    'nextWeek'  => new \DateTime('+1 week'),
    'nextMonth' => new \DateTime('+1 month')
];

$today = $periods['today']->format(DATE_FORMAT);

$start = $this->start ? $this->start->format(DATE_FORMAT) : '';
$end   = $this->end   ? $this->end  ->format(DATE_FORMAT) : '';

$help = $this->translateDateString(DATE_FORMAT);
$size = strlen($help);

$url = BASE_URI.'/events';
$this->template->addToAsset('scripts', BASE_URI.'/js/searchForm.js');
?>
<div class="filterPanel expanded" id="filterPanel">
    <?php
        $options = '';
        $a = "<a href=\"$url?start=$today;end=%s\" %s role=\"option\" tabindex=\"-1\">%s</a>";
        foreach ($periods as $period => $datetime) {
            $date = $datetime->format(DATE_FORMAT);

            if ($start === $today && $end === $date) {
                $selected = 'class="current"';
                $currentPeriod = $period;
            }
            else {
                $selected = '';
            }
            $options.= sprintf($a, $date, $selected, $this->_($period));
        }
        if (!isset($currentPeriod)) { $currentPeriod = $this->_('chooseDates'); }
    ?>
    <dl class="nav-dropdown">
        <dt id="dropdown-label1"><?= $this->_('searchPresets'); ?></dt>
        <dd>
            <div class="nav-dropdown-current" role="button" aria-haspopup="true" tabindex="0"><?= $this->_($currentPeriod); ?></div>
            <div class="nav-dropdown-options" role="listbox" aria-labeledby="dropdown-label1">
                <?= $options; ?>
            </div>
        </dd>
    </dl>

    <form method="get" action="<?php echo $url; ?>">
        <fieldset id="dateFieldSet">
            <dl class="dateRange">
                <dt><label for="inputStartDate"><?= $this->_('startDate'); ?></label></dt>
                <dd><input id= "inputStartDate" name="start" value="<?= $start; ?>"
                        size="<?= $size; ?>" placeholder="<?= $help; ?>" />
                </dd>
            </dl>
            <dl class="dateRange">
                <dt><label for="inputEndDate"><?= $this->_('endDate'); ?></label></dt>
                <dd><input id= "inputEndDate" name="end" value="<?= $end; ?>"
                        size="<?= $size; ?>" placeholder="<?= $help; ?>" />
                </dd>
            </dl>
        </fieldset>
        <fieldset>
            <legend><?= $this->_('eventTypeLegend'); ?></legend>
            <dl class="mod-checkbox">
                <dt></dt>
                <?php
                    global $EVENT_TYPES;
                    foreach ($EVENT_TYPES as $type=>$info) {
                        $checked = in_array($type, $this->filters['eventTypes'])
                            ? 'checked="checked"'
                            : '';

                        echo "
                        <dd><label>
                                <input type=\"checkbox\" name=\"eventTypes[]\" value=\"$type\" $checked />
                                <span>$type<span>
                            </label>
                        </dd>
                        ";
                    }
                ?>
            </dl>
        </fieldset>
    <button type="submit" class="search"><?= $this->_('search'); ?></button>
    </form>
</div>
