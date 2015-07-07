<?php
/**
 * @copyright 2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
$this->template->addToAsset('scripts', OPENLAYERS.'/ol.js');
$this->template->addToAsset('scripts', BASE_URI.'/js/mapDisplay.js');

global $MAPLAYERS, $EVENT_TYPES;
?>
<div id="map" class="mapPanel">
    <div id="pleaseEnableJavascript" style="padding: 2em; max-width: 30em;">
        <h2>Please enable Javascript</h2>
        <p>To enjoy the best experience, please make sure Javascript is enabled, or switch to a browser that supports Javascript.</p>
        <p>This service uses Javascript to display an interactive map of scheduled road closures, which helps visualize where roads are scheduled to close.</p>
    </div>
</div>
<div id="marker"></div>
<script type="text/javascript">
    PHP.maplayers  = <?= json_encode($MAPLAYERS,   JSON_PRETTY_PRINT); ?>;
    PHP.eventTypes = <?= json_encode($EVENT_TYPES, JSON_PRETTY_PRINT); ?>;
</script>
