<?php
namespace Ubiquity\security\acl\persistence;

use Ubiquity\security\acl\models\AclElement;
use Ubiquity\security\acl\models\AbstractAclPart;
use Ubiquity\security\acl\models\Role;
use Ubiquity\security\acl\models\Permission;
use Ubiquity\security\acl\models\Resource;

/**
 * Ubiquity\security\acl\persistence$AclProviderInterface
 * This class is part of Ubiquity
 *
 * @author jc
 * @version 1.0.0
 *
 */
interface AclProviderInterface {

	/**
	 *
	 * @return AclElement[]
	 */
	public function loadAllAcls(): array;

	/**
	 *
	 * @return Role[]
	 */
	public function loadAllRoles(): array;

	/**
	 *
	 * @return Resource[]
	 */
	public function loadAllResources(): array;

	/**
	 *
	 * @return Permission[]
	 */
	public function loadAllPermissions(): array;

	public function saveAcl(AclElement $aclElement);

	public function savePart(AbstractAclPart $part);
}

