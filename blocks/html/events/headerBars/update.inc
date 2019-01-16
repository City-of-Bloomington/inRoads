<?php
/**
 * @copyright 2015-2019 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param Event $this->event
 */
$id = $this->event->getId()
    ? $this->event->getId()
    : $this->event->getGoogle_event_id();

$url = $this->event->getId()
    ? BASE_URI.'/events/view?id='.$id
    : BASE_URI.'/events';

$helper = $this->template->getHelper('buttonLink');
echo $helper->buttonLink($url, $this->template->_('back'), 'back');
?>
<div class="pillNav">
<?php
    $classCurrent = !empty($_GET['id']) ? 'class="current"' : '';
    $url = BASE_URI.'/events/update';
    if ($id) { $url.= "?id=$id"; }
    echo "<a href=\"$url\" $classCurrent>{$this->_('event')}</a>";

    if ($id) {
        $classCurrent = !empty($_GET['event_id']) ? 'class="current"' : '';
        $url = BASE_URI."/segments?event_id=$id";
        echo "<a href=\"$url\" $classCurrent>{$this->_(['segment', 'segments', 2])}</a>";
    }
?>
</div>
