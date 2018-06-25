<?php
/**
 * @copyright 2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
declare (strict_types=1);
namespace Application;

class Auth
{
	/**
	 * Checks if the user is supposed to have acces to the resource
	 *
	 * This is implemented by checking against a Zend_Acl object
	 * The Zend_Acl should be created in bootstrap.inc
	 *
	 * @param string $resource
	 * @param string $action
	 * @return boolean
	 */
	public static function isAllowed($resource, $action=null)
	{
		global $ZEND_ACL;
		$role = 'Anonymous';
		if (  isset($_SESSION['USER']) && $_SESSION['USER']->getRole()) {
			$role = $_SESSION['USER']->getRole();
		}
		return $ZEND_ACL->isAllowed($role, $resource, $action);
	}
}
