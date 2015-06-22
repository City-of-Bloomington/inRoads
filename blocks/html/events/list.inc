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
        <a href="#">Data source</a>
    </div>
    <div id="events">
    <?php
        $block = new Block('events/info.inc');

        foreach ($this->events as $e) {
            $url = BASE_URI."/events/view?id={$e->getId()};return_uri=$_SERVER[REQUEST_URI]";
            $block->event = $e;

            echo "<a href=\"$url\" class=\"panelItem\" id=\"e_{$e->getId()}\">";
            echo $block->render('html', $this->template);
            echo "</a>";
        }
    ?>
    </div>
</div>
