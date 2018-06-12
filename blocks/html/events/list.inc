<?php
/**
 * @copyright 2014-2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param array $this->events An array of Events
 * @param Event $this->event  Optional: currently selected event
 */
use Application\Models\Person;
use Application\Models\Event;
use Application\Templates\Helpers\ButtonLink;
use Blossom\Classes\Block;
?>
<div class="panelList">
    <?php
        $block = new Block('events/info.inc');
        $helper = $this->template->getHelper('buttonLink');

        if ($this->events) {
            echo "<div id=\"events\">";
                foreach ($this->events as $e) {
                    $event_id = $e->getId()
                              ? $e->getId()
                              : $e->getGoogle_event_id();

                    $url = BASE_URI."/events/view?id=$event_id";
                    $block->event = $e;

                    $classes = '';
                    if ($e->getGeography()) { $classes.= ' hasGeography'; }

                    $t = $e->getEventType();
                    if ($t) {
                        $t = lcfirst(str_replace(' ', '', ucwords($t)));
                        $classes.= " $t";
                    }

                    $editButton = '';
                    if (Person::isAllowed('events', 'update')
                        && $e->permitsEditingBy($_SESSION['USER'])) {

                        $editButton = $helper->buttonLink(
                            BASE_URI.'/events/update?id='.$event_id,
                            $this->_('event_edit'),
                            'edit',
                            ButtonLink::SIZE_ICON
                        );
                    }

                    $current = (isset($this->event) && $e->getId()===$this->event->getId())
                        ? 'current'
                        : '';

                    echo "
                    <div class=\"panelItem-container $classes\">
                        <a href=\"$url\" class=\"panelItem $current\" >
                            {$block->render('html', $this->template)}
                        </a>
                        $editButton
                    </div>
                    ";
                }
            echo "</div>";
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
