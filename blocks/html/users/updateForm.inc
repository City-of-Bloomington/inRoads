<?php
/**
 * @copyright 2006-2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param int    $this->id
 * @param int    $this->department_id
 * @param string $this->username
 * @param string $this->password
 * @param string $this->role
 * @param string $this->authenticationMethod
 * @param string $this->firstname
 * @param string $this->lastname
 * @param string $this->email
 * @param string $this->title
 */
?>
<section>
    <header>
        <h1><?= $this->title; ?></h1>
    </header>

	<form method="post">
		<fieldset><legend><?= $this->_('user_info'); ?></legend>
			<input name="id" type="hidden" value="<?= $this->id; ?>" />
			<?php
                $field = $this->template->getHelper('field');

                global $AUTHENTICATION_METHODS;
                $options = [];
                foreach (array_keys($AUTHENTICATION_METHODS) as $m) { $options[] = ['value'=>$m]; }
                echo $field->field([
                    'name'     => 'authenticationMethod',
                    'id'       => 'authenticationMethod',
                    'label'    => $this->_('authenticationMethod'),
                    'value'    => $this->authenticationMethod,
                    'type'     => 'select',
                    'options'  => $options,
                    'required' => true
                ]);

                echo $field->field(['name'=>'username', 'id'=>'username', 'label'=>$this->_('username'), 'value'=>$this->username, 'required'=>true]);
                echo $field->field(['name'=>'password', 'id'=>'password', 'label'=>$this->_('password'), 'type'=>'password']);

				$options = [];
                global $ACL;
                foreach (array_reverse($ACL->getRoles()) as $r) { $options[] = ['value'=>$r]; }
				echo $field->field([
                    'name'     => 'role',
                    'id'       => 'role',
                    'label'    => $this->_('role'),
                    'value'    => $this->role,
                    'type'     => 'select',
                    'options'  => $options,
                    'required' => true
				]);

				$options = [];
				foreach ($this->departments as $d) { $options[] = ['value'=>$d['id'], 'label'=>$d['name']]; }
				echo $field->field([
                    'name'    => 'department_id',
                    'id'      => 'department_id',
                    'label'   => $this->_('department'),
                    'value'   => $this->department_id,
                    'type'    => 'select',
                    'options' => $options
				]);
			?>
		</fieldset>
		<fieldset><legend><?= $this->_('person_info'); ?></legend>
            <?php
                echo $field->field(['name'=>'firstname', 'id'=>'firstname', 'label'=>$this->_('firstname'), 'value'=>$this->firstname]);
                echo $field->field(['name'=>'lastname',  'id'=>'lastname',  'label'=>$this->_('lastname' ), 'value'=>$this->lastname ]);
				echo $field->field(['name'=>'email',     'id'=>'email',     'label'=>$this->_('email'    ), 'value'=>$this->email, 'type'=>'email']);

				$helper = $this->template->getHelper('saveAndCancelButtons');
				echo $helper->saveAndCancelButtons(BASE_URI.'/users');
            ?>
		</fieldset>
	</form>
</section>
