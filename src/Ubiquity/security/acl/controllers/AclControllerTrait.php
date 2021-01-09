<?php

namespace Ubiquity\security\acl\controllers;

use Ubiquity\security\acl\AclManager;
use Ubiquity\exceptions\AclException;
use Ubiquity\log\Logger;

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
	
	protected function isRoleAllowedForControllerAction(string $controller,string $action,string $role):bool{
		$resourceController = AclManager::getPermissionMap ()->getRessourcePermission ( $controller, $action );
		if (isset ( $resourceController )) {
			try{
				if (AclManager::isAllowed ( $role, $resourceController ['resource'], $resourceController ['permission'] )) {
					return true;
				}
			}
			catch(AclException $e){
				Logger::alert('Router', $role.' is not allowed for this resource','Acls',[$controller,$action]);
			}
		}
		return false;
	}
	/**
	 * Returns True if access to the controller is allowed for $role.
	 *
	 * @param string $action
	 * @param string $role
	 * @return boolean
	 */
	protected function isValidRole($action,$role) {
		$controller=\get_class($this);
		if($this->isRoleAllowedForControllerAction($controller, $action, $role)){
			return true;
		}
		if ($action !== '*') {
			$r=new \ReflectionMethod($this,$action);
			$class=$r->getDeclaringClass()->getName();
			if($class!==$controller && $this->isRoleAllowedForControllerAction($class, $action, $role)){
				return true;
			}
			return $this->isRoleAllowedForControllerAction($controller, '*', $role );
		}
		return false;
	}
	/**
	 * Returns True if access to the controller is allowed for the role returned by _getRole method.
	 * To be override in sub classes
	 *
	 * @param string $action
	 * @return boolean
	 */
	public function isValid($action) {
		return $this->isValidRole($action, $this->_getRole());
	}
}

