<?php
namespace Ubiquity\annotations\acl;

use Ubiquity\annotations\BaseAnnotation;

/**
 * Annotation Permission.
 * usages :
 * - permission("permissionName")
 * - permission("permissionName","permissionLevel")
 * - permission("name"=>"permissionName")
 * - permission("name"=>"permissionName","level"=>"permissionLevel")
 *
 * @author jc
 * @version 1.0.0
 * @usage('method'=>true,'class'=>true,'inherited'=>true,'multiple'=>false)
 */
class PermissionAnnotation extends BaseAnnotation {

	public $name;

	public $level;

	/**
	 * Initialize the annotation.
	 */
	public function initAnnotation(array $properties) {
		if (isset($properties[0])) {
			$this->name = $properties[0];
			unset($properties[0]);
			if (isset($properties[1])) {
				$this->level = $properties[1];
				unset($properties[1]);
			}
		} else if (isset($properties['name'])) {
			$this->name = $properties['name'];
			if (isset($properties['level'])) {
				$this->level = $properties['level'];
			}
		} else {
			throw new \Exception('Permission annotation must have a name');
		}
	}
}
