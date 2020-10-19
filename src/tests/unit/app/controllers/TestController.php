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
	 * @allow('@ALL')
	 */
	public function allowExisting() {}
}

