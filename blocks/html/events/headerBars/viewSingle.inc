<?php
/**
 * @copyright 2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Event $this->event
 */
use Application\Models\Person;

echo "<h1 class=\"headerBar-title\">{$this->template->title}</h1>";

if (Person::isAllowed('events', 'update')) {
    echo '<div class="headerBar-tools">';
        $return_uri = !empty($_GET['return_uri']) ? $_GET['return_uri'] : BASE_URI.'/events';
        $helper = $this->template->getHelper('buttonLink');
        echo $helper->buttonLink(
            $return_uri,
            $this->template->_('back'),
            'back'
        );
        echo $helper->buttonLink(
            BASE_URI.'/events/update?id='.$this->event->getId(),
            $this->template->_('event_edit'),
            'edit'
        );
    echo '</div>';
}
