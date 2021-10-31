<?php

namespace Ubiquity\security\acl\models;

/**
 * Ubiquity\security\acl\models$AclElement
 * This class is part of Ubiquity
 *
 * @author jc
 * @version 1.0.1
 *
 */

class AclElement {

	/**
	 *
	 * @id
	 * @column("name"=>"id","nullable"=>false,"dbType"=>"int(11)")
	 */
	#[\Ubiquity\attributes\items\Id()]
	#[\Ubiquity\attributes\items\Column(name:'id',nullable:false,dbType:'int(11)')]
	protected $id;

	/**
	 *
	 * @var Role
	 * @manyToOne
	 * @joinColumn("className"=>"Ubiquity\\security\\acl\\models\\Role","name"=>"idRole","nullable"=>false)
	 */
	#[\Ubiquity\attributes\items\ManyToOne()]
	#[\Ubiquity\attributes\items\JoinColumn(className:"Ubiquity\\security\\acl\\models\\Role",name: "idRole", nullable: false)]
	protected $role;

	/**
	 *
	 * @var Permission
	 * @manyToOne
	 * @joinColumn("className"=>"Ubiquity\\security\\acl\\models\\Permission","name"=>"idPermission","nullable"=>false)
	 */
	#[\Ubiquity\attributes\items\ManyToOne()]
	#[\Ubiquity\attributes\items\JoinColumn(className:"Ubiquity\\security\\acl\\models\\Permission",name: "idPermission", nullable: false)]
	protected $permission;

	/**
	 *
	 * @var \Ubiquity\security\acl\models\Resource
	 * @manyToOne
	 * @joinColumn("className"=>"Ubiquity\\security\\acl\\models\\Resource","name"=>"idResource","nullable"=>false)
	 */
	#[\Ubiquity\attributes\items\ManyToOne()]
	#[\Ubiquity\attributes\items\JoinColumn(className:"Ubiquity\\security\\acl\\models\\Resource",name: "idResource", nullable: false)]
	protected $resource;

	/**
	 *
	 * @return Role
	 *
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
	 * @return \Ubiquity\security\acl\models\Resource
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

	/**
	 *
	 * @return mixed
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 *
	 * @param mixed $id
	 */
	public function setId($id) {
		$this->id = $id;
	}

	/**
	 *
	 * @param \Ubiquity\security\acl\models\Role $role
	 */
	public function setRole($role) {
		$this->role = $role;
	}

	/**
	 *
	 * @param \Ubiquity\security\acl\models\Permission $permission
	 */
	public function setPermission($permission) {
		$this->permission = $permission;
	}

	/**
	 *
	 * @param \Ubiquity\security\acl\models\Resource $resource
	 */
	public function setResource($resource) {
		$this->resource = $resource;
	}

	public function getId_() {
		$id = '';
		if (isset($this->role)) {
			$id = $this->role->getName();
		}
		if (isset($this->resource)) {
			$id .= $this->resource->getName();
		}
		if (isset($this->permission)) {
			$id .= $this->permission->getName();
		}
		return \crc32($id) . '.';
	}

	public function castAs(string $class) {
		return unserialize(sprintf('O:%d:"%s"%s', \strlen($class), $class, \strstr(\strstr(\serialize($this), '"'), ':')));
	}
}

