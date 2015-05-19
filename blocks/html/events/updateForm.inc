<?php
/**
 * @copyright 2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Event $this->event
 */
use Application\Models\Event;
use Application\Models\JurisdictionsTable;
use Blossom\Classes\View;

$headline    = View::escape($this->event->getHeadline());
$description = View::escape($this->event->getDescription());
$detour      = View::escape($this->event->getDetour());
$startDate = $this->event->getStartDate(DATE_FORMAT);
$endDate   = $this->event->getEndDate(DATE_FORMAT);

$title = $this->event->getId() ? $this->_('event_edit') : $this->_('event_add');
?>
<section>
    <h1><?php echo $title; ?></h1>
    <form method="post" id="eventUpdateForm" action="<?php echo BASE_URI; ?>/events/update">
        <fieldset>
            <input name="event_id" type="hidden" value="<?php echo $this->event->getId(); ?>" />

            <legend><?php echo $this->_('event_info'); ?></legend>
            <table>
                <tr><td><label for="jurisdiction_id"><?php echo $this->_('jurisdiction'); ?></label></td>
                    <td><select name="jurisdiction_id">
                        <?php
                            $table = new JurisdictionsTable();
                            $list = $table->find();
                            foreach ($list as $j) {
                                $name = View::escape($j->getName());
                                $selected = $this->event->getId()==$j->getId()
                                    ? 'selected="selected"'
                                    : '';
                                echo "<option value=\"{$j->getId()}\" $selected>$name</option>";
                            }
                        ?>
                        </select>
                    </td>
                </tr>
                <tr><td><label for="headline"><?php echo $this->_('headline'); ?></label></td>
                    <td><input name="headline" id="headline" value="<?php echo $headline; ?>" /></td>
                </tr>
                <tr><td><label for="eventType"><?php echo $this->_('eventType'); ?></label></td>
                    <td><select name="eventType" id="eventType">
                        <?php
                            foreach (Event::$TYPES as $t=>$d) {
                                $selected = $this->event->getEventType()==$t
                                    ? 'selected="selected"'
                                    : '';
                                echo "<option $selected>$t</option>";
                            }
                        ?>
                        </select>
                    </td>
                </tr>
                <tr><td><label for="startDate"><?= $this->_('startDate'); ?></label></td>
                    <td><input name="startDate" id="startDate" value="<?= $startDate; ?>" /></td>
                </tr>
                <tr><td><label for="endDate"><?= $this->_('endDate'); ?></label></td>
                    <td><input name="endDate" id="endDate" value="<?= $endDate; ?>" /></td>
                </tr>
                <tr><td><label for="severity"><?php echo $this->_('severity'); ?></label></td>
                    <td><select name="severity" id="severity">
                        <?php
                            foreach (Event::$SEVERITIES as $s=>$d) {
                                $selected = $this->event->getSeverity()==$s
                                    ? 'selected="selected"'
                                    : '';
                                echo "<option $selected>$s</option>";
                            }
                        ?>
                        </select>
                    </td>
                </tr>
                <tr><td><label for="status"><?php echo $this->_('status'); ?></label></td>
                    <td><select name="status" id="status">
                        <?php
                            foreach (Event::$STATUS as $s) {
                                $selected = $this->event->getStatus()==$s
                                    ? 'selected="selected"'
                                    : '';
                                echo "<option $selected>$s</option>";
                            }
                        ?>
                        </select>
                    </td>
                </tr>
            </table>
            <div>
                <div><label for="description"><?php echo $this->_('description'); ?></label></div>
                <textarea name="description" id="description"><?php echo $description; ?></textarea>
            </div>
            <div>
                <div><label for="detour"><?php echo $this->_('detour'); ?></label></div>
                <textarea name="detour" id="detour"><?php echo $detour; ?></textarea>
            </div>
            <div>
                <div><label for="geography"><?php echo $this->_('geography'); ?></label></div>
                <textarea name="geography" id="geography"><?php echo $this->event->getGeography(); ?></textarea>
                <div id="map">
                    <div class="ol-control" id="clearFeaturesControl">
                        <button type="button" id="clearFeaturesButton">clear</button>
                    </div>
                </div>
                <?php
                    $this->template->addToAsset('scripts', OPENLAYERS.'/build/ol-debug.js');
                    $this->template->addToAsset('scripts', BASE_URI.'/js/mapDisplay.js');
                    $this->template->addToAsset('scripts', BASE_URI.'/js/mapEditor.js');
                ?>
            </div>
            <?php
                $helper = $this->template->getHelper('saveAndCancelButtons');
                echo $helper->saveAndCancelButtons(BASE_URI.'/events');
            ?>
        </fieldset>
    </form>
</section>