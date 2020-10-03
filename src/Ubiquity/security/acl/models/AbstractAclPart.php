<?php
namespace Ubiquity\security\acl\models;

/**
 * Ubiquity\security\acl\models$AbastractAclElement
 * This class is part of Ubiquity
 *
 * @author jc
 * @version 1.0.0
 *
 */
abstract class AbstractAclPart {

	protected $name;

	/**
	 *
	 * @return mixed
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 *
	 * @param mixed $name
	 */
	public function setName($name) {
		$this->name = $name;
	}
}

