<?php
use Ubiquity\security\acl\AclManager;

/**
 * AclManager test case.
 */
class AclManagerTest extends \Codeception\Test\Unit {

	/**
	 *
	 * @var AclManager
	 */
	private $aclManager;

	protected function _before() {
		$this->aclManager = new AclManager();
	}

	/**
	 * Tests AclManager::start()
	 */
	public function testStart() {
		$this->aclManager->start();
		$this->assertFalse($this->aclManager->isAllowed('@ALL'));
	}

	/**
	 * Tests AclManager::initFromProviders()
	 */
	public function testInitFromProviders() {
		// TODO Auto-generated AclManagerTest::testInitFromProviders()
		$this->markTestIncomplete("initFromProviders test not implemented");

		AclManager::initFromProviders(/* parameters */);
	}

	/**
	 * Tests AclManager::addRole()
	 */
	public function testAddRole() {
		// TODO Auto-generated AclManagerTest::testAddRole()
		$this->markTestIncomplete("addRole test not implemented");

		AclManager::addRole(/* parameters */);
	}

	/**
	 * Tests AclManager::addResource()
	 */
	public function testAddResource() {
		// TODO Auto-generated AclManagerTest::testAddResource()
		$this->markTestIncomplete("addResource test not implemented");

		AclManager::addResource(/* parameters */);
	}

	/**
	 * Tests AclManager::addPermission()
	 */
	public function testAddPermission() {
		// TODO Auto-generated AclManagerTest::testAddPermission()
		$this->markTestIncomplete("addPermission test not implemented");

		AclManager::addPermission(/* parameters */);
	}

	/**
	 * Tests AclManager::allow()
	 */
	public function testAllow() {
		// TODO Auto-generated AclManagerTest::testAllow()
		$this->markTestIncomplete("allow test not implemented");

		AclManager::allow(/* parameters */);
	}

	/**
	 * Tests AclManager::isAllowed()
	 */
	public function testIsAllowed() {
		// TODO Auto-generated AclManagerTest::testIsAllowed()
		$this->markTestIncomplete("isAllowed test not implemented");

		AclManager::isAllowed(/* parameters */);
	}
}

