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

	/**
	 *
	 * @id
	 * @column("name"=>"id","nullable"=>false,"dbType"=>"int(11)")
	 */
	protected $id;

	/**
	 *
	 * @var string
	 */
	protected $name;

	public function __construct(?string $name = null) {
		$this->name = $name;
	}

	/**
	 *
	 * @return mixed
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 *
	 * @param string $name
	 */
	public function setName($name) {
		$this->name = $name;
	}

	public function toArray(): array {
		return \get_object_vars($this);
	}

	public function fromArray(array $values) {
		foreach ($values as $k => $v) {
			$this->$k = $v;
		}
	}

	/**
	 *
	 * @return mixed
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 *
	 * @param mixed $id
	 */
	public function setId($id) {
		$this->id = $id;
	}

	public function __toString() {
		return $this->name;
	}
}

