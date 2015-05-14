<?php
/**
 * @copyright 2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Zend\Db\Result $this->events
 */
$this->template->addToAsset('scripts', OPENLAYERS.'/build/ol.js');
$this->template->addToAsset('scripts', BASE_URI.'/js/mapDisplay.js');

$mapdata = [];
foreach ($this->events as $e) {
    $mapdata[] = "'{$e->getGeography()}'";
}
?>
<div id="map"></div>
<script type="text/javascript">
    PHP.mapdata = [<?= implode(",\n", $mapdata); ?>];
</script>
