<?php
/**
 * @copyright 2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param DateTime $this->start
 * @param DateTime $this->end
 */

$help = $this->translateDateString(DATE_FORMAT);
$size = strlen($help);

$now       = new \DateTime();
$tomorrow  = new \DateTime('tomorrow');
$nextWeek  = new \DateTime('+1 week');
$nextMonth = new \DateTime('+1 month');

$now       =       $now->format(DATE_FORMAT);
$tomorrow  =  $tomorrow->format(DATE_FORMAT);
$nextWeek  =  $nextWeek->format(DATE_FORMAT);
$nextMonth = $nextMonth->format(DATE_FORMAT);

$start = $this->start ? $this->start->format(DATE_FORMAT) : '';
$end   = $this->end   ? $this->end  ->format(DATE_FORMAT) : '';

$url = BASE_URI.'/events';
$this->template->addToAsset('scripts', BASE_URI.'/js/searchForm.js');
?>
<div id="searchPanel">
    <nav>
        <a href="<?= "$url?start=$now;end=$tomorrow";  ?>"><?= $this->_('today'); ?></a>
        <a href="<?= "$url?start=$now;end=$nextWeek";  ?>"><?= $this->_('nextWeek'); ?></a>
        <a href="<?= "$url?start=$now;end=$nextMonth"; ?>"><?= $this->_('nextMonth'); ?></a>
    </nav>

    <form method="get" action="<?php echo BASE_URI; ?>/events">
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
