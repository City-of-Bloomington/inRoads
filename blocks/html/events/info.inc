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
<section>
    <h1><?php echo View::escape($this->event->getGeography_description()); ?></h1>
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
    <p><?= $this->event->getHumanReadableDuration('F j,Y'); ?></p>
    <?php
        $t = $this->event->getType();
        if ($t) {
            echo "<p>$t; ".Event::$types[$t]."</p>";
        }
    ?>

    <p><?= View::escape($this->event->getDescription()); ?></p>
</section>
