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
}

