<?php
/**
 * @copyright 2009-2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Laminas\Db\ResultSet $people
 */
?>
<table>
    <thead>
        <tr><th><?= $this->_('username'); ?></th>
            <th><?= $this->_('name'); ?></th>
            <th><?= $this->_(['email','emails',1]); ?></th>
            <th><?= $this->_('phone'); ?></th>
            <th><?= $this->_('department'); ?></th>
        </tr>
    </thead>
    <tbody>
    <?php
        foreach ($this->people as $person) {
            $url   = BASE_URI.'/people/view?person_id='.$person->id;
            $user  = parent::escape($person->username       );
            $name  = parent::escape($person->fullname()     );
            $email = parent::escape($person->email          );
            $dept  = parent::escape($person->department_name);
            $phone = parent::escape($person->phone          );

            echo "
            <tr><td><a href=\"$url\">$user</a></td>
                <td><a href=\"$url\">$name</a></td>
                <td><a href=\"$url\">$email</a></td>
                <td><a href=\"$url\">$phone</a></td>
                <td><a href=\"$url\">$dept</a></td>
            </tr>
            ";
        }
    ?>
    </tbody>
</table>
