<?php
namespace Ubiquity\security\acl\persistence;

use Ubiquity\security\acl\models\AclElement;
use Ubiquity\security\acl\models\Permission;
use Ubiquity\security\acl\models\Resource;
use Ubiquity\security\acl\models\Role;

/**
 * Ubiquity\security\acl\persistence$AclArrayProvider
 * This class is part of Ubiquity
 *
 * @author jc
 * @version 1.0.0
 *
 */
abstract class AclArrayProvider implements AclProviderInterface {

	protected $aclsArray;

	protected $parts;

	public function __construct() {}

	protected function loadAllPart($class): array {
		$elements = [];
		foreach ($this->parts[$class] as $partArray) {
			$elm = new $class();
			$elm->fromArray($partArray);
			$elements[$partArray['name']] = $elm;
		}
		return $elements;
	}

	/**
	 *
	 * {@inheritdoc}
	 * @see \Ubiquity\security\acl\persistence\AclLoaderInterface::loadAllAcls()
	 */
	public function loadAllAcls() {
		$acls = [];
		foreach ($this->aclsArray as $aclArray) {
			$aclElement = new AclElement();
			$aclElement->fromArray($aclArray);
			$acls[] = $aclElement;
		}
		return $acls;
	}

	/**
	 *
	 * {@inheritdoc}
	 * @see \Ubiquity\security\acl\persistence\AclLoaderInterface::saveAcl()
	 */
	public function saveAcl(AclElement $aclElement) {
		$this->aclsArray[] = $aclElement->toArray();
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

