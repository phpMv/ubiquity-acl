<?php

namespace Ubiquity\security\acl\controllers;

use Ubiquity\security\acl\AclManager;

/**
 * To use with a controller with acls.
 * Ubiquity\security\acl\controllers$AclControllerTrait
 * This class is part of Ubiquity
 *
 * @author jc
 * @version 1.0.0
 *         
 */
trait AclControllerTrait {
	public abstract function _getRole();

	/**
	 * Returns True if access to the controller is allowed for the role returned by _getRole method.
	 * To be override in sub classes
	 *
	 * @param string $action
	 * @return boolean
	 */
	public function isValid($action) {
		$controller = \get_class ( $this );
		$resourceController = AclManager::getPermissionMap ()->getRessourcePermission ( $controller, $action );
		if (isset ( $resourceController )) {
			if (AclManager::isAllowed ( $this->_getRole (), $resourceController ['resource'], $resourceController ['permission'] )) {
				return true;
			}
		}
		if ($action !== '*') {
			return $this->isValid ( '*' );
		}
		return false;
	}
}

