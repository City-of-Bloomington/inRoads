<?php
/**
 * @copyright 2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
$this->template->addToAsset('scripts', OPENLAYERS.'/ol.js');
$this->template->addToAsset('scripts', BASE_URI.'/js/mapDisplay.js');

global $MAPLAYERS;
?>
<div id="map" class="mapPanel"></div>
<div id="marker"></div>
<script type="text/javascript">
    PHP.maplayers = <?= json_encode($MAPLAYERS, JSON_PRETTY_PRINT); ?>;
</script>