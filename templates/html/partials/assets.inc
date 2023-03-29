<?php
/**
 * @copyright 2015-2023 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
if (   isset($this->assets['scripts'])) {
    foreach ($this->assets['scripts'] as $url) {
        echo "<script type=\"text/javascript\" src=\"$url\"></script>\n";
    }
}
if (   isset($this->assets['stylesheets'])) {
    foreach ($this->assets['stylesheets'] as $url) {
        echo "<link rel=\"stylesheet\" href=\"$url\" />\n";
    }
}
?>
<script async src="https://www.googletagmanager.com/gtag/js?id=G-DR1WWQWXVP"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', 'G-DR1WWQWXVP');
</script>
