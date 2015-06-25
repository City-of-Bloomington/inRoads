<?php
use Blossom\Classes\View;
?>
<header>
    <div class="container">
        <a href="https://bloomington.in.gov" class="site-homeLink">
            <img src="<?= BASE_URI ?>/css/images/bloomington-logo.svg" alt="Bloomington.in.gov - Mark Kruzan, Mayor" class="site-logo" />
        </a>
        <div class="site-breadCrumb">
            <div class="site-breadCrumb-ancestors">
                Residents &gt; Getting Around Bloomington
            </div>
            <a href="<?= BASE_URI; ?>/events" class="site-breadCrumb-home">
                <?= APPLICATION_NAME ?>
            </a>
        </div>
        <div class="site-utilityBar">
        <?php
            $this->addToAsset('scripts', BASE_URI.'/js/menus.js');

            $this->_include('/partials/panel-widgets/User_menu.inc');
            $this->_include('/partials/panel-widgets/Administrator_menu.inc');
        ?>
        </div>
    </div>
</header>
