<?php
/**
 * @copyright 2015-2019 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param string $this->title
 * @param int    $this->event_id
 * @param int    $this->segment_id
 * @param int    $this->street_id
 * @param string $this->street
 * @param string $this->streetFrom
 * @param string $this->streetTo
 * @param string $this->direction
 * @param string $this->startLatitude
 * @param string $htis->startLongitude
 * @param string $this->endLatitude
 * @param string $this->endLongitude
 * @param array  $this->directions     Array of values to populate directions dropdown
 * @param array  $this->crossStreets   Array of streets that intersect the chosen street
 */
$this->template->addToAsset('scripts', BASE_URI.'/js/segments/chooseStreet.js');
?>
<script type="text/javascript">
    var ADDRESS_SERVICE = "<?= ADDRESS_SERVICE; ?>";
</script>
<section class="editPanel">
    <header>
        <h1><?= $this->title; ?></h1>
    </header>

    <form method="get" id="streetSearchForm">
        <fieldset>
            <dl><dt><label  for="streetQuery"><?= $this->_('street'); ?></label></dt>
                <dd><input name="streetQuery" id="streetQuery" value="<?= $this->street; ?>" /></dd>
            </dl>
            <button type="submit" class="search"><?= $this->_('search'); ?></button>
            <div id="streetResults"></div>
        </fieldset>
    </form>

    <form method="post" id="segmentForm" action="<?= BASE_URI; ?>/segments/update">
        <fieldset>
            <input name="event_id"   type="hidden" value="<?= $this->event_id;   ?>" />
            <input name="segment_id" type="hidden" value="<?= $this->segment_id; ?>" />

            <input name="street"         type="hidden" id="street"         value="<?= $this->street; ?>" />
            <input name="street_id"      type="hidden" id="street_id"      value="<?= $this->street_id; ?>" />
            <input name="startLatitude"  type="hidden" id="startLatitude"  value="<?= $this->startLatitude; ?>" />
            <input name="startLongitude" type="hidden" id="startLongitude" value="<?= $this->startLongitude; ?>" />
            <input name="endLatitude"    type="hidden" id="endLatitude"    value="<?= $this->endLatitude; ?>" />
            <input name="endLongitude"   type="hidden" id="endLongitude"   value="<?= $this->endLongitude; ?>" />

            <h3><?= $this->street; ?></h3>
            <dl><dt><label   for="streetFrom">From</label></dt>
                <dd><select name="streetFrom" id="streetFrom"><option value=""></option>
                    <?php
                        foreach ($this->crossStreets as $s) {
                            $selected = $this->streetFrom == $s->streetName ? 'selected="true"' : '';
                            echo "<option $selected>{$s->streetName}</option>";
                        }
                    ?>
                    </select>
                </dd>
            </dl>

            <dl><dt><label   for="streetTo">To</label></dt>
                <dd><select name="streetTo" id="streetTo"><option value=""></option>
                    <?php
                        foreach ($this->crossStreets as $s) {
                            $selected = $this->streetTo == $s->streetName ? 'selected="true"' : '';
                            echo "<option data-street_id=\"{$s->id}\" $selected>{$s->streetName}</option>";
                        }
                    ?>
                    </select>
                </dd>
            </dl>

            <dl><dt><label   for="direction"><?= $this->_('direction'); ?></label></dt>
                <dd><select name="direction" id="direction"><option value=""></option>
                    <?php
                        foreach ($this->directions as $dir) {
                            $selected = $this->direction == $dir ? 'selected="true"' : '';
                            echo "<option $selected>$dir</option>";
                        }
                    ?>
                    </select>
                </dd>
            </dl>
            <?php
                $h = $this->template->getHelper('saveAndCancelButtons');
                echo $h->saveAndCancelButtons(BASE_URI.'/segments?event_id='.$this->event_id);
            ?>
        </fieldset>
    </form>
</section>
