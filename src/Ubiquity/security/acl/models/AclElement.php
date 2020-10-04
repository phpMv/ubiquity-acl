<?php
namespace Ubiquity\security\acl\models;

class AclElement {

	/**
	 *
	 * @var Role
	 */
	protected $role;

	/**
	 *
	 * @var Permission
	 */
	protected $permission;

	/**
	 *
	 * @var \Ubiquity\security\acl\models\Resource
	 */
	protected $resource;

	/**
	 *
	 * @return Role
	 */
	public function getRole() {
		return $this->role;
	}

	/**
	 *
	 * @return Permission
	 */
	public function getPermission() {
		return $this->permission;
	}

	/**
	 *
	 * @return Resource
	 */
	public function getResource() {
		return $this->resource;
	}

	public function fromArray($aclArray) {
		$role = new Role();
		$role->fromArray($aclArray['role']);
		$resource = new Resource();
		$resource->fromArray($aclArray['resource']);
		$permission = new Permission();
		$permission->fromArray($aclArray['permission']);
		$this->role = $role;
		$this->permission = $permission;
		$this->resource = $resource;
	}

	public function toArray(): array {
		return [
			'resource' => $this->resource->toArray(),
			'role' => $this->role->toArray(),
			'permission' => $this->permission->toArray()
		];
	}

	public function allow(Role $role, Resource $resource, Permission $permission) {
		$this->role = $role;
		$this->resource = $resource;
		$this->permission = $permission;
	}
}

