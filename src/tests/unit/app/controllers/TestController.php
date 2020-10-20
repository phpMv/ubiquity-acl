<?php
namespace controllers;

use Ubiquity\controllers\Controller;

/**
 *
 * @resource("Home")
 */
class TestController extends Controller {

	/**
	 *
	 * @resource('IndexResource')
	 */
	public function index() {}

	/**
	 *
	 * @allow('role'=>'@ALL','permission'=>'ALLOW')
	 */
	public function allowExisting() {}

	/**
	 *
	 * @allow('role'=>'@OTHER','resource'=>'Other','permission'=>'ALLOW_OTHER')
	 */
	public function allowOther() {}

	/**
	 *
	 * @permission('NEW_PERMISSION')
	 */
	public function newPermission() {}

	/**
	 *
	 * @permission('READ')
	 */
	public function existingPerm() {}

	/**
	 *
	 * @allow("@OTHER")
	 */
	public function allowOther2() {}

	/**
	 *
	 * @permission("name"=>"ADMIN","level"=>100)
	 */
	public function otherPermission() {}
}

