<?php

namespace Ubiquity\security\acl\models;

use Ubiquity\attributes\items\Transient;

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
	 * @column("name"=>"name","nullable"=>false,"dbType"=>"varchar(100)")
	 */
	#[\Ubiquity\attributes\items\Id()]
	#[\Ubiquity\attributes\items\Column(name:'name',nullable:false,dbType:'varchar(100)')]
	protected $name;

	/**
	 * @var string
	 * @transient
	 */
	#[Transient]
	protected $type;

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

	public function getId_() {
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

	public function __toString() {
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function getType(): string {
		return $this->type;
	}

	/**
	 * @param string $type
	 */
	public function setType(string $type): void {
		$this->type = $type;
	}

	public function castAs(string $class) {
		return unserialize(sprintf('O:%d:"%s"%s', \strlen($class), $class, \strstr(\strstr(\serialize($this), '"'), ':')));
	}
}

