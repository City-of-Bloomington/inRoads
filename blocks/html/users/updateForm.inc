<?php
/**
 * @copyright 2006-2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param User $this->user
 */
use Application\Models\DepartmentsTable;
use Application\Models\Person;
use Blossom\Classes\View;

$title = $this->user->getId() ? $this->_('user_edit') : $this->_('user_add');
?>
<section>
    <h1><?= $title; ?></h1>
	<form method="post" action="<?= BASE_URI; ?>/users/update">
		<fieldset><legend><?= $this->_('user_info'); ?></legend>
			<input name="user_id" type="hidden" value="<?= $this->user->getId(); ?>" />
			<table>
				<tr><td><label for="authenticationMethod"><?= $this->_('authenticationMethod'); ?></label></td>
					<td><select name="authenticationMethod" id="authenticationMethod" required="required">
						<?php
							foreach (Person::getAuthenticationMethods() as $method) {
								$selected = $this->user->getAuthenticationMethod()==$method
									? 'selected="selected"'
									: '';
								echo "<option $selected>$method</option>";
							}
						?>
						</select>
					</td>
				</tr>
				<tr><td><label for="username"><?= $this->_('username'); ?></label></td>
					<td><input name="username" id="username" value="<?= View::escape($this->user->getUsername()); ?>"
                            required="required" />
					</td>
				</tr>
				<tr><td><label for="password"><?= $this->_('password'); ?></label></td>
					<td><input name="password" id="password" type="password" /></td>
				</tr>
				<tr><td><label for="role"><?= $this->_('role'); ?></label></td>
					<td><select name="role" id="role"><option></option>
						<?php
							global $ZEND_ACL;
							foreach (array_reverse($ZEND_ACL->getRoles()) as $role) {
								$selected = $this->user->getRole()==$role
									? 'selected="selected"'
									: '';
								echo "<option $selected>$role</option>";
							}
						?>
						</select>
					</td>
				</tr>
				<tr><td><label for="department_id"><?= $this->_('department'); ?></td>
                    <td><select name="department_id" id="department_id"><option value=""></option>
                        <?php
                            $table = new DepartmentsTable();
                            $list = $table->find();
                            foreach ($list as $d) {
                                $selected = $d->getId()==$this->user->getDepartment_id()
                                    ? 'selected="selected"'
                                    : '';
                                $name = View::escape($d->getName());
                                echo "<option value=\"{$d->getId()}\" $selected>$name</option>";
                            }
                        ?>
                        </select>
                    </td>
			</table>
			<?php
				$helper = $this->template->getHelper('saveAndCancelButtons');
				echo $helper->saveAndCancelButtons(BASE_URI.'/users');
			?>
		</fieldset>
	</form>
</section>
