<?php
/**
 * @copyright 2014-2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param array $this->events An array of Events
 * @param Event $this->event Optional: currently selected event
 */
use Application\Models\Person;
use Application\Models\Event;
use Application\Templates\Helpers\ButtonLink;
use Blossom\Classes\Block;
?>
<div class="panelList">
    <div id="events">
    <?php
        $block = new Block('events/info.inc');
        $helper = $this->template->getHelper('buttonLink');

        foreach ($this->events as $e) {
            $url = BASE_URI."/events/view?id={$e->getId()}";
            $block->event = $e;

            $classes = '';
            if ($e->getGeography()) { $classes.= ' hasGeography'; }

            $t = $e->getType();
            if ($t) {
                $t = lcfirst(str_replace(' ', '', ucwords($t)));
                $classes.= " $t";
            }

            $editButton = '';
            if (Person::isAllowed('events', 'update')) {
                $editButton = $helper->buttonLink(
                    BASE_URI.'/events/update?id='.$e->getId(),
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
    ?>
    </div>
</div>
