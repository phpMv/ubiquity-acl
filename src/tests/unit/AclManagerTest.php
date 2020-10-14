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
		$this->assertEquals(1, count($this->aclManager->getRoles()));
		$this->expectException(AclException::class);
		$this->aclManager->isAllowed('newRole');

		$this->aclManager->addRole('newRole');
		$this->assertEquals(2, count($this->aclManager->getRoles()));
		$this->assertFalse($this->aclManager->isAllowed('newRole'));
	}

	/**
	 * Tests AclManager::addResource()
	 */
	public function testAddResource() {
		$this->aclManager->start();
		$this->assertEquals(1, count($this->aclManager->getResources()));
		$this->aclManager->addResource('newResource');
		$this->assertEquals(2, count($this->aclManager->getResources()));
		$this->assertFalse($this->aclManager->isAllowed('@ALL', 'newResource'));
	}

	/**
	 * Tests AclManager::addPermission()
	 */
	public function testAddPermission() {
		$this->aclManager->start();
		$this->aclManager->allow('@ALL');
		$this->assertEquals(1, count($this->aclManager->getPermissions()));

		$this->expectException(AclException::class);
		$this->aclManager->isAllowed('@ALL', '*', 'READ');

		$this->aclManager->addPermission('READ');
		$this->assertTrue($this->aclManager->isAllowed('@ALL', '*', 'READ'));
		$this->assertEquals(2, count($this->aclManager->getPermissions()));
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

		$this->assertEquals(0, count($this->aclManager->getAcls()));

		$this->aclManager->allow('user', 'newResource', 'READ');

		$this->assertEquals(1, count($this->aclManager->getAcls()));

		$this->assertFalse($this->aclManager->isAllowed('user', '*', 'READ'));
		$this->assertTrue($this->aclManager->isAllowed('user', 'newResource', 'READ'));
		$this->assertFalse($this->aclManager->isAllowed('user', 'newResource'));
	}

	/**
	 * Tests AclManager::addAndAllow()
	 */
	public function testAddAndAllow() {
		$this->aclManager->start();
		$this->aclManager->addAndAllow('user', 'newResource', 'READ');
		$this->assertTrue($this->aclManager->isAllowed('user', 'newResource', 'READ'));
		$this->assertEquals(2, count($this->aclManager->getRoles()));
		$this->assertEquals(2, count($this->aclManager->getRoles()));
		$this->assertEquals(2, count($this->aclManager->getPermissions()));
	}

	/**
	 * Tests AclManager::addRoles()
	 */
	public function testAddRoles() {
		$this->aclManager->start();
		$this->aclManager->addRoles([
			'user' => [],
			'admin' => [
				'user'
			]
		]);
		$this->aclManager->addPermission('READ');
		$this->aclManager->allow('user', '*', 'READ');
		$this->assertTrue($this->aclManager->isAllowed('user', '*', 'READ'));
		$this->assertTrue($this->aclManager->isAllowed('admin', '*', 'READ'));
		$this->assertEquals(3, count($this->aclManager->getRoles()));
		$this->assertEquals(2, count($this->aclManager->getPermissions()));
	}

	/**
	 * Tests AclManager::addResources()
	 */
	public function testAddResources() {
		$this->aclManager->start();
		$this->aclManager->addResources([
			'Home' => '/home',
			'Admin' => '/admin'
		]);
		$this->aclManager->addPermission('READ');
		$this->aclManager->allow('@ALL', 'Home', 'READ');
		$this->assertTrue($this->aclManager->isAllowed('@ALL', 'Home', 'READ'));
		$this->assertFalse($this->aclManager->isAllowed('@ALL', 'Admin', 'READ'));
		$this->assertEquals(3, count($this->aclManager->getResources()));
		$this->assertEquals(2, count($this->aclManager->getPermissions()));
	}

	/**
	 * Tests AclManager::addPermissions()
	 */
	public function testAddPermissions() {
		$this->aclManager->start();
		$this->aclManager->addPermissions([
			'READ' => 1,
			'WRITE' => 2
		]);
		$this->aclManager->addRole('user');
		$this->aclManager->addResource('Home');
		$this->aclManager->allow('user', 'Home', 'READ');
		$this->assertTrue($this->aclManager->isAllowed('user', 'Home', 'READ'));
		$this->assertFalse($this->aclManager->isAllowed('user', 'Home', 'WRITE'));
		$this->assertEquals(3, count($this->aclManager->getPermissions()));
		$this->assertEquals(2, count($this->aclManager->getRoles()));
		$this->assertEquals(2, count($this->aclManager->getResources()));
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
		$this->assertTrue($this->aclManager->isAllowed('userPlus', 'newResource'));
		$this->aclManager->addRole('admin');
		$this->assertTrue($this->aclManager->isAllowed('admin', 'newResource'));
	}

	/**
	 * Tests AclManager::setPermissionLevel()
	 */
	public function testSetPermissionLevel() {
		$this->aclManager->start();
		$this->aclManager->addPermission('READ', 5);
		$this->aclManager->addPermission('DELETE', 15);
		$this->aclManager->addResource('newResource');
		$this->aclManager->addRole('user');

		$this->assertFalse($this->aclManager->isAllowed('user', '*', 'READ'));
		$this->assertFalse($this->aclManager->isAllowed('user', 'newResource', 'READ'));

		$this->aclManager->allow('user', 'newResource', 'READ');

		$this->assertFalse($this->aclManager->isAllowed('user', '*', 'READ'));
		$this->assertTrue($this->aclManager->isAllowed('user', 'newResource', 'READ'));
		$this->assertFalse($this->aclManager->isAllowed('user', 'newResource', 'DELETE'));

		$this->expectException(AclException::class);
		$this->aclManager->setPermissionLevel('NOT_EXIST', 10);

		$this->aclManager->setPermissionLevel('DELETE', 4);
		$this->assertTrue($this->aclManager->isAllowed('user', 'newResource', 'DELETE'));
	}
}

