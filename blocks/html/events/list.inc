<?php
/**
 * @copyright 2014-2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
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
            $url = BASE_URI.'/events/view?id='.$e->getId();

            $description = View::escape($e->getDescription());
            $summary     = View::escape($e->getSummary());

            $format = $e->isAllDay() ? DATE_FORMAT : DATETIME_FORMAT;

            #$geography = $e->getGeography();
            #if ($geography) {

            #}

            echo "
            <article>
                <header>
                    <h1><a href=\"$url\">$summary</a></h1>
                    <div>{$e->getStart($format)} through {$e->getEnd($format)}</div>
                </header>
            </article>
            ";
        }
    ?>
</section>