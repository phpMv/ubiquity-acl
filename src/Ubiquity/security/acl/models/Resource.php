<?php
namespace Ubiquity\security\acl\models;

/**
 * Ubiquity\security\acl\models$Resource
 * This class is part of Ubiquity
 *
 * @author jc
 * @version 1.0.0
 *
 */
class Resource extends AbstractAclPart {

	protected $value;

	/**
	 *
	 * @return mixed
	 */
	public function getValue() {
		return $this->value;
	}

	/**
	 *
	 * @param mixed $value
	 */
	public function setValue($value) {
		$this->value = $value;
	}
}

