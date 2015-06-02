<?php
/**
 * @copyright 2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param array $this->events An array of Events
 * @param DateTime $this->start
 * @param DateTime $this->end
 */
use Application\Models\Person;
use Application\Models\Event;
use Blossom\Classes\View;
?>
<section>
    <header class="windowPanel-header">
        <div class="windowPanel-title"><?= $this->_('upcoming_closures') ?></div>
        <?php
            if (Person::isAllowed('events', 'add')) {
                $helper = $this->template->getHelper('buttonLink');

                echo '<div class="windowPanel-headerTools">';
                echo $helper->buttonLink(
                    BASE_URI.'/events/update',
                    $this->_('event_add'),
                    'windowPanel-addButton'
                );
                echo '</div>';
            }
        ?>
    </header>
    <?php include APPLICATION_HOME.'/blocks/html/events/searchForm.inc'; ?>
    <div class="panelList">
        <?php include APPLICATION_HOME.'/blocks/html/events/list.inc'; ?>
    </div>
</section>
