<?php
/**
 * @copyright 2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param array $this->departments
 */
use Application\Models\Person;
use Blossom\Classes\View;
?>
<section>
    <h1><?= $this->_(['department', 'departments', 2]); ?></h1>
    <div>
    <?php
        if (Person::isAllowed('departments')) {
            $helper = $this->template->getHelper('buttonLink');
            echo $helper->buttonLink(
                BASE_URI."/departments/update",
                $this->translate('department_add'),
                'add'
            );
        }
    ?>
    </div>

    <table>
        <thead>
            <tr><th><?= $this->_('code'); ?></th>
                <th><?= $this->_('name'); ?></th>
                <th><?= $this->_('phone'); ?></th>
            </tr>
        </thead>
        <tbody>
        <?php
            foreach ($this->departments as $d) {
                $code  = View::escape($d->getCode ());
                $name  = View::escape($d->getName ());
                $phone = View::escape($d->getPhone());

                $url = BASE_URI.'/departments/view?department_id='.$d->getId();

                echo "
                <tr><td><a href=\"$url\">$code</a></td>
                    <td>$name</td>
                    <td>$phone</td>
                </tr>
                ";
            }
        ?>
        </tbody>
    </table>
</section>