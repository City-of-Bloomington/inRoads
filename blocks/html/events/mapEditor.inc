<?php
/**
 * @copyright 2015-2019 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
$this->template->addToAsset('scripts', OPENLAYERS.'/ol.js');
$this->template->addToAsset('scripts', BASE_URI.'/js/mapDisplay-'.VERSION.'.js');
$this->template->addToAsset('scripts', BASE_URI.'/js/mapEditor-'.VERSION.'.js');

global $MAPLAYERS, $EVENT_TYPES;
?>

<div id="map" class="mapPanel"></div>

<div id="mapTools" class="mapTools">
    <button type="button" id="LineString">Line</button>
    <button type="button" id="Point">Point</button>
    <button type="button" id="clearFeaturesButton">Clear</button>
</div>
<script type="text/javascript">
    PHP.maplayers  = <?= json_encode($MAPLAYERS,   JSON_PRETTY_PRINT); ?>;
    PHP.eventTypes = <?= json_encode($EVENT_TYPES, JSON_PRETTY_PRINT); ?>;
</script>
