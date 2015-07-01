<?php
/**
 * @copyright 2014-2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param array $this->events An array of Events
 * @param Event $this->event Optional: currently selected event
 */
use Application\Models\Person;
use Application\Models\Event;
use Application\Templates\Helpers\ButtonLink;
use Blossom\Classes\Block;
?>
<div class="schedule-container">
    <?php
        $block = new Block('events/info.inc');

        foreach ($this->events as $e) {
            $url = BASE_URI."/events/view?id={$e->getId()};return_uri=$_SERVER[REQUEST_URI]";
            $block->event = $e;

            echo "
                <article>
                    {$block->render('html', $this->template)}
                </article>
            ";
        }
    ?>
</div>
