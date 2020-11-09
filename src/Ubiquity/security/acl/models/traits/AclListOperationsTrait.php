<?php
namespace Ubiquity\security\acl\models\traits;

use Ubiquity\security\acl\models\AclElement;
use Ubiquity\security\acl\models\AbstractAclPart;
use Ubiquity\security\acl\models\Role;
use Ubiquity\security\acl\models\Resource;
use Ubiquity\security\acl\models\Permission;
use Ubiquity\security\acl\persistence\AclProviderInterface;

/**
 * Ubiquity\security\acl\models\traits$AclListOperations
 * This class is part of Ubiquity
 *
 * @author jc
 * @version 1.0.0
 * @property Role[] $roles
 * @property Resource[] $resources
 * @property Permission[] $permissions
 * @property AclProviderInterface[] $providers
 * @property array $elementsCache
 * @property AclElement[] $acls
 *
 */
trait AclListOperationsTrait {

	abstract public function getRoleByName(string $name);

	abstract public function getResourceByName(string $name);

	abstract public function getPermissionByName(string $name);

	abstract public function init();

	abstract protected function elementExistByName(string $name, array $inArray): bool;

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
		unset($this->roles[$roleName]);
		$this->unsetCache("role_$roleName");
		$this->removeAcl($roleName);
		$this->removePart($role);
	}

	protected function unsetCache($name) {
		if (isset($this->elementsCache[$name])) {
			unset($this->elementsCache[$name]);
		}
	}

	public function removePermission(string $permissionName) {
		$permission = $this->getPermissionByName($permissionName);
		unset($this->permissions[$permissionName]);
		$this->unsetCache("perm_$permissionName");
		$this->removeAcl(null, null, $permissionName);
		$this->removePart($permission);
	}

	public function removeResource(string $resourceName) {
		$resource = $this->getResourceByName($resourceName);
		unset($this->resources[$resourceName]);
		$this->unsetCache("res_$resourceName");
		$this->removeAcl(null, $resourceName);
		$this->removePart($resource);
	}

	public function removeAcl(string $roleName = null, string $resourceName = null, string $permissionName = null) {
		$toRemove = [];
		foreach ($this->acls as $index => $acl) {
			if (($resourceName == null || $acl->getResource()->getName() === $resourceName) && ($roleName == null || $acl->getRole()->getName() === $roleName) && ($permissionName == null || $acl->getPermission()->getName() === $permissionName)) {
				foreach ($this->providers as $provider) {
					$provider->removeAcl($acl);
				}
				$toRemove[] = $index;
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

	public function clear() {
		$this->init();
	}

	public function addRole(Role $role) {
		$this->roles[$role->getName()] = $role;
		$this->savePart($role);
	}

	public function addResource(Resource $resource) {
		$this->resources[$resource->getName()] = $resource;
		$this->savePart($resource);
	}

	public function addPermission(Permission $permission) {
		$this->permissions[$permission->getName()] = $permission;
		$this->savePart($permission);
	}

	public function setPermissionLevel(string $name, int $level) {
		$perm = $this->getPermissionByName($name);
		$perm->setLevel($level);
		$this->updatePart($perm);
	}

	public function allow(string $roleName, string $resourceName, string $permissionName) {
		$aclElm = new AclElement();
		$aclElm->allow($this->getRoleByName($roleName), $this->getResourceByName($resourceName), $this->getPermissionByName($permissionName));
		$this->acls[] = $aclElm;
		$this->saveAclElement($aclElm);
	}

	public function addAndAllow(string $roleName, string $resourceName, string $permissionName) {
		if (! $this->elementExistByName($roleName, $this->roles)) {
			$this->addRole(new Role($roleName));
		}
		if ($resourceName !== '*' && ! $this->elementExistByName($resourceName, $this->resources)) {
			$this->addResource(new Resource($resourceName));
		}
		if ($permissionName !== 'ALL' && ! $this->elementExistByName($permissionName, $this->permissions)) {
			$this->addPermission(new Permission($permissionName));
		}
		$this->allow($roleName, $resourceName ?? '*', $permissionName ?? 'ALL');
	}
}

