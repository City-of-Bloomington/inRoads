<?php
/**
 * @copyright 2015-2019 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param DateTime $this->start
 * @param DateTime $this->end
 * @param array    $this->filters
 * @param array    $this->presets     An array of \DateTime ranges
 * @param array    $this->departments
 */
use Application\Controllers\EventsController;
use Application\Models\Event;
use Application\Models\EventType;
use Application\View;
use Application\Url;

$this->template->addToAsset('scripts', BASE_URI.'/js/searchForm-'.VERSION.'.js');
?>
<div class="filterPanel expanded" id="filterPanel">
    <?php
        $view = (!empty($_GET['view']) && $_GET['view'] === 'schedule')
              ? 'schedule'
              : 'map';

        $url             = new Url(BASE_URL.'/events');
        $url->eventTypes = !empty($_GET['eventTypes']) ? $_GET['eventTypes'] : null;
        $url->view       = $view;

        $options = '';
        $current = null;
        foreach ($this->presets as $label=>$range) {
            if ($this->start == $range['start'] && $this->end == $range['end']) {
                $selected = 'class="current"';
                $current  = $label;
            }
            else { $selected = ''; }
            $url->start = $range['start']->format(EventsController::DATE_FORMAT);
            $url->end   = $range['end'  ]->format(EventsController::DATE_FORMAT);
            $options.= "<a href=\"$url\" $selected role=\"option\" tabindex=\"-1\">{$this->_($label)}</a>";
        }
        if (!$current) { $current = 'chooseDates'; }
    ?>
    <dl class="nav-dropdown">
        <dt id="dropdown-label1"><?= $this->_('searchPresets'); ?></dt>
        <dd>
            <div class="nav-dropdown-current" role="button" aria-haspopup="true" tabindex="0"><?= $this->_($current); ?></div>
            <div class="nav-dropdown-options" role="listbox" aria-labeledby="dropdown-label1">
                <?= $options; ?>
            </div>
        </dd>
    </dl>

    <form method="get">
        <fieldset id="dateFieldSet">
            <?php
                $h = $this->template->getHelper('field');
                echo $h->field([
                    'name'  => 'start',
                    'id'    => 'start',
                    'label' => $this->_('startDate'),
                    'type'  => 'date',
                    'value' => $this->start ? $this->start->format('U') : null
                ]);
                echo $h->field([
                    'name'  => 'end',
                    'id'    => 'end',
                    'label' => $this->_('endDate'),
                    'type'  => 'date',
                    'value' => $this->end ? $this->end->format('U') : null
                ]);
            ?>
        </fieldset>
        <fieldset>
            <dl class="mod-checkbox filters-legend">
                <dt class="accessible-hide"><?= $this->_('eventTypeLegend'); ?></dt>
                <?php
                    foreach (EventType::types() as $type) {
                        $checked = in_array($type->getCode(), $this->filters['eventTypes'])
                            ? 'checked="checked"'
                            : '';
                        $name = View::escape($type->getName());
                        echo "
                        <dd class=\"{$type->getCode()}\">
                            <label>
                                <input type=\"checkbox\" name=\"eventTypes[]\" value=\"{$type->getCode()}\" $checked />
                                <span>$name</span>
                            </label>
                        </dd>
                        ";
                    }
                ?>
            </dl>
            <?php
                $options = [['value'=>'']];
                foreach ($this->departments as $d) { $options[] = ['value'=>$d->getId(), 'label'=>$d->getName()]; }
                echo $h->field([
                    'name'    => 'department_id',
                    'id'      => 'department_id',
                    'label'   => $this->_('department'),
                    'type'    => 'select',
                    'value'   => !empty($_GET['department_id']) ? (int)$_GET['department_id'] : null,
                    'options' => $options
                ]);
            ?>
        </fieldset>
        <button type="submit" class="search"><?= $this->_('search'); ?></button>
    </form>
</div>
