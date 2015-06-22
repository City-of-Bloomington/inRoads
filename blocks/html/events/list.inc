<?php
/**
 * @copyright 2014-2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param array $this->events An array of Events
 */
use Application\Models\Person;
use Application\Models\Event;
use Blossom\Classes\Block;
?>
<div class="panelList">
    <div class="panelList-googleLinks">
        <a href="<?= 'https://www.google.com/calendar/feeds/'.GOOGLE_CALENDAR_ID.'/public/basic'; ?>">XML</a>
        <a href="<?= 'https://www.google.com/calendar/ical/' .GOOGLE_CALENDAR_ID.'/public/basic.ics'; ?>">iCal</a>
    </div>
    <div id="events">
    <?php
        $block = new Block('events/info.inc');

        foreach ($this->events as $e) {
            $url = BASE_URI."/events/view?id={$e->getId()};return_uri=$_SERVER[REQUEST_URI]";
            $block->event = $e;

            $classes = '';
            if ($e->getGeography()) { $classes.= ' hasGeography'; }

            $t = $e->getType();
            if ($t) {
                $t = lcfirst(str_replace(' ', '', ucwords($t)));
                $classes.= " $t";
            }

            echo "
            <a href=\"$url\" class=\"panelItem $classes\" id=\"e_{$e->getId()}\">
            {$block->render('html', $this->template)}
            </a>
            ";
        }
    ?>
    </div>
</div>
