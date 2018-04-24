<nav>
<?php
/**
 * @copyright 2015-2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
use Blossom\Classes\View;

global $AUTH0;

if (isset($_SESSION['USER'])) {
    $name = View::escape($_SESSION['USER']->getFullname());

    echo "
    <div class=\"menuLauncher\">$name</div>
    <div class=\"menuLinks closed\">
        <a href=\"".BASE_URI."/login/logout\">{$this->_('logout')}</a>
    </div>
    ";
}
else {
    $return_url = View::escape($_SERVER['REQUEST_URI']);
    if (defined('CAS') || isset($AUTH0)) {
        if (defined('CAS')) {
            echo "<a href=\"".BASE_URI."/login/cas?return_url=$return_url\">CAS Login</a>";
        }
        if (isset($AUTH0)) {
            echo "<a href=\"".BASE_URI."/login/auth0?return_url=$return_url\">Auth0 Login</a>";
        }

    }
    else {
        echo "<a href=\"".BASE_URI."/login?return_url=$return_url\">{$this->_('login')}</a>";
    }
}
?>
</nav>
