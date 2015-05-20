<?php
/**
 * @copyright 2014-2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Zend\Db\Result $this->events
 */
use Application\Models\Person;
use Application\Models\Event;
use Blossom\Classes\View;
?>
<section>
    <h1><?php echo $this->_(['event', 'events', 2]); ?></h1>
    <div class="tools">
    <?php
        if (Person::isAllowed('events', 'add')) {
            $helper = $this->template->getHelper('buttonLink');
            echo $helper->buttonLink(
                BASE_URI.'/events/update',
                $this->_('event_add'),
                'add'
            );
        }
    ?>
    </div>
    <?php
        foreach ($this->events as $e) {
            $url = BASE_URI.'/events/view?event_id='.$e->getId();

            $description = View::escape($e->getDescription());
            $location    = View::escape($e->getGeography_description());
            $startDate = $e->getStartDate(DATE_FORMAT);
            $endDate   = $e->getEndDate(DATE_FORMAT);

            $type = Event::$TYPES[$e->getEventType()];

            echo "
            <article>
                <header>
                    <h1><a href=\"$url\">$location</a></h1>
                    <div>$startDate through $endDate</div>
                    <div>{$e->getEventType()}; $type</div>
                    <p>$description</p>
                </header>
            </article>
            ";
        }
    ?>
</section>