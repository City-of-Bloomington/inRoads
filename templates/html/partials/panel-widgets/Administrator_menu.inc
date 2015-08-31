<?php
/**
 * @copyright 2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
use Application\Models\Person;

$links = '';

$routes = [
    'departments'   => 'department',
    'eventTypes'    => 'eventType',
    'people'        => 'person',
    'users'         => 'user'
];
foreach ($routes as $plural=>$singular) {
    $requiredAction = 'index';
    if (Person::isAllowed($plural, $requiredAction)) {
        $links.= "<a href=\"".BASE_URI."/$plural\">{$this->_([$singular, $plural, 2])}</a>";
    }
}

if ($links) {
    echo "
    <nav>
        <div class=\"menuLauncher\">{$this->_('admin')}</div>
        <div class=\"menuLinks closed\">$links</div>
    </nav>
    ";
}
