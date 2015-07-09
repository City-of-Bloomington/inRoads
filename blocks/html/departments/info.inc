<?php
/**
 * @copyright 2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
use Application\Models\Person;
use Blossom\Classes\View;

foreach (['name', 'code', 'phone'] as $f) {
    $get = 'get'.ucfirst($f);
    $$f = View::escape($this->department->$get())   ;
}

$editButton = '';
if (Person::isAllowed('departments', 'edit')) {
    $h = $this->template->getHelper('buttonLink');
    $editButton = $h->buttonLink(
        BASE_URI."/departments/update?department_id={$this->department->getId()}",
        $this->translate('edit'),
        'edit'
    );
}
?>
<section>
    <h1><?= $name; ?></h1>
    <div><?= $editButton; ?></div>
    <table>
        <tr><th><?= $this->_('code'); ?></th>
            <td><?= $code; ?></td>
        </tr>
        <tr><th><?= $this->_('phone'); ?></th>
            <td><?= $phone; ?></th>
        </tr>
    </table>
</section>
