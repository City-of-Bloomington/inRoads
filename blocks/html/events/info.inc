<?php
/**
 * @copyright 2014-2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Event $this->event
 */
use Application\Models\Event;
use Blossom\Classes\View;

global $EVENT_TYPES;
?>
<h2 class="title"><?php echo View::escape($this->event->getGeography_description()); ?></h2>
<div class="dates"><?= $this->event->getHumanReadableDuration('F j, Y'); ?></div>
<?php
    $t = $this->event->getType();
    if ($t) {
        echo "<div class=\"type\">$t; {$EVENT_TYPES[$t]['description']}</div>";
    }

    $description = View::escape($this->event->getDescription());
    if ($description) {
        echo "<div class=\"description\">$description</div>";
    }

    $geography = $this->event->getGeography();
    if ($geography) {
        echo "<div class=\"geography\">$geography</div>";
    }
?>
