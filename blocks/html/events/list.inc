<?php
/**
 * @copyright 2014-2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
use Application\Models\Person;
use Application\Models\Event;
use Blossom\Classes\View;
?>
<section>
    <header class="windowPanel-header">
    <div class="windowPanel-title"><?= $this->_('upcoming_closures') ?></div>
        <div class="windowPanel-headerTools">
        <?php
            if (Person::isAllowed('events', 'add')) {
                $url = BASE_URI.'/events/update';
                echo "<a href=\"$url\" class=\"windowPanel-addButton\" title=\"{$this->_('event_add')}\">{$this->_('event_add')}</a>";
            }
        ?>
        </div>
    </header>
    <div class="panelList">
        <?php
            foreach ($this->events as $e) {
                $url = BASE_URI.'/events/view?id='.$e->getId();
    
                $description = View::escape($e->getDescription());
                $summary     = View::escape($e->getSummary());
    
                $format = $e->isAllDay() ? DATE_FORMAT : DATETIME_FORMAT;
    
                #$geography = $e->getGeography();
                #if ($geography) {
    
                #}
                ?>
                    <a href="<?= $url ?>" class="panelItem">
                        <span class="panelItem-title"><?= $summary ?></span>
                        <span class="panelItem-dates"><?= $e->getStart($format) ?> through <?= $e->getEnd($format) ?></span>
                    </a>
                <?php
            }
        ?>
    </div>
</section>