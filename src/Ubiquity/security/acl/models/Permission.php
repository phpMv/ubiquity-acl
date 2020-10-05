<?php
namespace Ubiquity\security\acl\models;

/**
 * Ubiquity\security\acl\models$Permission
 * This class is part of Ubiquity
 *
 * @author jc
 * @version 1.0.0
 *
 */
class Permission extends AbstractAclPart {

	/**
	 *
	 * @var int
	 * @column("name"=>"level","dbType"=>"int(11)")
	 */
	protected $level;

	public function __construct(?string $name = null, int $level = 0) {
		parent::__construct($name);
		$this->level = $level;
	}

	/**
	 *
	 * @return int
	 */
	public function getLevel() {
		return $this->level;
	}

	/**
	 *
	 * @param int $level
	 */
	public function setLevel(int $level) {
		$this->level = $level;
	}
}

