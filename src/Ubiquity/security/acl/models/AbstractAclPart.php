<?php

namespace Ubiquity\security\acl\models;

/**
 * Ubiquity\security\acl\models$AbastractAclElement
 * This class is part of Ubiquity
 *
 * @author jc
 * @version 1.0.1
 *
 */
abstract class AbstractAclPart {

	/**
	 *
	 * @id
	 * @column("name"=>"id","nullable"=>false,"dbType"=>"int(11)")
	 */
	#[\Ubiquity\attributes\items\Id()]
	#[\Ubiquity\attributes\items\Column(name:'id',nullable:false,dbType:'int(11)')]
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

	public function getId_() {
		return $this->name;
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

	public function castAs(string $class) {
		return unserialize(sprintf('O:%d:"%s"%s', \strlen($class), $class, \strstr(\strstr(\serialize($this), '"'), ':')));
	}
}

