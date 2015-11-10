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
use Application\Models\EventType;
use Blossom\Classes\View;
use Blossom\Classes\Url;

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

$this->template->addToAsset('scripts', BASE_URI.'/js/searchForm.js');
?>
<div class="filterPanel expanded" id="filterPanel">
    <?php
        $c = (!empty($_GET['view']) && $_GET['view'] === 'schedule')
            ? 'schedule'
            : 'map';

        $url = new Url(BASE_URL.'/events');
        $url->start      = !empty($_GET['start'])      ? $_GET['start']      : null;
        $url->end        = !empty($_GET['end'])        ? $_GET['end']        : null;
        $url->eventTypes = !empty($_GET['eventTypes']) ? $_GET['eventTypes'] : null;

        $options = '';
        $url->start = $today;
        $url->view = $c;
        foreach ($periods as $period => $datetime) {
            $date = $datetime->format(DATE_FORMAT);

            if ($start === $today && $end === $date) {
                $selected = 'class="current"';
                $currentPeriod = $period;
            }
            else {
                $selected = '';
            }
            $url->end = $date;
            $options.= "<a href=\"$url\" $selected role=\"option\" tabindex=\"-1\">{$this->_($period)}</a>";
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

    <form method="get" action="<?php echo BASE_URI.'/events'; ?>">
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
            <dl class="mod-checkbox filters-legend">
                <dt class="accessible-hide"><?= $this->_('eventTypeLegend'); ?></dt>
                <?php
                    foreach (EventType::types() as $type) {
                        $checked = in_array($type->getCode(), $this->filters['eventTypes'])
                            ? 'checked="checked"'
                            : '';
                        $name = View::escape($type->getName());
                        echo "
                        <dd class=\"{$type->getCode()}\">
                            <label>
                                <input type=\"checkbox\" name=\"eventTypes[]\" value=\"{$type->getCode()}\" $checked />
                                <span>$name</span>
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
