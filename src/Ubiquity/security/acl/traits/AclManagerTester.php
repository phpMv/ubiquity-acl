<?php

namespace Ubiquity\security\acl\traits;

use Ubiquity\security\acl\models\AbstractAclPart;
use Ubiquity\security\acl\models\AclElement;
use Ubiquity\security\acl\models\AclList;

/**
 * @property ?AclList $aclList
 */
trait AclManagerTester {

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
	
	public static function roleExists(string $roleName): bool {
		return self::$aclList->roleExists($roleName);
	}

	public static function resourceExists(string $resourceName): bool {
		return self::$aclList->resourceExists($resourceName);
	}

	public static function permissionExists(string $permissionName): bool {
		return self::$aclList->permissionExists($permissionName);
	}
}