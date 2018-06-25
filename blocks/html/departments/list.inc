<?php
/**
 * @copyright 2015-2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param array $this->departments
 */
use Application\Auth;
?>
<section>
    <h1><?= $this->_(['department', 'departments', 2]); ?></h1>
    <div>
    <?php
        if (Auth::isAllowed('departments', 'update')) {
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
                $code  = parent::escape($d->getCode ());
                $name  = parent::escape($d->getName ());
                $phone = parent::escape($d->getPhone());

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
