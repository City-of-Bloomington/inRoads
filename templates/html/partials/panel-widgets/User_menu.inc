<nav>
<?php
/**
 * @copyright 2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
use Blossom\Classes\View;

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
    echo "<a href=\"".BASE_URI."/login/cas?return_url=$return_url\">{$this->_('login')}</a>";
}
?>
</nav>
