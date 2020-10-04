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

	protected $level;

	public function __construct(?string $name = null, int $level = 0) {
		parent::__construct($name);
		$this->level = $level;
	}

	/**
	 *
	 * @return mixed
	 */
	public function getLevel() {
		return $this->level;
	}

	/**
	 *
	 * @param mixed $level
	 */
	public function setLevel($level) {
		$this->level = $level;
	}
}

