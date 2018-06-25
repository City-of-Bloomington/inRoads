<?php
/**
 * @copyright 2015-2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
use Application\Auth;

$links  = [];
$routes = [
    'departments'   => 'department',
    'eventTypes'    => 'eventType',
    'people'        => 'person',
    'users'         => 'user'
];
foreach ($routes as $plural=>$singular) {
    $requiredAction = 'index';
    if (Auth::isAllowed($plural, $requiredAction)) {
        $links[] = ['url'=>BASE_URI."/$plural", 'label'=>$this->_([$singular, $plural, 2])];
    }
}

if ($links) {
    $helper = $this->getHelper('dropdown');
    echo $helper->dropdown($links, $this->_('admin'), 'admin_menu');
}
