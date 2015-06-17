<?php
/**
 * @copyright 2014-2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Event $this->event
 */
use Application\Models\Event;
use Application\Models\Person;
use Blossom\Classes\View;
?>
<section class="detailPanel">
    <h1 class="detailPanel-title"><?php echo View::escape($this->event->getGeography_description()); ?></h1>
    <div class="detailPanel-dates"><?= $this->event->getHumanReadableDuration('F j,Y'); ?></div>
    <div class="tools">
    <?php
        if (Person::isAllowed('events', 'edit')) {
            $helper = $this->template->getHelper('buttonLink');
            echo $helper->buttonLink(
                BASE_URI.'/events/update?id='.$this->event->getId(),
                $this->_('event_edit'),
                'edit'
            );
        }
    ?>
    </div>
    <?php
        $t = $this->event->getType();
        if ($t) {
            echo "<p>$t; ".Event::$types[$t]."</p>";
        }
    ?>

    <div class="detailPanel-description"><?= View::escape($this->event->getDescription()); ?></div>
</section>
