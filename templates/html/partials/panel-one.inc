<?php
use Blossom\Classes\View;
?>
<div id="panel-one" class="windowPanel">
    <header class="windowPanel-header">
        <div class="windowPanel-title"><?= View::escape($this->title); ?></div>
        <div class="windowPanel-headerTools">
            <?= !empty($this->headerToolsButton) ? $this->headerToolsButton : ''; ?>
        </div>
    </header>
    <?= $this->includeBlocks('panel-one'); ?>
</div>
