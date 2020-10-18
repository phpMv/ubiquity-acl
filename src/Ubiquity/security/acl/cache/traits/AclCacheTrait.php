<?php
namespace Ubiquity\security\acl\cache\traits;

use Ubiquity\cache\CacheManager;

/**
 * Ubiquity\security\acl\cache\traits$AclCacheTrait
 * This class is part of Ubiquity
 *
 * @author jc
 * @version 1.0.0
 *
 */
trait AclCacheTrait {

	protected static $KEY = 'acls/';

	protected function getRootKey($element) {
		return self::$KEY . \md5($element);
	}

	protected function createCache($part) {
		if (! CacheManager::$cache->exists($part)) {
			CacheManager::$cache->store($part, []);
		}
	}
}

