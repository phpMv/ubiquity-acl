<?php
namespace Ubiquity\security\acl\models;

use Ubiquity\security\acl\persistence\AclProviderInterface;
use Ubiquity\exceptions\AclException;
use Ubiquity\security\acl\models\traits\AclListOperationsTrait;
use Ubiquity\security\acl\models\traits\AclListQueryTrait;
use Ubiquity\security\acl\persistence\AclCacheProvider;

/**
 * Ubiquity\security\acl\models$AclList
 * This class is part of Ubiquity
 *
 * @author jc
 * @version 1.0.0
 *
 */
class AclList {
	use AclListOperationsTrait,AclListQueryTrait;

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

	public function __construct() {
		$this->providers = [];
		$this->init();
	}

	public function init() {
		$this->roles = [
			'role_@ALL' => new Role('@ALL')
		];
		$this->resources = [
			'res_*' => new Resource('*')
		];
		$this->permissions = [
			'perm_ALL' => new Permission('ALL', 1000)
		];
		$this->elementsCache = [];
		$this->acls = [];
		foreach ($this->providers as $prov) {
			$prov->clearAll();
		}
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

	public function getRolePermissionsOn(string $roleName, $resourceName = '*'): array {
		$role = $this->getRoleByName($roleName);
		$parents = $role->getParentsArray();
		$result = [];
		foreach ($this->acls as $aclElement) {
			$aclRoleName = $aclElement->getRole()->getName();
			if ($aclRoleName === '@ALL' || $aclRoleName === $roleName) {
				$aclResourceName = $aclElement->getResource()->getName();
				if ($aclResourceName === '*' || $aclResourceName === $resourceName || \strpos($resourceName, $aclResourceName.'.')!==false) {
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

	/**
	 *
	 * @param string $providerClass
	 * @return \Ubiquity\security\acl\persistence\AclProviderInterface|NULL
	 */
	public function getProvider(string $providerClass) {
		foreach ($this->providers as $prov) {
			if ($prov instanceof $providerClass) {
				return $prov;
			}
		}
		return null;
	}

	/**
	 *
	 * @param string $id_
	 * @return ?AclElement
	 */
	public function getAclById_(string $id_): ?AclElement {
		foreach ($this->acls as $acl) {
			if ($acl->getId_() === $id_) {
				return $acl;
			}
		}
		return null;
	}

	public function getProviderClasses() {
		$result = [];
		foreach ($this->providers as $prov) {
			$result[] = \get_class($prov);
		}
		return $result;
	}

	public function hasCache() {
		foreach ($this->providers as $prov) {
			if ($prov instanceof AclCacheProvider) {
				return true;
			}
		}
		return false;
	}

	public function getElementsNames($part) {
		$result = [];
		foreach ($this->$part as $elm) {
			$result[] = $elm->__toString();
		}
		return $result;
	}
}

