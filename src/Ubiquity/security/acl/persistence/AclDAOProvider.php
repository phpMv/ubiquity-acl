<?php
namespace Ubiquity\security\acl\persistence;

use Ubiquity\orm\DAO;
use Ubiquity\security\acl\models\AbstractAclPart;
use Ubiquity\security\acl\models\AclElement;
use Ubiquity\security\acl\models\Permission;
use Ubiquity\security\acl\models\Resource;
use Ubiquity\security\acl\models\Role;
use Ubiquity\orm\OrmUtils;

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

	protected $roleClass;

	protected $permissionClass;

	protected $resourceClass;

	/**
	 *
	 * @param array $classes
	 *        	associative array['acl'=>'','role'=>'','resource'=>'','permission'=>'']
	 */
	public function __construct($classes = []) {
		$this->aclClass = $classes['acl'] ?? AclElement::class;
		$this->roleClass = $classes['role'] ?? Role::class;
		$this->resourceClass = $classes['resource'] ?? Resource::class;
		$this->permissionClass = $classes['permission'] ?? Permission::class;
	}

	public function setDbOffset($dbOffset = 'default') {
		DAO::setModelDatabase($this->aclClass, $dbOffset);
		DAO::setModelDatabase($this->resourceClass, $dbOffset);
		DAO::setModelDatabase($this->roleClass, $dbOffset);
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
		$object = $this->castElement($aclElement);
		$res = DAO::save($object);
		if ($res) {
			$aclElement->setId($object->getId());
		}
		return $res;
	}

	/**
	 *
	 * {@inheritdoc}
	 * @see \Ubiquity\security\acl\persistence\AclProviderInterface::removeAcl()
	 */
	public function removeAcl(AclElement $aclElement) {
		return DAO::remove($aclElement);
	}

	protected function loadElements(string $className): array {
		$elements = DAO::getAll($className);
		$result = [];
		foreach ($elements as $elm) {
			$result[$elm->getName()] = $elm;
		}
		return $result;
	}

	protected function castElement($part) {
		$class = $this->getModelClasses()[get_class($part)] ?? get_class($part);
		return $part->castAs($class);
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
		return $this->loadElements($this->roleClass);
	}

	/**
	 *
	 * {@inheritdoc}
	 * @see \Ubiquity\security\acl\persistence\AclProviderInterface::savePart()
	 */
	public function savePart(\Ubiquity\security\acl\models\AbstractAclPart $part) {
		$object = $this->castElement($part);
		$res = DAO::insert($object);
		if ($res) {
			$part->setId($object->getId());
		}
		return $res;
	}

	/**
	 *
	 * {@inheritdoc}
	 * @see \Ubiquity\security\acl\persistence\AclProviderInterface::updatePart()
	 */
	public function updatePart(\Ubiquity\security\acl\models\AbstractAclPart $part) {
		return DAO::update($this->castElement($part));
	}

	/**
	 *
	 * {@inheritdoc}
	 * @see \Ubiquity\security\acl\persistence\AclProviderInterface::removePart()
	 */
	public function removePart(\Ubiquity\security\acl\models\AbstractAclPart $part) {
		return DAO::remove($this->castElement($part));
	}

	public function isAutosave(): bool {
		return true;
	}

	public function saveAll(): void {}

	public function existPart(AbstractAclPart $part): bool {
		$elm = $this->castElement($part);
		return DAO::exists(\get_class($elm), 'id= ?', [
			$elm->getId()
		]);
	}

	public function existAcl(AclElement $aclElement): bool {
		$elm = $this->castElement($aclElement);
		return DAO::exists(\get_class($aclElement), 'id= ?', [
			$elm->getId()
		]);
	}

	public function getDetails(): array {
		return [
			'user' => $this->roleClass,
			'archive' => $this->resourceClass,
			'unlock alternate' => $this->permissionClass,
			'lock' => $this->aclClass
		];
	}

	public function getModelClassesSwap(): array {
		$swap = $this->getModelClasses();
		$classes = \array_values($swap);
		$result = [];
		foreach ($classes as $class) {
			$result[$class] = $swap;
		}
		return $result;
	}

	public function getModelClasses(): array {
		return [
			AclElement::class => $this->aclClass,
			Role::class => $this->roleClass,
			Resource::class => $this->resourceClass,
			Permission::class => $this->permissionClass
		];
	}

	public function clearAll(): void {}
}

