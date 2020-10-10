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
	 * @see \Ubiquity\security\acl\persistence\AclProviderInterface::loadAllAcls()
	 */
	public function loadAllAcls(): array {
		$acls = [];
		foreach ($this->aclsArray as $aclArray) {
			$aclElement = new AclElement();
			$aclElement->fromArray($aclArray);
			$acls[$aclElement->getId_()] = $aclElement;
		}
		return $acls;
	}

	protected function _saveAcl(AclElement $aclElement) {
		$this->aclsArray[$aclElement->getId_()] = $aclElement->toArray();
	}

	/**
	 *
	 * {@inheritdoc}
	 * @see \Ubiquity\security\acl\persistence\AclProviderInterface::loadAllPermissions()
	 */
	public function loadAllPermissions(): array {
		return $this->loadAllPart(Permission::class);
	}

	/**
	 *
	 * {@inheritdoc}
	 * @see \Ubiquity\security\acl\persistence\AclProviderInterface::loadAllResources()
	 */
	public function loadAllResources(): array {
		return $this->loadAllPart(Resource::class);
	}

	/**
	 *
	 * {@inheritdoc}
	 * @see \Ubiquity\security\acl\persistence\AclProviderInterface::loadAllRoles()
	 */
	public function loadAllRoles(): array {
		return $this->loadAllPart(Role::class);
	}

	protected function _savePart(\Ubiquity\security\acl\models\AbstractAclPart $part) {
		$class = \get_class($part);
		$this->parts[$class][$part->getName()] = $part->toArray();
	}
}

