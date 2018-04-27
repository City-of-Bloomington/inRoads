<?php
/**
 * @copyright 2014-2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Role\GenericRole as Role;
use Zend\Permissions\Acl\Resource\GenericResource as Resource;

$ZEND_ACL = new Acl();
$ZEND_ACL->addRole(new Role('Anonymous'))
		 ->addRole(new Role('Public'),        'Anonymous')
		 ->addRole(new Role('Staff'),         'Public')
		 ->addRole(new Role('Administrator'), 'Staff');

/**
 * Declare all the resources
 */
$ZEND_ACL->addResource(new Resource('index'));
$ZEND_ACL->addResource(new Resource('people'));
$ZEND_ACL->addResource(new Resource('users'));
$ZEND_ACL->addResource(new Resource('login'));
$ZEND_ACL->addResource(new Resource('account'));
$ZEND_ACL->addResource(new Resource('departments'));
$ZEND_ACL->addResource(new Resource('eventTypes'));

$ZEND_ACL->addResource(new Resource('events'));
$ZEND_ACL->addResource(new Resource('segments'));
$ZEND_ACL->addResource(new Resource('streets'));
$ZEND_ACL->addResource(new Resource('waze'));
$ZEND_ACL->addResource(new Resource('trafficcast'));

/**
 * Assign permissions to the resources
 */
$ZEND_ACL->allow(null,'login');

// Permissions for unauthenticated browsing
$ZEND_ACL->allow(null,
    ['index', 'events', 'waze', 'trafficcast', 'segments'],
    ['index', 'view']
);

$ZEND_ACL->allow('Public', 'events'  , ['index', 'view', 'update']);
$ZEND_ACL->allow('Public', 'segments', ['index', 'view', 'update', 'search', 'delete']);
$ZEND_ACL->allow('Public', 'streets' , ['index',                   'search']);
$ZEND_ACL->allow('Public', 'account');


// Allow Staff to do stuff
$ZEND_ACL->allow('Staff', ['people', 'departments', 'eventTypes'], ['index', 'view']);
$ZEND_ACL->allow('Staff', ['events', 'segments',       'streets'], ['index', 'view', 'update', 'search', 'delete']);

// Administrator is allowed access to everything
$ZEND_ACL->allow('Administrator');
