<?php
use Blossom\Classes\View;
?>
<div id="panel-one" class="windowPanel">
    <header class="windowPanel-header">
        <div class="windowPanel-title"><?= View::escape($this->title); ?></div>
        <?php
            if ($this->headerToolsButton) {
                echo "<div class=\"windowPanel-headerTools\">{$this->headerToolsButton}</div>";
            }
        ?>
    </header>
    <?= $this->includeBlocks('panel-one'); ?>
</div>
