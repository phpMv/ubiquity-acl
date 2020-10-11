<?php
namespace Ubiquity\security\acl\persistence;

use Ubiquity\cache\CacheManager;
use Ubiquity\security\acl\models\AbstractAclPart;
use Ubiquity\security\acl\models\AclElement;
use Ubiquity\security\acl\models\Role;
use Ubiquity\security\acl\models\Resource;
use Ubiquity\security\acl\models\Permission;

class AclCacheProvider extends AclArrayProvider {

	private $key = 'acls/';

	private function getRootKey($element) {
		return $this->key . \md5($element);
	}

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

	protected function createCache($part) {
		if (! CacheManager::$cache->exists($part)) {
			CacheManager::$cache->store($part, []);
		}
	}

	protected function loadAllPart($class): array {
		$this->parts[$class] = CacheManager::$cache->fetch($this->getRootKey($class));
		return parent::loadAllPart($class);
	}

	public function loadAllAcls(): array {
		$this->aclsArray = CacheManager::$cache->fetch($this->getRootKey('acls'));
		return parent::loadAllAcls();
	}

	public function savePart(AbstractAclPart $part) {
		$this->_savePart($part);
	}

	public function updatePart(AbstractAclPart $part) {
		$this->_updatePart($part);
	}

	public function saveAcl(AclElement $aclElement) {
		$this->_saveAcl($aclElement);
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
			CacheManager::$cache->store($this->getRootKey($class), $this->parts[$class]);
		}
	}
}

