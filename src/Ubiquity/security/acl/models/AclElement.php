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
	 * @var Resource
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
}

