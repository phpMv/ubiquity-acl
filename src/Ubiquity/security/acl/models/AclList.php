<?php
namespace Ubiquity\security\acl\models;

use Ubiquity\security\acl\persistence\AclLoaderInterface;

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
	 * @var Resource[]
	 */
	protected $resources;

	/**
	 *
	 * @var Permission[]
	 */
	protected $permissions;

	/**
	 *
	 * @var AclLoaderInterface[]
	 */
	protected $providers;

	public function loadAcls(): void {
		foreach ($this->providers as $provider) {
			$this->acls += $provider->loadAllAcls();
		}
	}

	public function loadRoles(): void {
		foreach ($this->providers as $provider) {
			$this->acls += $provider->loadAllRoles();
		}
	}

	public function loadResources(): void {
		foreach ($this->providers as $provider) {
			$this->acls += $provider->loadAllResources();
		}
	}

	public function loadPermissions(): void {
		foreach ($this->providers as $provider) {
			$this->acls += $provider->loadAllPermissions();
		}
	}
}

