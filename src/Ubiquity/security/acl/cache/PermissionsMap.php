<?php
namespace Ubiquity\security\acl\cache;

use Ubiquity\cache\CacheManager;
use Ubiquity\security\acl\cache\traits\AclCacheTrait;
use Ubiquity\security\acl\AclManager;

/**
 * Ubiquity\security\acl\cache$PermissionsMap
 * This class is part of Ubiquity
 *
 * @author jc
 * @version 1.0.2
 *
 */
class PermissionsMap {
	use AclCacheTrait;

	/**
	 *
	 * @var array
	 */
	private $arrayMap;

	private const CACHE_KEY = 'permissionsMap';

	public function __construct() {
		$this->arrayMap = [];
		$this->createCache($this->getRootKey(self::CACHE_KEY));
	}

	protected function getKey($controller, $action) {
		return "$controller.$action";
	}
	
	private function _getRessourcePermission(string $controller, string $action) {
		return $this->arrayMap[$this->getKey($controller, $action)] ?? null;
	}

	public function addAction(string $controller, string $action, ?string $resource = '*', ?string $permission = 'ALL') {
		$this->arrayMap[$this->getKey($controller, $action)] = [
			'resource' => $resource,
			'permission' => $permission
		];
	}

	/**
	 *
	 * @param string $controller
	 * @param string $action
	 * @return array|NULL
	 */
	public function getRessourcePermission(string $controller, string $action) {
		if( ($r=$this->_getRessourcePermission($controller, $action))!==null){
			return $r;
		}
		if ($action !== '*') {
			$r=new \ReflectionMethod($controller,$action);
			$class=$r->getDeclaringClass()->getName();
			if($class!==$controller){
				return $this->_getRessourcePermission($class, $action);
			}
		}
		return $this->_getRessourcePermission($controller, '*');
	}

	public function save() {
		CacheManager::$cache->store($this->getRootKey(self::CACHE_KEY), $this->arrayMap);
	}

	public function init() {
		CacheManager::$cache->store($this->getRootKey(self::CACHE_KEY), $this->arrayMap = []);
	}

	public function load() {
		$this->arrayMap = CacheManager::$cache->fetch($this->getRootKey(self::CACHE_KEY));
	}

	public function getMap() {
		$result = [];
		foreach ($this->arrayMap as $k => $v) {
			$result[] = [
				'controller.action' => $k
			] + $v;
		}
		return $result;
	}

	public function getObjectMap() {
		$result = [];
		foreach ($this->arrayMap as $k => $v) {
			$resource = $v['resource'] ?? '*';
			$permission = $v['permission'] ?? 'ALL';
			$roles = AclManager::getAclList()->getRolesWithPermissionOnResource($resource, $permission);
			$result[] = new PermissionMapObject($k, $resource, $permission, $roles);
		}
		return $result;
	}
}

