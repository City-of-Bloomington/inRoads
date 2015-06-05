<?php
/**
 * @copyright 2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param DateTime $this->start
 * @param DateTime $this->end
 */
$now             = new \DateTime();
$endOf_today     = new \DateTime('tomorrow');
$endOf_nextWeek  = new \DateTime('+1 week');
$endOf_nextMonth = new \DateTime('+1 month');

$now             =             $now->format(DATE_FORMAT);
$endOf_today     =     $endOf_today->format(DATE_FORMAT);
$endOf_nextWeek  =  $endOf_nextWeek->format(DATE_FORMAT);
$endOf_nextMonth = $endOf_nextMonth->format(DATE_FORMAT);

$start = $this->start ? $this->start->format(DATE_FORMAT) : '';
$end   = $this->end   ? $this->end  ->format(DATE_FORMAT) : '';

$help = $this->translateDateString(DATE_FORMAT);
$size = strlen($help);

$url = BASE_URI.'/events';
$this->template->addToAsset('scripts', BASE_URI.'/js/searchForm.js');
?>
<div id="searchPanel">
    <?php
        $options = '';
        foreach (['today', 'nextWeek', 'nextMonth'] as $period) {
            $a = "<a href=\"$url?start=$now;end=%s\" %s role=\"menuItem\" tabindex=\"-1\">%s</a>";
            $var = "endOf_$period";

            if ($start === $now && $end === $$var) {
                $selected = 'class="current"';
                $currentPeriod = $period;
            }
            else {
                $selected = '';
            }

            $options.= sprintf($a, $$var, $selected, $this->_($period));
        }
        if (!isset($currentPeriod)) { $currentPeriod = $this->_('chooseDates'); }
    ?>
    <div class="selector">
        <div class="selector-current" role="button"           id="currentPeriod" aria-haspopup="true" aria-expanded="true"><?= $currentPeriod; ?></div>
        <div class="selector-options" role="menu" aria-labeledby="currentPeriod">
            <?= $options; ?>
        </div>
    </div>

    <form method="get" action="<?php echo $url; ?>">
        <fieldset>
            <dl><dt><?= $this->_('startDate'); ?></dt>
                <dd><input name="start" value="<?= $start; ?>"
                        size="<?= $size; ?>" placeholder="<?= $help; ?>" />
                </dd>
            </dl>
            <dl><dt><?= $this->_('endDate'); ?></dt>
                <dd><input name="end" value="<?= $end; ?>"
                        size="<?= $size; ?>" placeholder="<?= $help; ?>" />
                </dd>
            </dl>
            <button type="submit" class="search"><?= $this->_('search'); ?></button>
        </fieldset>
    </form>
</div>
