<?php
/**
 * @copyright 2014-2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Event  $this->event
 * @param string $this->link  Specifies what to draw a link around.
 */
use Application\Models\Event;
use Application\Models\EventType;
use Application\Models\Person;

$title                 = parent::escape($this->event->getTitle());
$primaryContact        = parent::escape($this->event->getPrimaryContact());
$description           = parent::escape($this->event->getDescription());
$geography_description = parent::escape($this->event->getGeography_description());
$department            = parent::escape($this->event->getDepartment());
$geography             = $this->event->getGeography();

$id = $this->event->getId()
    ? $this->event->getId()
    : $this->event->getGoogle_event_id();

if ($this->link && $this->link === 'title') {
    $url = BASE_URI."/events/view?id=$id";
    $title = "<a href=\"$url\">$title</a>";
}

$typeClass = '';
$t = $this->event->getEventType();
if ($t) {
    $tn = parent::escape($t->getName());
    $td = parent::escape($t->getDescription());
    $typeDiv   = "<div class=\"type\">$tn; $td</div>";
    $typeClass = "class=\"{$t->getCode()}\"";
}
?>
<article id="e_<?= $id; ?>" <?= $typeClass; ?>>
    <h2 class="title"><?= $title; ?></h2>
    <h2><?= $geography_description; ?></h2>
    <div class="dates"><?= $this->event->getHumanReadableDuration('F j, Y'); ?></div>
    <?php

        if ($description) {
            echo "\t<div class=\"description\">$description</div>\n";
        }
        if ($primaryContact  && Person::isAllowed('events', 'viewPrimaryContact')) {
            echo "\t<div class=\"primaryContact\">$primaryContact</div>\n";
        }

        if ($department) { echo "\t<div class=\"department\">$department</div>\n"; }
        if ($geography ) { echo "\t<div class=\"geography\" >$geography</div>\n";  }
    ?>
</article>
