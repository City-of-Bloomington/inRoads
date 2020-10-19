<?php
/**
 * @copyright 2014-2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param array $this->events An array of Events
 */
use Application\Auth;
use Application\Models\Event;
use Application\Templates\Helpers\ButtonLink;
use Application\Block;
?>
<div id="eventsList">
    <?php
        $helper = $this->template->getHelper('buttonLink');

        if ($this->events) {
            foreach ($this->events as $e) {
                $id = $e->getId()
                    ? $e->getId()
                    : $e->getGoogle_event_id();

                $url = "<a href=\"".BASE_URI."/events/view?id=$id\">{$this->_('event_info')}</a>";

                $classes = '';
                if ($e->getGeography()) { $classes = 'hasGeography'; }

                $t = $e->getEventType();
                if ($t) {
                    $classes.= ' '.lcfirst(str_replace(' ', '', ucwords($t)));
                }

                $title                 = parent::escape($e->getTitle());
                $description           = parent::escape($e->getDescription());
                $geography_description = parent::escape($e->getGeography_description());
                $department            = parent::escape($e->getDepartment());
                $geography             = $e->getGeography();

                $primaryContact = ($e->getPrimaryContact() && Auth::isAllowed('events', 'viewPrimaryContact'))
                                ? parent::escape($e->getPrimaryContact())
                                : '';

                echo "
                <details id=\"e_$id\" class=\"$classes\">
                    <summary>
                        <span class=\"title\">$title</span>
                        <span>$geography_description</span>
                        <span class=\"dates\">{$e->getHumanReadableDuration('F j, Y')}</span>
                    </summary>
                ";
                    foreach (['description', 'primaryContact', 'department', 'geography', 'url'] as $f) {
                        if (!empty($$f)) {
                            echo "\t<div class=\"$f\">{$$f}</div>\n";
                        }
                    }
                echo "
                </details>
                ";
            }
        }
        else {
            echo "
                <div class=\"emptyResult\">
                    <h2>{$this->_('noEventsReturned.heading', 'messages')}</h2>
                    <p> {$this->_('noEventsReturned.message', 'messages')}</p>
                </div>
            ";
        }
    ?>
</div>
