<?php
/**
 * @copyright 2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
$this->template->addToAsset('scripts', OPENLAYERS.'/ol.js');
$this->template->addToAsset('scripts', BASE_URI.'/js/mapDisplay.js');
$this->template->addToAsset('scripts', BASE_URI.'/js/mapEditor.js');
?>
<div class="tools">
    <button type="button" id="clearFeaturesButton">clear</button>
    <select name="geometryType" id="geometryType">
        <option value="LineString">Line</option>
        <option value="Point">Point</option>
    </select>
</div>
<div id="map" class="mapPanel"></div>
