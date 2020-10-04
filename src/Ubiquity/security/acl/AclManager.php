<?php
namespace Ubiquity\security\acl;

use Ubiquity\security\acl\models\AclList;
use Ubiquity\security\acl\models\Role;
use Ubiquity\security\acl\models\Resource;
use Ubiquity\security\acl\models\Permission;
use Ubiquity\security\acl\models\AclElement;
use Ubiquity\security\acl\persistence\AclProviderInterface;

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

	/**
	 * Create AclList with default roles and resources.
	 */
	public static function start(): void {
		self::$aclList = new AclList();
		self::$aclList->init();
	}

	/**
	 * Load acls, roles, resources and permissions from providers.
	 *
	 * @param AclProviderInterface[] $providers
	 */
	public static function initFromProviders(?array $providers = []): void {
		self::$aclList->setProviders($providers);
		self::$aclList->loadAcls();
		self::$aclList->loadRoles();
		self::$aclList->loadResources();
		self::$aclList->loadPermissions();
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

	/**
	 * Allow role to access to resource with the permission.
	 *
	 * @param string $role
	 * @param string $resource
	 * @param string $permission
	 */
	public static function allow(string $role, ?string $resource = '*', ?string $permission = 'ALL') {
		self::$aclList->allow($role, $resource ?? '*', $permission ?? 'ALL');
	}

	/**
	 * Check if access to resource is allowed for role with the permission.
	 *
	 * @param string $role
	 * @param string $resource
	 * @param string $permission
	 * @return bool
	 */
	public static function isAllowed(string $role, ?string $resource = '*', ?string $permission = 'ALL'): bool {
		return self::$aclList->isAllowed($role, $resource ?? '*', $permission ?? 'ALL');
	}
}

