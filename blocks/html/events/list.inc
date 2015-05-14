<?php
/**
 * @copyright 2014-2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Zend\Db\Result $this->events
 */
use Application\Models\Person;
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
            $jurisdiction = View::escape($e->getJurisdiction()->getName());

            $url = BASE_URI.'/events/view?event_id='.$e->getId();

            $description = View::escape($e->getDescription());
            $getDetour   = View::escape($e->getDetour());

            echo "
            <article>
                <header>
                    <h1><a href=\"$url\">{$e->getSeverity()} {$e->getEventType()}</a>
                        <span class=\"date\">{$e->getCreated(DATE_FORMAT)}</span>
                    </h1>
                    <p>$description</p>
                    <p>$getDetour<p>
                </header>
            </article>
            ";
        }
    ?>
</section>