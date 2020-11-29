<?php
namespace Ubiquity\security\acl\models\traits;

use Ubiquity\security\acl\models\AclElement;
use Ubiquity\security\acl\models\AbstractAclPart;

/**
 *
 * @author jc
 * @property AclElement[] $acls
 */
trait AclListQueryTrait {

	abstract public function getRoleByName(string $name);

	abstract public function getResourceByName(string $name);

	abstract public function getPermissionByName(string $name);

	abstract public function getProvider(string $providerClass);

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

	public function getRolesWithPermissionOnResource(string $resource, string $permission) {
		$result = [];
		foreach ($this->acls as $acl) {
			if ($acl->getPermission()->getName() === $permission && $acl->getResource()->getName() === $resource) {
				$result[] = $acl->getRole()->getName();
			}
		}
		return $result;
	}

	/**
	 *
	 * @param AbstractAclPart $part
	 * @param string $providerClass
	 * @return boolean
	 */
	public function existPartIn(AbstractAclPart $part, string $providerClass) {
		$prov = $this->getProvider($providerClass);
		if (isset($prov)) {
			return $prov->existPart($part);
		}
		return false;
	}

	/**
	 *
	 * @param AclElement $elm
	 * @param string $providerClass
	 * @return boolean
	 */
	public function existAclIn(AclElement $elm, string $providerClass) {
		$prov = $this->getProvider($providerClass);
		if (isset($prov)) {
			return $prov->existAcl($elm);
		}
		return false;
	}
}

