<?php
/**
 * @copyright 2006-2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param User $this->user
 */
use Application\Models\DepartmentsTable;
use Application\Models\Person;

$fields = [
    'id', 'username', 'role', 'department_id', 'authenticationMethod',
    'firstname', 'lastname', 'email', 'phone'
];
foreach ($fields as $f) {
    $get = 'get'.ucfirst($f);
    $$f  = parent::escape($this->user->$get());
}

$title = $id ? $this->_('user_edit') : $this->_('user_add');
?>
<section>
    <h1><?= $title; ?></h1>
	<form method="post" action="<?= BASE_URI; ?>/users/update">
		<fieldset><legend><?= $this->_('user_info'); ?></legend>
			<input name="user_id" type="hidden" value="<?= $id; ?>" />
			<?php
                $h = $this->template->getHelper('field');

                $options = [];
                foreach (Person::getAuthenticationMethods() as $m) { $options[] = ['value'=>$m]; }
                echo $h->field([
                    'name'     => 'authenticationMethod',
                    'id'       => 'authenticationMethod',
                    'label'    => $this->_('authenticationMethod'),
                    'value'    => $authenticationMethod,
                    'type'     => 'select',
                    'options'  => $options,
                    'required' => true
                ]);
                echo $h->field([
                    'name'     => 'username',
                    'id'       => 'username',
                    'label'    => $this->_('username'),
                    'value'    => $username,
                    'required' => true
                ]);
                echo $h->field([
                    'name'     => 'password',
                    'id'       => 'password',
                    'label'    => $this->_('password'),
                    'type'     => 'password'
                ]);

                global $ZEND_ACL;
                $options = [['value'=>'']];
                foreach (array_reverse($ZEND_ACL->getRoles()) as $r) { $options[] = ['value'=>$r]; }
                echo $h->field([
                    'name'     => 'role',
                    'id'       => 'role',
                    'label'    => $this->_('role'),
                    'value'    => $role,
                    'type'     => 'select',
                    'options'  => $options,
                    'required' => true
                ]);

                $options = [['value'=>'']];
                $table   = new DepartmentsTable();
                $list    = $table->find();
                foreach ($list as $d) { $options[] = ['value'=>$d->getId(), 'label'=>parent::escape($d->getName())]; }
                echo $h->field([
                    'name'     => 'department_id',
                    'id'       => 'department_id',
                    'label'    => $this->_('department'),
                    'value'    => $department_id,
                    'type'     => 'select',
                    'options'  => $options,
                    'required' => true
                ]);

			?>
        </fieldset>
        <fieldset><legend><?= $this->_('person_info'); ?></legend>
            <p><?= $this->_('employee_authentication_help', 'messages'); ?></p>
			<?php
                echo $h->field([
                    'name'     => 'firstname',
                    'id'       => 'firstname',
                    'label'    => $this->_('firstname'),
                    'value'    => $firstname
                ]);
                echo $h->field([
                    'name'     => 'lastname',
                    'id'       => 'lastname',
                    'label'    => $this->_('lastname'),
                    'value'    => $lastname
                ]);
                echo $h->field([
                    'name'     => 'email',
                    'id'       => 'email',
                    'label'    => $this->_('email'),
                    'value'    => $email,
                    'type'     => 'email'
                ]);
                echo $h->field([
                    'name'     => 'phone',
                    'id'       => 'phone',
                    'label'    => $this->_('phone'),
                    'value'    => $phone,
                    'type'     => 'tel'
                ]);

				$h = $this->template->getHelper('saveAndCancelButtons');
				echo $h->saveAndCancelButtons(BASE_URI.'/users');
			?>
		</fieldset>
	</form>
</section>
