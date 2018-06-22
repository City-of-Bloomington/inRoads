<?php
/**
 * @copyright 2015-2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Event $this->event
 */
use Application\Models\Person;
?>
<?php
    $return_uri = !empty($_GET['return_uri']) ? $_GET['return_uri'] : BASE_URI.'/events';
    $helper = $this->template->getHelper('buttonLink');
    echo $helper->buttonLink(
        $return_uri,
        $this->template->_('back'),
        'back'
    );
?>
<h1 class="headerBar-title"><?= $this->template->title; ?></h1>
