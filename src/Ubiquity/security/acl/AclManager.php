<?php
namespace Ubiquity\security\acl;

use Ubiquity\security\acl\models\AclList;
use Ubiquity\security\acl\models\Role;
use Ubiquity\security\acl\models\Resource;
use Ubiquity\security\acl\models\Permission;
use Ubiquity\security\acl\models\AclElement;

/**
 * Ubiquity\security\acl$AclManager
 * This class is part of Ubiquity
 *
 * @author jc
 * @version 1.0.0
 *
 */
class AclManager {

	/**
	 *
	 * @var AclList
	 */
	protected static $aclList;

	public static function start(?array $providers = []) {
		self::$aclList = new AclList();
		self::$aclList->setProviders($providers);
		self::$aclList->loadAcls();
	}

	public static function addRole(string $name, ?array $parents = []) {
		self::$aclList->addRole(new Role($name, $parents));
	}

	public static function addResource(string $name, ?string $value = null) {
		self::$aclList->addResource(new Resource($name, $value));
	}

	public static function addPermission(string $name, int $level = 0) {
		self::$aclList->addPermission(new Permission($name, $level));
	}

	public static function allow(string $role, ?string $resource = '*', ?string $permission = 'ALL') {
		self::$aclList->allow($role, $resource, $permission);
	}

	public static function isAllowed(string $role, ?string $resource = '*', ?string $permission = 'ALL') {
		return self::$aclList->isAllowed($role, $resource, $permission);
	}
}

