<?php
namespace controllers;

use Ubiquity\controllers\Controller;
use Ubiquity\security\acl\controllers\AclControllerTrait;

/**
 *
 * @resource("Home")
 */
class TestController extends Controller {
	use AclControllerTrait;

	/**
	 *
	 * @resource('IndexResource')
	 */
	public function index() {
		echo 'index';
	}

	/**
	 *
	 * @allow('role'=>'@ALL','permission'=>'ALLOW')
	 */
	public function allowExisting() {
		echo 'allowExisting';
	}

	/**
	 *
	 * @allow('role'=>'@OTHER','resource'=>'Other','permission'=>'ALLOW_OTHER')
	 */
	public function allowOther() {
		echo 'allowOther';
	}

	/**
	 *
	 * @permission('NEW_PERMISSION')
	 */
	public function newPermission() {
		echo 'newPermission';
	}

	/**
	 *
	 * @permission('READ')
	 */
	public function existingPerm() {
		echo 'existingPerm';
	}

	/**
	 *
	 * @allow("@OTHER")
	 */
	public function allowOther2() {
		echo 'allowOther2';
	}

	/**
	 *
	 * @permission("name"=>"ADMIN","level"=>100)
	 */
	public function otherPermission() {
		echo 'otherPermission';
	}

	public function _getRole() {
		return $_GET['role'] ?? 'nobody';
	}

	/**
	 *
	 * {@inheritdoc}
	 * @see \Ubiquity\controllers\Controller::onInvalidControl()
	 */
	public function onInvalidControl() {
		echo $this->_getRole() . ' is not allowed!';
	}

	public function added() {
		echo "added!";
	}
}

