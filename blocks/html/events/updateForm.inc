<?php
/**
 * @copyright 2014-2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Event $this->event
 */
use Application\Models\Event;
use Application\Models\JurisdictionsTable;
use Blossom\Classes\View;

$geography_description = View::escape($this->event->getGeography_description());
$description = View::escape($this->event->getDescription());
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
            <dl><dt><label for="eventType"><?php echo $this->_('eventType'); ?></label></dt>
                <dd><select name="eventType" id="eventType">
                    <?php
                        foreach (Event::$TYPES as $t=>$d) {
                            $selected = $this->event->getEventType()==$t
                                ? 'selected="selected"'
                                : '';
                            echo "<option $selected>$t</option>";
                        }
                    ?>
                    </select>
                </dd>
            </dl>
            <?php
                $help = View::translateDateString(DATE_FORMAT);
                $size = strlen($help);
            ?>
            <dl><dt><label  for="startDate"><?= $this->_('startDate'); ?></label></dt>
                <dd><input name="startDate" id="startDate" value="<?= $startDate; ?>"
                        placeholder="<?= $help; ?>" size="<?= $size; ?>" />
                </dd>
            </dl>
            <dl><dt><label  for="endDate"><?= $this->_('endDate'); ?></label></dt>
                <dd><input name="endDate" id="endDate" value="<?= $endDate; ?>"
                        placeholder="<?= $help; ?>" size="<?= $size; ?>" />
                </dd>
            </dl>
            <dl><dt><label  for="geography_description"><?= $this->_('geography_description'); ?></label></dt>
                <dd><input name="geography_description" id="geography_description" value="<?= $geography_description; ?>" />
                    <input name="geography" id="geography" type="hidden" value="<?= $this->event->getGeography(); ?>" />
                </dd>
            </dl>
            <div>
                <div><label for="description"><?php echo $this->_('description'); ?></label></div>
                <textarea  name="description" id="description"><?php echo $description; ?></textarea>
            </div>
            <div id="map">
                <div class="ol-control"   id="clearFeaturesControl">
                    <button type="button" id="clearFeaturesButton">clear</button>
                </div>
            </div>
            <?php
                $this->template->addToAsset('scripts', OPENLAYERS.'/build/ol-debug.js');
                $this->template->addToAsset('scripts', BASE_URI.'/js/mapDisplay.js');
                $this->template->addToAsset('scripts', BASE_URI.'/js/mapEditor.js');

                $helper = $this->template->getHelper('saveAndCancelButtons');
                echo $helper->saveAndCancelButtons(BASE_URI.'/events');
            ?>
        </fieldset>
    </form>
</section>
