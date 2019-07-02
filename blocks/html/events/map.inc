<?php
/**
 * @copyright 2015-2019 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
use Application\Models\EventTypesTable;

$this->template->addToAsset('scripts', OPENLAYERS.'/ol.js');
$this->template->addToAsset('scripts', BASE_URI.'/js/mapDisplay-'.VERSION.'.js');

global $MAPLAYERS;
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
    <?php
        $types = [];
        $table = new EventTypesTable();
        $list  = $table->find();
        foreach ($list as $t) {
            $types[] = [
                'code'        => $t->getCode(),
                'name'        => $t->getName(),
                'description' => $t->getDescription(),
                'default'     => $t->isDefault(),
                'color'       => sscanf($t->getColor(), '%02x%02x%02x')
            ];
        }
    ?>
    PHP.eventTypes = <?= json_encode($types, JSON_PRETTY_PRINT); ?>;
</script>
