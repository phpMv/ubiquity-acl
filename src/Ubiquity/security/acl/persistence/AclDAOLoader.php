<?php
namespace Ubiquity\security\acl\persistence;

use Ubiquity\orm\DAO;
use Ubiquity\security\acl\models\AclElement;
use Ubiquity\security\acl\models\Permission;
use Ubiquity\security\acl\models\Resource;
use Ubiquity\security\acl\models\Role;

/**
 * Ubiquity\security\acl\persistence$AclDAOLoader
 * This class is part of Ubiquity
 *
 * @author jc
 * @version 1.0.0
 *
 */
class AclDAOLoader implements AclLoaderInterface {

	public function __construct($dbOffset = 'default') {
		DAO::setModelDatabase(AclElement::class, $dbOffset);
		DAO::setModelDatabase(Resource::class, $dbOffset);
		DAO::setModelDatabase(Role::class, $dbOffset);
		DAO::setModelDatabase(Permission::class, $dbOffset);
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
		return DAO::save($aclElement);
	}

	/**
	 *
	 * {@inheritdoc}
	 * @see \Ubiquity\security\acl\persistence\AclLoaderInterface::loadAllPermissions()
	 */
	public function loadAllPermissions(): array {
		return DAO::getAll(Permission::class);
	}

	/**
	 *
	 * {@inheritdoc}
	 * @see \Ubiquity\security\acl\persistence\AclLoaderInterface::loadAllResources()
	 */
	public function loadAllResources(): array {
		return DAO::getAll(Resource::class);
	}

	/**
	 *
	 * {@inheritdoc}
	 * @see \Ubiquity\security\acl\persistence\AclLoaderInterface::loadAllRoles()
	 */
	public function loadAllRoles(): array {
		return DAO::getAll(Role::class);
	}

	/**
	 *
	 * {@inheritdoc}
	 * @see \Ubiquity\security\acl\persistence\AclLoaderInterface::savePart()
	 */
	public function savePart(\Ubiquity\security\acl\models\AbstractAclPart $part) {
		return DAO::save($part);
	}
}

