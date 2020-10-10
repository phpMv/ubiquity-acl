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

	public function __construct(?string $name = null, $parents = '') {
		parent::__construct($name);
		$this->setParents($parents);
	}

	/**
	 *
	 * @var string
	 */
	protected $parents = '';

	/**
	 *
	 * @return string
	 */
	public function getParents() {
		return $this->parents;
	}

	/**
	 *
	 * @return array
	 */
	public function getParentsArray() {
		return ($this->parents == null) ? [] : \explode(',', $this->parents);
	}

	/**
	 *
	 * @param array|string $parents
	 */
	public function setParents($parents) {
		if (\is_array($parents)) {
			$parents = \implode(',', $parents);
		}
		$this->parents = $parents;
	}
}

