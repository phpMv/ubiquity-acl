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

/**
 * Ubiquity\security\acl$AclManager
 * This class is part of Ubiquity
 *
 * @author jc
 * @version 1.0.1
 *
 */
class AclManager {

	protected static ?AclList $aclList=null;

	protected static PermissionsMap $permissionMap;

	protected static array $providersPersistence;

	/**
	 * Create AclList with default roles and resources.
	 */
	public static function start(): void {
		self::$aclList = new AclList();
		self::$aclList->init();
	}

	/**
	 * Start the Acls with AclCacheProvider (for attributes or annotations).
	 */
	public static function startWithCacheProvider(): void {
		self::start();
		self::initFromProviders([new AclCacheProvider()]);
	}

	/**
	 * Check whether the Acl service is started.
	 *
	 * @return bool
	 */
	public static function isStarted(): bool {
		return isset(self::$aclList) && (self::$aclList instanceof AclList);
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

	/**
	 *
	 * @param array|string $selectedProviders
	 */
	public static function reloadFromSelectedProviders($selectedProviders = '*') {
		$sProviders = self::$aclList->getProviders();
		self::$aclList->clear();
		$providers = [];
		foreach ($sProviders as $prov) {
			if ($selectedProviders === '*' || (\is_array($selectedProviders) && \array_search(\get_class($prov), $selectedProviders) !== false)) {
				$providers[] = $prov;
			}
		}
		self::initFromProviders($providers);
		self::$aclList->setProviders($sProviders);
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

	/**
	 *
	 * @return \Ubiquity\security\acl\models\AclList
	 */
	public static function getAclList() {
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
	public static function allow(string $role, ?string $resource = '*', ?string $permission = 'ALL') {
		self::$aclList->allow($role, $resource ?? '*', $permission ?? 'ALL');
	}

	/**
	 * Add role, resource and permission and allow this role to access to resource with the permission.
	 *
	 * @param string $role
	 * @param ?string $resource
	 * @param ?string $permission
	 */
	public static function addAndAllow(string $role, ?string $resource = '*', ?string $permission = 'ALL') {
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

	public static function isAllowedRoute(string $role,string $routeName){
		$routeInfo=Router::getRouteInfoByName($routeName);
		if (!isset ( $routeDetails ['controller'] )) {
			$routeInfo=current($routeInfo);
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
	public static function saveAll() {
		self::$aclList->saveAll();
	}

	/**
	 *
	 * @param string $role
	 */
	public static function removeRole(string $role) {
		self::$aclList->removeRole($role);
	}

	/**
	 *
	 * @param string $permission
	 */
	public static function removePermission(string $permission) {
		self::$aclList->removePermission($permission);
	}

	/**
	 *
	 * @param string $resource
	 */
	public static function removeResource(string $resource) {
		self::$aclList->removeResource($resource);
	}

	/**
	 *
	 * @param string $role
	 * @param string $resource
	 * @param ?string $permission
	 */
	public static function removeAcl(string $role, string $resource, ?string $permission = null) {
		self::$aclList->removeAcl($role, $resource, $permission);
	}

	/**
	 * Initialize acls cache with controllers annotations.
	 * Do not execute at runtime
	 *
	 * @param array $config
	 * @throws \Ubiquity\exceptions\AclException
	 */
	public static function initCache(&$config) {
		if(!self::isStarted()){
			self::start();
			self::initFromProviders([
				new AclCacheProvider()
			]);
		}
		self::filterProviders(AclCacheProvider::class);
		self::reloadFromSelectedProviders([]);
		self::registerAnnotations();
		$files = \Ubiquity\cache\CacheManager::getControllersFiles($config, true);
		$parser = new AclControllerParser();
		$parser->init();
		foreach ($files as $file) {
			if (\is_file($file)) {
				$controller = ClassUtils::getClassFullNameFromFile($file);
				try {
					$parser->parse($controller);
				} catch (\Exception $e) {
					if ($e instanceof AclException) {
						throw $e;
					}
				}
			}
		}
		$parser->save();
		self::removefilterProviders();
		self::reloadFromSelectedProviders();
	}

	protected static function registerAnnotations() {
		CacheManager::getAnnotationsEngineInstance()->registerAcls();
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

	/**
	 *
	 * @param string $providerClass
	 * @return AclProviderInterface|NULL
	 */
	public static function getProvider(string $providerClass):?AclProviderInterface {
		return self::$aclList->getProvider($providerClass);
	}

	public static function getModelClassesSwap(): array {
		$result = [];
		$aclList = self::getAclList();
		if (isset($aclList)) {
			foreach ($aclList->getProviders() as $prov) {
				$result += $prov->getModelClassesSwap();
			}
		}
		return $result;
	}

	public static function filterProviders(string $providerClass):void {
		$providers = self::$aclList->getProviders();
		$filter = [];
		foreach ($providers as $prov) {
			if ($prov instanceof $providerClass) {
				$filter[] = $prov;
			}
		}
		self::$aclList->setProviders($filter);
		self::$providersPersistence = $providers;
	}

	public static function removefilterProviders():void {
		self::$aclList->setProviders(self::$providersPersistence);
	}

	/**
	 * Initializes AclDAOProvider and creates ACL tables in the specified dbOffset.
	 * Do not use in production
	 * @param array $config
	 * @param string $dbOffset
	 * @param array $classes
	 *        	associative array['acl'=>'','role'=>'','resource'=>'','permission'=>'']
	 * @return AclDAOProvider
	 * @throws AclException
	 */
	public static function initializeDAOProvider(array &$config, string $dbOffset='default', array $classes=[]): AclDAOProvider {
		self::start();
		return AclDAOProvider::initializeProvider($config,$dbOffset,$classes);
	}
}
