<?php
namespace Ubiquity\security\acl\cache;

use Ubiquity\cache\CacheManager;
use Ubiquity\security\acl\cache\traits\AclCacheTrait;

/**
 * Ubiquity\security\acl\cache$PermissionsMap
 * This class is part of Ubiquity
 *
 * @author jc
 * @version 1.0.0
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
		$this->createCache($this->getRootKey(self::CACHE_KEY));
	}

	protected function getKey($controller, $action) {
		return \crc32($controller . $action) . '_';
	}

	public function addAction(string $controller, string $action, string $resource, ?string $permission = 'ALL') {
		$this->arrayMap[$this->getKey($controller, $action)] = [
			'resource' => $resource,
			'permission' => $permission
		];
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
}

