<?php
namespace Ubiquity\annotations\acl;

use Ubiquity\annotations\BaseAnnotation;

/**
 * Annotation Resource.
 * usages :
 * - resource("resourceName")
 * - resource("name"=>"resourceName")
 *
 * @author jc
 * @version 1.0.0
 * @usage('method'=>true,'class'=>true,'inherited'=>true,'multiple'=>false)
 */
class ResourceAnnotation extends BaseAnnotation {

	public $name;

	/**
	 * Initialize the annotation.
	 */
	public function initAnnotation(array $properties) {
		if (isset($properties[0])) {
			$this->name = $properties[0];
			unset($properties[0]);
		} else if (isset($properties['name'])) {
			$this->name = $properties['name'];
		} else {
			throw new \Exception('Resource annotation must have a name');
		}
	}
}
