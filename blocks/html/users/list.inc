<?php
/**
 * @copyright 2007-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Zend\Db\Sql\Select $users
 */
use Application\Models\Person;
use Blossom\Classes\View;
?>
<section>
	<h1><?php echo $this->_(['user','users',2]); ?></h1>
	<div>
	<?php
        $helper = $this->template->getHelper('buttonLink');
        if (Person::isAllowed('users')) {
            echo $helper->buttonLink(BASE_URI.'/users/update', $this->_('user_add'), 'add');
        }
	?>
	</div>
	<table>
		<thead>
			<tr><th></th>
				<th><?php echo $this->_('username'); ?></th>
				<th><?php echo $this->_('name'); ?></th>
				<th><?php echo $this->_('authenticationMethod'); ?></th>
				<th><?php echo $this->_('role'); ?></th>
				<th></th>
			</tr>
		</thead>
		<tbody>
		<?php
			foreach ($this->users as $user) {
				$editButton = '';
				$deleteButton = '';
				if (Person::isAllowed('users')) {
					$editButton = $helper->buttonLink(
						BASE_URI.'/users/update?user_id='.$user->getId(),
						$this->_('user_edit'),
						'edit'
					);
					$deleteButton = $helper->buttonLink(
						BASE_URI.'/users/delete?user_id='.$user->getId(),
						$this->_('user_delete'),
						'delete'
					);
				}

				$name = View::escape($user->getFullname());
				echo "
				<tr><td>$editButton</td>
					<td>{$user->getUsername()}</td>
					<td>$name</td>
					<td>{$user->getAuthenticationMethod()}</td>
					<td>{$user->getRole()}</td>
					<td>$deleteButton</td>
				</tr>
				";
			}
		?>
		</tbody>
	</table>
</section>
