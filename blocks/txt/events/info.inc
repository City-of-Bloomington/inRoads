<?php
/**
 * @copyright 2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Event $this->event
 */
use Application\Models\Event;

$t = $this->event->getType();
$type = $t ? $t.'; '.Event::$types[$t] : '';

$url = BASE_URL.'/events/view?id='.$this->event->getId();

echo "
{$this->event->getGeography_description()}
{$this->event->getHumanReadableDuration('F j, Y')}
$type
{$this->event->getDescription()}
$url
";
