<?php
namespace Ubiquity\annotations\acl;

use Ubiquity\annotations\BaseAnnotation;

/**
 * Annotation Allow.
 * usages :
 * - allow("roleName")
 * - allow("role"=>"roleName")
 * - allow("role"=>"roleName","resource"=>"resourceName","permission"=>"permissionName")
 * - allow("roleName","resourceName","permissionName")
 *
 * @author jc
 * @version 1.0.0
 * @usage('method'=>true,'class'=>true,'inherited'=>true,'multiple'=>true)
 */
class AllowAnnotation extends BaseAnnotation {

	public $role;

	public $permission;

	public $resource;

	/**
	 * Initialize the annotation.
	 */
	public function initAnnotation(array $properties) {
		if (isset($properties[0])) {
			$this->role = $properties[0];
			unset($properties[0]);
			if (isset($properties[1])) {
				$this->resource = $properties[1];
				unset($properties[1]);
				if (isset($properties[2])) {
					$this->permission = $properties[2];
					unset($properties[2]);
				}
			}
		} else if (isset($properties['role'])) {
			$this->role = $properties['role'];
			if (isset($properties['resource'])) {
				$this->resource = $properties['resource'];
			}
			if (isset($properties['permission'])) {
				$this->permission = $properties['permission'];
			}
		} else {
			throw new \Exception('Allow annotation must have a role');
		}
	}
}
