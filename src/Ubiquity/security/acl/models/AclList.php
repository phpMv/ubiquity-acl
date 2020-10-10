<?php
namespace Ubiquity\security\acl\models;

use Ubiquity\security\acl\persistence\AclProviderInterface;
use Ubiquity\exceptions\AclException;

/**
 * Ubiquity\security\acl\models$AclList
 * This class is part of Ubiquity
 *
 * @author jc
 * @version 1.0.0
 *
 */
class AclList {

	/**
	 *
	 * @var AclElement[]
	 */
	protected $acls;

	/**
	 *
	 * @var Role[]
	 */
	protected $roles;

	/**
	 *
	 * @var \Ubiquity\security\acl\models\Resource[]
	 */
	protected $resources;

	/**
	 *
	 * @var Permission[]
	 */
	protected $permissions;

	/**
	 *
	 * @var AclProviderInterface[]
	 */
	protected $providers = [];

	protected $elementsCache = [];

	protected function getElementByName(string $name, array $inArray, string $type) {
		foreach ($inArray as $elm) {
			if ($elm->getName() == $name) {
				return $elm;
			}
		}
		throw new AclException("$name does not exist in $type ACL");
	}

	protected function elementExistByName(string $name, array $inArray): bool {
		foreach ($inArray as $elm) {
			if ($elm->getName() == $name) {
				return true;
			}
		}
		return false;
	}

	public function init() {
		$this->roles['role_@ALL'] = new Role('@ALL');
		$this->resources['res_*'] = new Resource('*');
		$this->permissions['perm_ALL'] = new Permission('ALL', 1000);
		$this->acls = [];
	}

	public function getRoleByName(string $name) {
		return $this->elementsCache["role_$name"] ??= $this->getElementByName($name, $this->roles, 'roles');
	}

	public function getResourceByName(string $name) {
		return $this->elementsCache["res_$name"] ??= $this->getElementByName($name, $this->resources, 'resources');
	}

	public function getPermissionByName(string $name) {
		return $this->elementsCache["perm_$name"] ??= $this->getElementByName($name, $this->permissions, 'permissions');
	}

	public function loadAcls(): array {
		foreach ($this->providers as $provider) {
			$this->acls += $provider->loadAllAcls();
		}
		return $this->acls;
	}

	public function loadRoles(): array {
		foreach ($this->providers as $provider) {
			$this->roles += $provider->loadAllRoles();
		}
		return $this->roles;
	}

	public function loadResources(): array {
		foreach ($this->providers as $provider) {
			$this->resources += $provider->loadAllResources();
		}
		return $this->resources;
	}

	public function loadPermissions(): array {
		foreach ($this->providers as $provider) {
			$this->permissions += $provider->loadAllPermissions();
		}
		return $this->permissions;
	}

	public function addProvider(AclProviderInterface $provider) {
		$this->providers[] = $provider;
	}

	/**
	 *
	 * @return AclElement[]
	 */
	public function getAcls() {
		return $this->acls;
	}

	/**
	 *
	 * @return Role[]
	 */
	public function getRoles() {
		return $this->roles;
	}

	/**
	 *
	 * @return \Ubiquity\security\acl\models\Resource[]
	 */
	public function getResources() {
		return $this->resources;
	}

	/**
	 *
	 * @return Permission[]
	 */
	public function getPermissions() {
		return $this->permissions;
	}

	/**
	 *
	 * @return AclProviderInterface[]
	 */
	public function getProviders() {
		return $this->providers;
	}

	/**
	 *
	 * @param AclProviderInterface[] $providers
	 */
	public function setProviders($providers) {
		$this->providers = $providers;
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
	}

	public function allow(string $roleName, string $resourceName, string $permissionName) {
		$aclElm = new AclElement();
		$aclElm->allow($this->getRoleByName($roleName), $this->getResourceByName($resourceName), $this->getPermissionByName($permissionName));
		$this->acls[] = $aclElm;
		$this->saveAclElement($aclElm);
	}

	public function getRolePermissionsOn(string $roleName, $resourceName = '*'): array {
		$role = $this->getRoleByName($roleName);
		$parents = $role->getParentsArray();
		$result = [];
		foreach ($this->acls as $aclElement) {
			$aclRoleName = $aclElement->getRole()->getName();
			if ($aclRoleName === '@ALL' || $aclRoleName === $roleName) {
				$aclResourceName = $aclElement->getResource()->getName();
				if ($aclResourceName === '*' || $aclResourceName === $resourceName) {
					$result[] = $aclElement;
				}
			}
		}
		foreach ($parents as $parentElm) {
			$result += $this->getRolePermissionsOn($parentElm, $resourceName);
		}
		return $result;
	}

	public function isAllowed(string $roleName, string $resourceName, string $permissionName) {
		$acls = $this->getRolePermissionsOn($roleName, $resourceName);
		if (\count($acls) > 0) {
			$permissionLevel = $this->getPermissionByName($permissionName)->getLevel();
			foreach ($acls as $aclElm) {
				$level = $aclElm->getPermission()->getLevel();
				if ($level >= $permissionLevel) {
					return true;
				}
			}
		}
		return false;
	}

	public function saveAclElement(AclElement $aclElement) {
		foreach ($this->providers as $provider) {
			$provider->saveAcl($aclElement);
		}
	}

	public function savePart(AbstractAclPart $aclPart) {
		foreach ($this->providers as $provider) {
			$provider->savePart($aclPart);
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

