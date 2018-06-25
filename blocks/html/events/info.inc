<?php
/**
 * @copyright 2014-2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Event  $this->event
 */
use Application\Models\Event;
use Application\Models\EventType;
use Application\Auth;

$title                 = parent::escape($this->event->getTitle());
$primaryContact        = parent::escape($this->event->getPrimaryContact());
$description           = parent::escape($this->event->getDescription());
$geography_description = parent::escape($this->event->getGeography_description());
$department            = parent::escape($this->event->getDepartment());
$geography             = $this->event->getGeography();

$id = $this->event->getId()
    ? $this->event->getId()
    : $this->event->getGoogle_event_id();

$typeClass = '';
$t = $this->event->getEventType();
if ($t) {
    $typeClass = "class=\"{$t->getCode()}\"";
}
?>
<article id="e_<?= $id; ?>" <?= $typeClass; ?>>
    <header>
        <div class="tools">
        <?php
            $helper = $this->template->getHelper('buttonLink');
            if (Auth::isAllowed('events', 'update')) {
                echo $helper->buttonLink(
                    BASE_URI.'/events/update?id='.$id,
                    $this->template->_('edit'),
                    'edit'
                );
            }
            if (Auth::isAllowed('events', 'history')) {
                echo $helper->buttonLink(
                    BASE_URI.'/events/history?id='.$id,
                    $this->template->_('eventHistory'),
                    'history'
                );
            }
            if (Auth::isAllowed('events', 'delete')) {
                $uri = BASE_URI.'/events/delete?id='.$id;
                echo "
                <a href=\"$uri\" class=\"delete button\"
                    onclick=\"return PHP.deleteConfirmation();\">
                    {$this->_('delete')}
                </a>";
            }
        ?>
        </div>
        <h2 class="title"><?= $title; ?></h2>
        <h2><?= $geography_description; ?></h2>
    </header>
    <div class="dates"><?= $this->event->getHumanReadableDuration('F j, Y'); ?></div>
    <?php

        if ($description) {
            echo "\t<div class=\"description\">$description</div>\n";
        }
        if ($primaryContact  && Auth::isAllowed('events', 'viewPrimaryContact')) {
            echo "\t<div class=\"primaryContact\">$primaryContact</div>\n";
        }

        if ($department) { echo "\t<div class=\"department\">$department</div>\n"; }
        if ($geography ) { echo "\t<div class=\"geography\" >$geography</div>\n";  }
    ?>
</article>
