<?php
/**
 * @copyright 2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Event $this->event
 */
declare (strict_types=1);
use Application\Models\EventHistory;

$fields = [
    'department_id', 'eventType_id', 'google_event_id',
    'title', 'description', 'constructionFlag',
    'geography_description', 'geography',
    'startDate', 'startTime', 'endDate', 'endTime', 'rrule'
];
?>
<section>
    <header><h3><?= $this->_('eventHistory'); ?></h3></header>
    <?php
        foreach ($this->event->getHistory() as $i=>$h) {
            $date    = $h->getDate(DATE_FORMAT);
            $person  = parent::escape($h->getPerson()->getFullname());
            $action  = parent::escape($h->getAction());
            $changes = $h->getChanges();
            echo "
            <article class=\"changeLogEntry\">
                <header>
                    <h4>$date: $person $action</h4>
                    <div class=\"tools\">
                        <button        id=\"changeLogEntry-control-$i\"
                            aria-controls=\"changeLogEntry-content-$i\" aria-expanded=\"true\">
                            {$this->_('expand')}
                        </button>
                    </div>
                </header>
                <div id=\"changeLogEntry-content-$i\" aria-hidden=\"false\">
            ";
                foreach (EventHistory::$states as $state) {
                    if (    isset($changes[$state]) && count($changes[$state])) {
                        $change = $changes[$state];
                        $$state = [];
                        foreach ($fields as $f) {
                            $$state[$f] = !empty($change[$f]) ? parent::escape($change[$f]) : '';
                        }
                    }
                    else { unset($$state); }
                }
                echo "
                <table>
                    <thead>
                        <tr><th></th>
                ";
                        foreach (EventHistory::$states as $state) {
                            if (isset($$state)) { echo "<th>{$this->_($state)}</th>"; }
                        }
                echo "
                        </tr>
                    </thead>
                    <tbody>
                ";
                    foreach ($fields as $f) {
                        echo "<tr><th>{$this->_($f)}</th>";
                        foreach (EventHistory::$states as $state) {
                            if (isset($$state)) { echo "<td>{$$state[$f]}</td>"; }
                        }
                        echo '</tr>';
                    }
            echo "
                    </tbody>
                </table>

                </div>
            </article>
            ";
        }
    ?>
</section>
