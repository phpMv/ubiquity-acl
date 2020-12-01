<?php
namespace Ubiquity\security\acl\persistence;

use Ubiquity\security\acl\models\AclElement;
use Ubiquity\security\acl\models\Permission;
use Ubiquity\security\acl\models\Resource;
use Ubiquity\security\acl\models\Role;
use Ubiquity\exceptions\AclException;
use Ubiquity\security\acl\models\AbstractAclPart;

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
		$part = $this->parts[$class] ?? [];
		foreach ($part as $partArray) {
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

	public function saveAcl(AclElement $aclElement) {
		$this->aclsArray[$aclElement->getId_()] = $aclElement->toArray();
	}

	public function removeAcl(AclElement $aclElement) {
		unset($this->aclsArray[$aclElement->getId_()]);
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

	public function savePart(\Ubiquity\security\acl\models\AbstractAclPart $part) {
		$class = \get_class($part);
		$this->parts[$class][$part->getName()] = $part->toArray();
	}

	public function updatePart(\Ubiquity\security\acl\models\AbstractAclPart $part) {
		$class = \get_class($part);
		$this->parts[$class][$part->getName()] = $part->toArray();
	}

	public function removePart(\Ubiquity\security\acl\models\AbstractAclPart $part) {
		$name = $part->getName();
		if ($part instanceof Resource) {
			$field = 'resource';
		} elseif ($part instanceof Role) {
			$field = 'role';
		} else {
			$field = 'permission';
		}
		foreach ($this->aclsArray as $acl) {
			if ($acl[$field]['name'] === $name) {
				throw new AclException("$name is in use in ACLs and can't be removed!");
			}
		}
		unset($this->parts[\get_class($part)][$name]);
	}

	public function existPart(AbstractAclPart $part): bool {
		$name = $part->getName();
		return isset($this->parts[\get_class($part)][$name]);
	}

	public function existAcl(AclElement $aclElement): bool {
		return isset($this->aclsArray[$aclElement->getId_()]);
	}

	public function getModelClassesSwap(): array {
		return [];
	}

	/**
	 *
	 * {@inheritdoc}
	 * @see \Ubiquity\security\acl\persistence\AclProviderInterface::clearAll()
	 */
	public function clearAll(): void {
		$this->parts = [];
		$this->aclsArray = [];
	}
}

