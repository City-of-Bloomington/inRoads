<?php
/**
 * @copyright 2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
$this->template->addToAsset('scripts', OPENLAYERS.'/build/ol.js');
$this->template->addToAsset('scripts', BASE_URI.'/js/mapDisplay.js');

$mapdata = [];
foreach ($this->events as $e) {
    $g = $e->getGeography();
    if ($g) { $mapdata[] = $g; }
}
?>
<div id="map" class="mapPanel"></div>
<script type="text/javascript">
    PHP.mapdata = <?= json_encode($mapdata); ?>;
</script>
