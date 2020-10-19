<?php
/**
 * @copyright 2007-2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param array $this->users
 */
use Application\Auth;
use Application\Url;

$helper = $this->template->getHelper('buttonLink');
$url    = new Url(Url::current_url(BASE_HOST));
$url->format = 'csv';
echo $helper->buttonLink($url, $this->_('csv'), 'download');
?>
<table>
    <thead>
        <tr><th></th>
            <th><?= $this->_('username'); ?></th>
            <th><?= $this->_('name'); ?></th>
            <th><?= $this->_('authenticationMethod'); ?></th>
            <th><?= $this->_('role'); ?></th>
            <th><?= $this->_('department'); ?></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
    <?php
        foreach ($this->users as $user) {
            $editButton = '';
            $deleteButton = '';
            if (Auth::isAllowed('users', 'update')) {
                $editButton = $helper->buttonLink(
                    BASE_URI.'/users/update?id='.$user->id,
                    $this->_('user_edit'),
                    'edit'
                );
            }
            if (Auth::isAllowed('users', 'delete')) {
                $deleteButton = $helper->buttonLink(
                    BASE_URI.'/users/delete?id='.$user->id,
                    $this->_('user_delete'),
                    'delete'
                );
            }

            $username   = parent::escape($user->username);
            $fullname   = parent::escape($user->fullname());
            $department = parent::escape($user->department_name);
            echo "
            <tr><td>$editButton</td>
                <td>$username</td>
                <td>$fullname</td>
                <td>{$user->authenticationMethod}</td>
                <td>{$user->role}</td>
                <td>$department</td>
                <td>$deleteButton</td>
            </tr>
            ";
        }
    ?>
    </tbody>
</table>
