<?php
namespace Ubiquity\security\acl;

use Ubiquity\security\acl\models\AclList;
use Ubiquity\security\acl\models\Role;
use Ubiquity\security\acl\models\Resource;
use Ubiquity\security\acl\models\Permission;
use Ubiquity\security\acl\models\AclElement;
use Ubiquity\security\acl\persistence\AclProviderInterface;
use Ubiquity\security\acl\models\AbstractAclPart;

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
		if (\count($providers) > 0) {
			self::$aclList->loadAcls();
			self::$aclList->loadRoles();
			self::$aclList->loadResources();
			self::$aclList->loadPermissions();
		}
	}

	public static function addRole(string $name, ?array $parents = []) {
		self::$aclList->addRole(new Role($name, $parents));
	}

	public static function addRoles(array $nameParents) {
		foreach ($nameParents as $name => $parents) {
			self::$aclList->addRole(new Role($name, $parents));
		}
	}

	public static function addResource(string $name, ?string $value = null) {
		self::$aclList->addResource(new Resource($name, $value));
	}

	public static function addResources(array $nameValue) {
		foreach ($nameValue as $name => $value) {
			self::$aclList->addResource(new Resource($name, $value));
		}
	}

	public static function addPermission(string $name, int $level = 0) {
		self::$aclList->addPermission(new Permission($name, $level));
	}

	public static function addPermissions(array $nameLevel) {
		foreach ($nameLevel as $name => $level) {
			self::$aclList->addPermission(new Permission($name, $level));
		}
	}

	public static function setPermissionLevel(string $name, int $level) {
		self::$aclList->setPermissionLevel($name, $level);
	}

	public static function getRoles() {
		return self::$aclList->getRoles();
	}

	public static function getResources() {
		return self::$aclList->getResources();
	}

	public static function getPermissions() {
		return self::$aclList->getPermissions();
	}

	public static function getAcls() {
		return self::$aclList->getAcls();
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
	 * Add role, resource and permission and allow this role to access to resource with the permission.
	 *
	 * @param string $role
	 * @param string $resource
	 * @param string $permission
	 */
	public static function addAndAllow(string $role, ?string $resource = '*', ?string $permission = 'ALL') {
		self::$aclList->addRole(new Role($role, []));
		if ($resource !== '*') {
			self::$aclList->addResource(new Resource($resource));
		}
		if ($permission !== 'ALL') {
			self::$aclList->addPermission(new Permission($permission));
		}
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

	public static function saveAll() {
		self::$aclList->saveAll();
	}

	public static function removeRole(string $role) {
		self::$aclList->removeRole($role);
	}

	public static function removePermission(string $permission) {
		self::$aclList->removePermission($permission);
	}

	public static function removeResource(string $resource) {
		self::$aclList->removeResource($resource);
	}

	public static function removeAcl(string $role, string $resource, string $permission = null) {
		self::$aclList->removeAcl($role, $resource, $permission);
	}
}

