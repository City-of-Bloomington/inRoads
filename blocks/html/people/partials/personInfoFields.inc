<?php
/**
 * @copyright 2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Person $this->person
 */
declare (strict_types=1);

$fields = ['firstname', 'lastname', 'email', 'phone', 'notify_updates', 'notify_emergency'];
foreach ($fields as $f) {
	$get = 'get'.ucfirst($f);
	$$f = parent::escape($this->person->$get());
}

$h = $this->template->getHelper('field');
echo $h->field([
    'name'     => 'firstname',
    'id'       => 'firstname',
    'label'    => $this->_('firstname'),
    'value'    => $firstname,
    'required' => true,
    'attr'     => ['inputmode'=>'latin-name']
]);

echo $h->field([
    'name'     => 'lastname',
    'id'       => 'lastname',
    'label'    => $this->_('lastname'),
    'value'    => $lastname,
    'required' => true,
    'attr'     => ['inputmode'=>'latin-name']
]);

echo $h->field([
    'name'     => 'email',
    'id'       => 'email',
    'label'    => $this->_('email'),
    'value'    => $email,
    'type'     => 'email',
    'attr'     => ['inputmode'=>'email']
]);

echo $h->field([
    'name'     => 'phone',
    'id'       => 'phone',
    'label'    => $this->_('phone'),
    'value'    => $phone,
    'type'     => 'tel',
    'attr'     => ['inputmode'=>'tel']
]);

$options = [
    ['value'=>1, 'label'=>$this->_('yes')],
    ['value'=>0, 'label'=>$this->_('no' )]
];
echo $h->field([
    'name'     => 'notify_updates',
    'id'       => 'notify_updates',
    'label'    => $this->_('notify_updates'),
    'value'    => $notify_updates,
    'type'     => 'select',
    'options'  => $options
]);
echo $h->field([
    'name'     => 'notify_emergency',
    'id'       => 'notify_emergency',
    'label'    => $this->_('notify_emergency'),
    'value'    => $notify_emergency,
    'type'     => 'select',
    'options'  => $options
]);
