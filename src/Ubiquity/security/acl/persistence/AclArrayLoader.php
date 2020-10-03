<?php
namespace Ubiquity\security\acl\persistence;

use Ubiquity\security\acl\models\AclElement;
use Ubiquity\security\acl\models\Permission;
use Ubiquity\security\acl\models\Resource;
use Ubiquity\security\acl\models\Role;

/**
 * Ubiquity\security\acl\persistence$AclArrayLoader
 * This class is part of Ubiquity
 *
 * @author jc
 * @version 1.0.0
 *
 */
abstract class AclArrayLoader implements AclLoaderInterface {

	protected $aclsArray;

	protected $parts;

	public function __construct() {}

	protected function loadAllPart($class): array {
		$elements = [];
		foreach ($this->parts[$class] as $partArray) {
			$elm = new $class();
			$elm->fromArray($partArray);
			$elements[] = $elm;
		}
		return $elements;
	}

	/**
	 *
	 * {@inheritdoc}
	 * @see \Ubiquity\security\acl\persistence\AclLoaderInterface::loadAllAcls()
	 */
	public function loadAllAcls() {
		return DAO::getAll(AclElement::class);
	}

	/**
	 *
	 * {@inheritdoc}
	 * @see \Ubiquity\security\acl\persistence\AclLoaderInterface::saveAcl()
	 */
	public function saveAcl(AclElement $aclElement) {
		$this->aclsArray[] = [
			'resource' => $aclElement->getResource(),
			'role' => $aclElement->getRole(),
			'permission' => $aclElement->getPermission()
		];
	}

	/**
	 *
	 * {@inheritdoc}
	 * @see \Ubiquity\security\acl\persistence\AclLoaderInterface::loadAllPermissions()
	 */
	public function loadAllPermissions(): array {
		return $this->loadAllPart(Permission::class);
	}

	/**
	 *
	 * {@inheritdoc}
	 * @see \Ubiquity\security\acl\persistence\AclLoaderInterface::loadAllResources()
	 */
	public function loadAllResources(): array {
		return $this->loadAllPart(Resource::class);
	}

	/**
	 *
	 * {@inheritdoc}
	 * @see \Ubiquity\security\acl\persistence\AclLoaderInterface::loadAllRoles()
	 */
	public function loadAllRoles(): array {
		return $this->loadAllPart(Role::class);
	}

	/**
	 *
	 * {@inheritdoc}
	 * @see \Ubiquity\security\acl\persistence\AclLoaderInterface::savePart()
	 */
	public function savePart(\Ubiquity\security\acl\models\AbstractAclPart $part) {
		$class = \get_class($part);
		$this->parts[$class][] = $part->toArray();
	}
}

