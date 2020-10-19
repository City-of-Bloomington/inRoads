<?php
/**
 * @copyright 2014-2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
use Laminas\Permissions\Acl\Acl;
use Laminas\Permissions\Acl\Role\GenericRole as Role;
use Laminas\Permissions\Acl\Resource\GenericResource as Resource;

$ACL = new Acl();
$ACL->addRole(new Role('Anonymous'))
    ->addRole(new Role('Public'),        'Anonymous')
    ->addRole(new Role('Staff'),         'Public')
    ->addRole(new Role('Administrator'), 'Staff');

/**
 * Declare all the resources
 */
$ACL->addResource(new Resource('index'));
$ACL->addResource(new Resource('people'));
$ACL->addResource(new Resource('users'));
$ACL->addResource(new Resource('login'));
$ACL->addResource(new Resource('account'));
$ACL->addResource(new Resource('departments'));
$ACL->addResource(new Resource('eventTypes'));

$ACL->addResource(new Resource('events'));
$ACL->addResource(new Resource('segments'));
$ACL->addResource(new Resource('streets'));
$ACL->addResource(new Resource('waze'));
$ACL->addResource(new Resource('trafficcast'));
$ACL->addResource(new Resource('notifications'));

/**
 * Assign permissions to the resources
 */
$ACL->allow(null,'login');

// Permissions for unauthenticated browsing
$ACL->allow(null,
    ['index', 'events', 'waze', 'trafficcast', 'segments'],
    ['index', 'view']
);

$ACL->allow('Public', 'events'  , ['index', 'view', 'update', 'viewPrimaryContact']);
$ACL->allow('Public', 'segments', ['index', 'view', 'update', 'search', 'delete']);
$ACL->allow('Public', 'streets' , ['index',                   'search']);
$ACL->allow('Public', 'account');


// Allow Staff to do stuff
$ACL->allow('Staff', ['people', 'departments', 'eventTypes'], ['index', 'view']);
$ACL->allow('Staff', ['events', 'segments',       'streets'], ['index', 'view', 'update', 'search', 'delete']);

// Administrator is allowed access to everything
$ACL->allow('Administrator');
