<?php
/**
 * @copyright 2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param string $this->firstname
 * @param string $this->lastname
 * @param string $this->email
 * @param string $this->phone
 */
declare (strict_types=1);

$h = $this->template->getHelper('field');
echo $h->field([
    'name'     => 'firstname',
    'id'       => 'firstname',
    'label'    => $this->_('firstname'),
    'value'    => $this->firstname,
    'required' => true,
    'attr'     => ['inputmode'=>'latin-name']
]);

echo $h->field([
    'name'     => 'lastname',
    'id'       => 'lastname',
    'label'    => $this->_('lastname'),
    'value'    => $this->lastname,
    'required' => true,
    'attr'     => ['inputmode'=>'latin-name']
]);

echo $h->field([
    'name'     => 'email',
    'id'       => 'email',
    'label'    => $this->_('email'),
    'value'    => $this->email,
    'type'     => 'email',
    'attr'     => ['inputmode'=>'email']
]);

echo $h->field([
    'name'     => 'phone',
    'id'       => 'phone',
    'label'    => $this->_('phone'),
    'value'    => $this->phone,
    'type'     => 'tel',
    'attr'     => ['inputmode'=>'tel']
]);

/*
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
*/
