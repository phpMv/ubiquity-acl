<?php
namespace Ubiquity\security\acl;

use Ubiquity\cache\CacheManager;
use Ubiquity\cache\ClassUtils;
use Ubiquity\exceptions\AclException;
use Ubiquity\security\acl\cache\AclControllerParser;
use Ubiquity\security\acl\cache\PermissionsMap;
use Ubiquity\security\acl\models\AbstractAclPart;
use Ubiquity\security\acl\models\AclElement;
use Ubiquity\security\acl\models\AclList;
use Ubiquity\security\acl\models\Permission;
use Ubiquity\security\acl\models\Resource;
use Ubiquity\security\acl\models\Role;
use Ubiquity\security\acl\persistence\AclCacheProvider;
use Ubiquity\controllers\Router;
use Ubiquity\security\acl\persistence\AclDAOProvider;
use Ubiquity\security\acl\persistence\AclProviderInterface;
use Ubiquity\security\acl\traits\AclManagerInit;

/**
 * Ubiquity\security\acl$AclManager
 * This class is part of Ubiquity
 *
 * @author jc
 * @version 1.0.2
 *
 */
class AclManager {

	use AclManagerInit;

	protected static ?AclList $aclList=null;

	protected static PermissionsMap $permissionMap;

	protected static array $providersPersistence;


	public static function addRole(string $name, ?array $parents = []): void {
		self::$aclList->addRole(new Role($name, $parents));
	}

	public static function addRoles(array $nameParents): void {
		foreach ($nameParents as $name => $parents) {
			self::$aclList->addRole(new Role($name, $parents));
		}
	}

	public static function addResource(string $name, ?string $value = null): void {
		self::$aclList->addResource(new Resource($name, $value));
	}

	public static function addResources(array $nameValue): void {
		foreach ($nameValue as $name => $value) {
			self::$aclList->addResource(new Resource($name, $value));
		}
	}

	public static function addPermission(string $name, int $level = 0): void {
		self::$aclList->addPermission(new Permission($name, $level));
	}

	public static function addPermissions(array $nameLevel): void {
		foreach ($nameLevel as $name => $level) {
			self::$aclList->addPermission(new Permission($name, $level));
		}
	}

	public static function setPermissionLevel(string $name, int $level): void {
		self::$aclList->setPermissionLevel($name, $level);
	}

	public static function getRoles(): array {
		return self::$aclList->getRoles();
	}

	public static function getResources(): array {
		return self::$aclList->getResources();
	}

	/**
	 *
	 * @return \Ubiquity\security\acl\models\AclList
	 */
	public static function getAclList(): ?AclList {
		return AclManager::$aclList;
	}

	public static function getPermissions():array {
		return self::$aclList->getPermissions();
	}

	public static function getAcls() {
		return self::$aclList->getAcls();
	}

	/**
	 * Allow role to access to resource with the permission.
	 *
	 * @param string $role
	 * @param ?string $resource
	 * @param ?string $permission
	 */
	public static function allow(string $role, ?string $resource = '*', ?string $permission = 'ALL'): void {
		self::$aclList->allow($role, $resource ?? '*', $permission ?? 'ALL');
	}

	/**
	 * Add role, resource and permission and allow this role to access to resource with the permission.
	 *
	 * @param string $role
	 * @param ?string $resource
	 * @param ?string $permission
	 */
	public static function addAndAllow(string $role, ?string $resource = '*', ?string $permission = 'ALL'): void {
		self::$aclList->addAndAllow($role, $resource ?? '*', $permission ?? 'ALL');
	}

	/**
	 * Check if access to resource is allowed for role with the permission.
	 *
	 * @param string $role
	 * @param ?string $resource
	 * @param ?string $permission
	 * @return bool
	 */
	public static function isAllowed(string $role, ?string $resource = '*', ?string $permission = 'ALL'): bool {
		return self::$aclList->isAllowed($role, $resource ?? '*', $permission ?? 'ALL');
	}

	public static function isAllowedRoute(string $role,string $routeName): bool {
		$routeInfo=Router::getRouteInfoByName($routeName);
		if (!isset ( $routeInfo ['controller'] )) {
			$routeInfo=\current($routeInfo);
		}
		$controller=$routeInfo['controller']??null;
		$action=$routeInfo['action']??null;
		if(isset($controller) && isset($action)){
			$resourceController = self::getPermissionMap ()->getRessourcePermission ( $controller, $action );
			if (isset ( $resourceController )) {
				try{
					if (self::isAllowed ( $role, $resourceController ['resource'], $resourceController ['permission'] )) {
						return true;
					}
				}
				catch(AclException $e){
					//Nothing to do
				}
			}
			return false;
		}
		return false;
	}

	/**
	 * Save all acls,roles, resources and permissions for AclProviders with no autoSave.
	 */
	public static function saveAll(): void {
		self::$aclList->saveAll();
	}

	/**
	 * Checks if ACL cache is updated.
	 * Do not use directly from this class: use checkCache instead.
	 * @return bool
	 */
	public static function cacheUpdated(): bool {
		return self::$aclList->cacheUpdated();
	}
	
	/**
	 *
	 * @param string $role
	 */
	public static function removeRole(string $role): void {
		self::$aclList->removeRole($role);
	}

	/**
	 *
	 * @param string $permission
	 */
	public static function removePermission(string $permission): void {
		self::$aclList->removePermission($permission);
	}

	/**
	 *
	 * @param string $resource
	 */
	public static function removeResource(string $resource): void {
		self::$aclList->removeResource($resource);
	}

	/**
	 *
	 * @param string $role
	 * @param string $resource
	 * @param ?string $permission
	 */
	public static function removeAcl(string $role, string $resource, ?string $permission = null): void {
		self::$aclList->removeAcl($role, $resource, $permission);
	}

	/**
	 *
	 * @return \Ubiquity\security\acl\cache\PermissionsMap
	 */
	public static function getPermissionMap():PermissionsMap {
		if (! isset(self::$permissionMap)) {
			self::$permissionMap = new PermissionsMap();
			self::$permissionMap->load();
		}
		return self::$permissionMap;
	}

	/**
	 *
	 * @param string $controller
	 * @param string $action
	 * @param string $resource
	 * @param string $permission
	 */
	public static function associate(string $controller, string $action, string $resource, string $permission = 'ALL'):void {
		self::$aclList->getResourceByName($resource);
		self::$aclList->getPermissionByName($permission);
		self::$permissionMap->addAction($controller, $action, $resource, $permission);
	}

	/**
	 *
	 * @param AbstractAclPart $part
	 * @param string $providerClass
	 * @return boolean
	 */
	public static function existPartIn(AbstractAclPart $part, string $providerClass):bool {
		return self::$aclList->existPartIn($part, $providerClass);
	}

	/**
	 *
	 * @param AclElement $elm
	 * @param string $providerClass
	 * @return boolean
	 */
	public static function existAclIn(AclElement $elm, string $providerClass):bool {
		return self::$aclList->existAclIn($elm, $providerClass);
	}
}
