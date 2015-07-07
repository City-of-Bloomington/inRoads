<?php
/**
 * @copyright 2014-2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Event $this->event
 * @param string $this->link Specifies what to draw a link around.
 */
use Application\Models\Event;
use Application\Models\EventType;
use Blossom\Classes\View;

$title = View::escape($this->event->getGeography_description());
if ($this->link && $this->link === 'title') {
    $url = BASE_URI."/events/view?id={$this->event->getId()}";
    $title = "<a href=\"$url\">$title</a>";
}

$typeClass = '';
$t = $this->event->getType();
if ($t) {
    $tn = View::escape($t->getName());
    $td = View::escape($t->getDescription());
    $typeDiv   = "<div class=\"type\">$tn; $td</div>";
    $typeClass = "class=\"{$t->getCode()}\"";
}
?>
<article id="e_<?= $this->event->getId(); ?>" <?= $typeClass; ?>>
    <h2 class="title"><?= $title; ?></h2>
    <div class="dates"><?= $this->event->getHumanReadableDuration('F j, Y'); ?></div>
    <?php

        $description = View::escape($this->event->getDescription());
        if ($description) {
            echo "<div class=\"description\">$description</div>";
        }

        $geography = $this->event->getGeography();
        if ($geography) {
            echo "<div class=\"geography\">$geography</div>";
        }
    ?>
</article>
