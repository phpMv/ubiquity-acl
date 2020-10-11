<?php
namespace Ubiquity\security\acl\models\traits;

use Ubiquity\security\acl\models\AclElement;
use Ubiquity\security\acl\models\AbstractAclPart;

/**
 * Ubiquity\security\acl\models\traits$AclListOperations
 * This class is part of Ubiquity
 *
 * @author jc
 * @version 1.0.0
 *
 */
trait AclListOperationsTrait {

	public function saveAclElement(AclElement $aclElement) {
		foreach ($this->providers as $provider) {
			$provider->saveAcl($aclElement);
		}
	}

	public function removeAclElement(AclElement $aclElement) {
		foreach ($this->providers as $provider) {
			$provider->removeAcl($aclElement);
		}
	}

	public function savePart(AbstractAclPart $aclPart) {
		foreach ($this->providers as $provider) {
			$provider->savePart($aclPart);
		}
	}

	public function updatePart(AbstractAclPart $aclPart) {
		foreach ($this->providers as $provider) {
			$provider->updatePart($aclPart);
		}
	}

	public function removePart(AbstractAclPart $aclPart) {
		foreach ($this->providers as $provider) {
			$provider->removePart($aclPart);
		}
	}

	public function removeRole(string $roleName) {
		$role = $this->getRoleByName($roleName);
		unset($this->roles["role_$roleName"]);
		$this->removePart($role);
	}

	public function removePermission(string $permissionName) {
		$permission = $this->getPermissionByName($permissionName);
		unset($this->permissions["perm_$permissionName"]);
		$this->removePart($permission);
	}

	public function removeResource(string $resourceName) {
		$resource = $this->getResourceByName($resourceName);
		unset($this->resources["res_$resourceName"]);
		$this->removePart($resource);
	}

	public function removeAcl(string $roleName, string $resourceName, string $permissionName = null) {
		$toRemove = [];
		foreach ($this->acls as $index => $acl) {
			if ($acl->getResource()->getName() === $resourceName && $acl->getRole()->getName() === $roleName) {
				if ($permissionName == null || $acl->getPermission()->getName() === $permissionName) {
					foreach ($this->providers as $provider) {
						$provider->removeAcl($acl);
					}
					$toRemove[] = $index;
				}
			}
		}
		foreach ($toRemove as $remove) {
			unset($this->acls[$remove]);
		}
	}

	public function saveAll() {
		foreach ($this->providers as $provider) {
			if (! $provider->isAutosave()) {
				$provider->saveAll();
			}
		}
	}
}

