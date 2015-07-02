<?php
/**
 * @copyright 2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param array $this->events An array of Events
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
        $block->event = $e;
        echo $block->render('html', $this->template);
    }
?>
</div>
