<div class="panelList">
<?php
/**
 * @copyright 2014-2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param array $this->events An array of Events
 */
use Application\Models\Person;
use Application\Models\Event;
use Blossom\Classes\View;
?>
    <div class="panelList-googleLinks">
        <a href="#">Data source</a>
    </div>
<?php
foreach ($this->events as $e) {
    $url = BASE_URI."/events/view?id={$e->getId()};return_uri=$_SERVER[REQUEST_URI]";

    $description = View::escape($e->getDescription());
    $summary     = View::escape($e->getGeography_description());

    $format = $e->isAllDay() ? DATE_FORMAT : DATETIME_FORMAT;

    #$geography = $e->getGeography();
    #if ($geography) {

    #}
    echo "
    <a href=\"$url\" class=\"panelItem\">
        <span class=\"panelItem-title\">$summary</span>
        <span class=\"panelItem-dates\">{$e->getStart($format)} &ndash; {$e->getEnd($format)}</span>
    </a>
    ";
}
?>
</div>
