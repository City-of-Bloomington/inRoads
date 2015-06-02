<?php
/**
 * @copyright 2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
$this->template->addToAsset('scripts', OPENLAYERS.'/build/ol-debug.js');
$this->template->addToAsset('scripts', BASE_URI.'/js/mapDisplay.js');
$this->template->addToAsset('scripts', BASE_URI.'/js/mapEditor.js');
?>
<div id="map" class="mapPanel">
    <div class="ol-control"   id="clearFeaturesControl">
        <button type="button" id="clearFeaturesButton">clear</button>
    </div>
</div>
