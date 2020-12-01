<?php
namespace Ubiquity\security\acl\persistence;

use Ubiquity\cache\CacheManager;
use Ubiquity\security\acl\models\AbstractAclPart;
use Ubiquity\security\acl\models\AclElement;
use Ubiquity\security\acl\models\Role;
use Ubiquity\security\acl\models\Resource;
use Ubiquity\security\acl\models\Permission;
use Ubiquity\security\acl\cache\traits\AclCacheTrait;

/**
 * Ubiquity\security\acl\persistence$AclCacheProvider
 * This class is part of Ubiquity
 *
 * @author jc
 * @version 1.0.0
 *
 */
class AclCacheProvider extends AclArrayProvider {
	use AclCacheTrait;

	public function __construct() {
		$cacheKeys = $this->getCacheKeys();
		foreach ($cacheKeys as $key) {
			$this->createCache($key);
		}
	}

	protected function getCacheKeys(): array {
		return [
			$this->getRootKey('acls'),
			$this->getRootKey(Role::class),
			$this->getRootKey(Resource::class),
			$this->getRootKey(Permission::class)
		];
	}

	protected function loadAllPart($class): array {
		$this->parts[$class] = CacheManager::$cache->fetch($this->getRootKey($class));
		return parent::loadAllPart($class);
	}

	public function loadAllAcls(): array {
		$this->aclsArray = CacheManager::$cache->fetch($this->getRootKey('acls'));
		return parent::loadAllAcls();
	}

	public function isAutosave(): bool {
		return false;
	}

	public function saveAll(): void {
		CacheManager::$cache->store($this->getRootKey('acls'), $this->aclsArray);
		$classes = [
			Role::class,
			Resource::class,
			Permission::class
		];
		foreach ($classes as $class) {
			CacheManager::$cache->store($this->getRootKey($class), $this->parts[$class] ?? []);
		}
	}

	public function getDetails(): array {
		return [
			'lock' => 'In memory or annotations cache'
		];
	}
}

