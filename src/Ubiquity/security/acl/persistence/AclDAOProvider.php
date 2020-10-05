<?php
namespace Ubiquity\security\acl\persistence;

use Ubiquity\orm\DAO;
use Ubiquity\security\acl\models\AclElement;
use Ubiquity\security\acl\models\Permission;
use Ubiquity\security\acl\models\Resource;
use Ubiquity\security\acl\models\Role;

/**
 * Load and save Acls with a database using DAO.
 * Ubiquity\security\acl\persistence$AclDAOProvider
 * This class is part of Ubiquity
 *
 * @author jc
 * @version 1.0.0
 *
 */
class AclDAOProvider implements AclProviderInterface {

	protected $aclClass;

	protected $RoleClass;

	protected $permissionClass;

	protected $resourceClass;

	/**
	 *
	 * @param array $classes
	 *        	associative array['acl'=>'','role'=>'','resource'=>'','permission'=>'']
	 */
	public function __construct($classes = []) {
		$this->aclClass = $classes['acl'] ?? AclElement::class;
		$this->RoleClass = $classes['role'] ?? Role::class;
		$this->resourceClass = $classes['resource'] ?? Resource::class;
		$this->permissionClass = $classes['permission'] ?? Permission::class;
	}

	public function setDbOffset($dbOffset = 'default') {
		DAO::setModelDatabase($this->aclClass, $dbOffset);
		DAO::setModelDatabase($this->resourceClass, $dbOffset);
		DAO::setModelDatabase($this->RoleClass, $dbOffset);
		DAO::setModelDatabase($this->permissionClass, $dbOffset);
	}

	/**
	 *
	 * {@inheritdoc}
	 * @see \Ubiquity\security\acl\persistence\AclProviderInterface::loadAllAcls()
	 */
	public function loadAllAcls(): array {
		return DAO::getAll($this->aclClass);
	}

	/**
	 *
	 * {@inheritdoc}
	 * @see \Ubiquity\security\acl\persistence\AclProviderInterface::saveAcl()
	 */
	public function saveAcl(AclElement $aclElement) {
		return DAO::save($aclElement);
	}

	protected function loadElements(string $className): array {
		$elements = DAO::getAll($className);
		$result = [];
		foreach ($elements as $elm) {
			$result[$elm->getName()] = $elm;
		}
		return $result;
	}

	/**
	 *
	 * {@inheritdoc}
	 * @see \Ubiquity\security\acl\persistence\AclProviderInterface::loadAllPermissions()
	 */
	public function loadAllPermissions(): array {
		return $this->loadElements($this->permissionClass);
	}

	/**
	 *
	 * {@inheritdoc}
	 * @see \Ubiquity\security\acl\persistence\AclProviderInterface::loadAllResources()
	 */
	public function loadAllResources(): array {
		return $this->loadElements($this->resourceClass);
	}

	/**
	 *
	 * {@inheritdoc}
	 * @see \Ubiquity\security\acl\persistence\AclProviderInterface::loadAllRoles()
	 */
	public function loadAllRoles(): array {
		return $this->loadElements($this->RoleClass);
	}

	/**
	 *
	 * {@inheritdoc}
	 * @see \Ubiquity\security\acl\persistence\AclProviderInterface::savePart()
	 */
	public function savePart(\Ubiquity\security\acl\models\AbstractAclPart $part) {
		return DAO::save($part);
	}
}

