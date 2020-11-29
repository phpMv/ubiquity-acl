<?php
namespace Ubiquity\security\acl\cache;

/**
 * Ubiquity\security\acl\cache$PermissionMapObject
 * This class is part of Ubiquity
 *
 * @author jc
 * @version 1.0.0
 *
 */
class PermissionMapObject {

	private $controllerAction;

	private $resource;

	private $permission;

	private $roles;

	public function __construct(?string $controllerAction = '', ?string $resource = '', ?string $permission = '', ?array $roles = []) {
		$this->controllerAction = $controllerAction;
		$this->resource = $resource;
		$this->permission = $permission;
		$this->roles = $roles;
	}

	/**
	 *
	 * @return mixed
	 */
	public function getControllerAction() {
		return $this->controllerAction;
	}

	/**
	 *
	 * @return mixed
	 */
	public function getResource() {
		return $this->resource;
	}

	/**
	 *
	 * @return mixed
	 */
	public function getPermission() {
		return $this->permission;
	}

	/**
	 *
	 * @param mixed $controllerAction
	 */
	public function setControllerAction($controllerAction) {
		$this->controllerAction = $controllerAction;
	}

	/**
	 *
	 * @param mixed $resource
	 */
	public function setResource($resource) {
		$this->resource = $resource;
	}

	/**
	 *
	 * @param mixed $permission
	 */
	public function setPermission($permission) {
		$this->permission = $permission;
	}

	/**
	 *
	 * @return mixed
	 */
	public function getRoles() {
		return $this->roles;
	}

	/**
	 *
	 * @param mixed $roles
	 */
	public function setRoles($roles) {
		$this->roles = $roles;
	}

	public function getId_() {
		return $this->controllerAction;
	}
}

