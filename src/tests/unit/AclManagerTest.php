<?php
use Ubiquity\security\acl\AclManager;
use Ubiquity\exceptions\AclException;

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
		$this->aclManager->start();
		$this->aclManager->initFromProviders([]);
		$this->assertFalse($this->aclManager->isAllowed('@ALL'));
	}

	/**
	 * Tests AclManager::addRole()
	 */
	public function testAddRole() {
		$this->aclManager->start();
		$this->expectException(AclException::class);
		$this->aclManager->isAllowed('newRole');

		$this->aclManager->addRole('newRole');
		$this->assertFalse($this->aclManager->isAllowed('newRole'));
	}

	/**
	 * Tests AclManager::addResource()
	 */
	public function testAddResource() {
		$this->aclManager->start();
		$this->aclManager->addResource('newResource');
		$this->assertFalse($this->aclManager->isAllowed('@ALL', 'newResource'));
	}

	/**
	 * Tests AclManager::addPermission()
	 */
	public function testAddPermission() {
		$this->aclManager->start();
		$this->aclManager->allow('@ALL');
		$this->expectException(AclException::class);
		$this->aclManager->isAllowed('@ALL', '*', 'READ');

		$this->aclManager->addPermission('READ');
		$this->assertTrue($this->aclManager->isAllowed('@ALL', '*', 'READ'));
	}

	/**
	 * Tests AclManager::allow()
	 */
	public function testAllow() {
		$this->aclManager->start();
		$this->aclManager->addPermission('READ');
		$this->aclManager->addResource('newResource');
		$this->aclManager->addRole('user');
		$this->assertFalse($this->aclManager->isAllowed('@ALL', '*', 'READ'));
		$this->assertFalse($this->aclManager->isAllowed('@ALL', 'newResource', 'READ'));

		$this->assertFalse($this->aclManager->isAllowed('user', '*', 'READ'));
		$this->assertFalse($this->aclManager->isAllowed('user', 'newResource', 'READ'));

		$this->aclManager->allow('user', 'newResource', 'READ');

		$this->assertFalse($this->aclManager->isAllowed('user', '*', 'READ'));
		$this->assertTrue($this->aclManager->isAllowed('user', 'newResource', 'READ'));
		$this->assertFalse($this->aclManager->isAllowed('user', 'newResource'));
	}

	/**
	 * Tests AclManager::isAllowed()
	 */
	public function testIsAllowed() {
		$this->aclManager->start();
		$this->aclManager->addPermission('READ');
		$this->aclManager->addResource('newResource');
		$this->aclManager->addRole('user');
		$this->assertFalse($this->aclManager->isAllowed('@ALL', '*', 'READ'));
		$this->assertFalse($this->aclManager->isAllowed('@ALL', 'newResource', 'READ'));

		$this->assertFalse($this->aclManager->isAllowed('user', '*', 'READ'));
		$this->assertFalse($this->aclManager->isAllowed('user', 'newResource', 'READ'));

		$this->aclManager->allow('user', 'newResource', 'READ');

		$this->assertFalse($this->aclManager->isAllowed('user', '*', 'READ'));
		$this->assertTrue($this->aclManager->isAllowed('user', 'newResource', 'READ'));
		$this->assertFalse($this->aclManager->isAllowed('user', 'newResource'));

		$this->aclManager->addRole('userPlus', [
			'user'
		]);

		$this->assertFalse($this->aclManager->isAllowed('userPlus', '*', 'READ'));
		$this->assertTrue($this->aclManager->isAllowed('userPlus', 'newResource', 'READ'));
		$this->assertFalse($this->aclManager->isAllowed('userPlus', 'newResource'));

		$this->aclManager->allow('@ALL', 'newResource');
		$this->assertTrue($this->aclManager->isAllowed('user', 'newResource'));
		$this->aclManager->addRole('admin');
		$this->assertTrue($this->aclManager->isAllowed('admin', 'newResource'));
	}
}

