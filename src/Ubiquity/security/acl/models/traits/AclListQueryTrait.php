<?php
namespace Ubiquity\security\acl\models\traits;

use Ubiquity\security\acl\models\AclElement;

/**
 *
 * @author jc
 * @property AclElement[] $acls
 */
trait AclListQueryTrait {

	abstract public function getRoleByName(string $name);

	abstract public function getResourceByName(string $name);

	abstract public function getPermissionByName(string $name);

	public function getAclsWithRole(string $role) {
		$result = [];
		foreach ($this->acls as $acl) {
			if ($acl->getRole()->getName() === $role) {
				$result[] = $acl;
			}
		}
		return $result;
	}

	public function getAclsWithResource(string $resource) {
		$result = [];
		foreach ($this->acls as $acl) {
			if ($acl->getResource()->getName() === $resource) {
				$result[] = $acl;
			}
		}
		return $result;
	}

	public function getAclsWithPermission(string $permission) {
		$result = [];
		foreach ($this->acls as $acl) {
			if ($acl->getPermission()->getName() === $permission) {
				$result[] = $acl;
			}
		}
		return $result;
	}
}

