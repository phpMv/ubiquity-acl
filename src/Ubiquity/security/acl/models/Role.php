<?php
namespace Ubiquity\security\acl\models;

/**
 * Ubiquity\security\acl\models$Role
 * This class is part of Ubiquity
 *
 * @author jc
 * @version 1.0.0
 *
 */
class Role extends AbstractAclPart {

	public function __construct(?string $name = null, ?array $parents = []) {
		parent::__construct($name);
		$this->parents = $parents;
	}

	/**
	 *
	 * @var array
	 */
	protected $parents = [];

	/**
	 *
	 * @return array
	 */
	public function getParents() {
		return $this->parents;
	}

	/**
	 *
	 * @param array $parents
	 */
	public function setParents(array $parents) {
		$this->parents = $parents;
	}
}

