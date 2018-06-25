<?php
/**
 * @copyright 2015-2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
use Application\Auth;
use Blossom\Classes\Url;

echo '<div class="pillNav">';
    $c = (!empty($_GET['view']) && $_GET['view'] === 'schedule')
        ? 'schedule'
        : 'map';

    $url = new Url(BASE_URL.'/events');
    $url->start   = !empty($_GET['start'])   ? $_GET['start']   : null;
    $url->end     = !empty($_GET['end'])     ? $_GET['end']     : null;
    $url->filters = !empty($_GET['filters']) ? $_GET['filters'] : null;

    $current = $c === 'map' ? 'class="current"' : '';
    echo "<a href=\"$url\" $current>{$this->_('mapView')}</a>";

    $current = $c === 'schedule' ? 'class="current"' : '';
    $url->view = 'schedule';
    echo "<a href=\"$url\" $current>{$this->_('scheduleView')}</a>";
echo '</div>';

if (Auth::isAllowed('events', 'update')) {
    echo '<div class="headerBar-tools">';
        $helper = $this->template->getHelper('buttonLink');
        echo $helper->buttonLink(
            BASE_URI.'/events/update',
            $this->template->_('event_add'),
            'add'
        );
    echo '</div>';
}
