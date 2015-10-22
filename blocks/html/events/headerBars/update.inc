<?php
/**
 * @copyright 2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Event $this->event
 */

$id = $this->event->getId()
    ? $this->event->getId()
    : $this->event->getGoogle_event_id();

?>
<div class="pillNav">
<?php
    $classCurrent = !empty($_GET['id']) ? ' class="current"' : '';
    $linkUrl = BASE_URI.'/events/update?id='.$id;
    echo "<a href=\"$linkUrl\"$classCurrent>Event</a>";
    $classCurrent = !empty($_GET['event_id']) ? ' class="current"' : '';
    $linkUrl = BASE_URI.'/segments?event_id='.$id;
    echo "<a href=\"$linkUrl\"$classCurrent>Segments</a>";
?>
</div>
<div class="headerBar-tools">
<?php
    $url = $this->event->getId()
        ? BASE_URI.'/events/view?id='.$id
        : BASE_URI.'/events';

    $helper = $this->template->getHelper('buttonLink');
    echo $helper->buttonLink($url, $this->template->_('cancel'), 'cancel');
?>
</div>
