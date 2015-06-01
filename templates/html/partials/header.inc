<?php
use Blossom\Classes\View;
?>
<header>
    <div class="container">
        <img src="<?= BASE_URI ?>/css/images/bloomington-logo.svg" alt="Bloomington.in.gov - Mark Kruzan, Mayor" class="site-logo" />
        <div class="site-utilityBar">
        <?php
            $this->addToAsset('scripts', BASE_URI.'/js/menus.js');

            $this->_include('/partials/panel-widgets/User_menu.inc');
            $this->_include('/partials/panel-widgets/Administrator_menu.inc');
        ?>
        </div>
    </div>
</header>
