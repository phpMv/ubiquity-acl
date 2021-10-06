<?php
use Ubiquity\security\acl\persistence\AclDAOProvider;
use Ubiquity\cache\CacheManager;
use Ubiquity\security\acl\AclManager;
use Ubiquity\controllers\Startup;
use Ubiquity\exceptions\AclException;

/**
 * AclDAOProvider test case.
 */
class AclDAOProviderTest extends \Codeception\Test\Unit {

	protected function _before() {
		$config = [
			"cache" => [
				"directory" => "cache/",
				"system" => "Ubiquity\\cache\\system\\ArrayCache",
				"params" => []
			],
			'database' => [
				"default" => [
					"wrapper" => "Ubiquity\\db\\providers\\pdo\\PDOWrapper",
					"type" => "mysql",
					"dbName" => "acls",
					"serverName" => "127.0.0.1",
					"port" => 3306,
					"options" => array(),
					"user" => "root",
					"password" => "",
					"cache" => false
				]
			]
		];
		CacheManager::startProd($config);
		Startup::$config = $config;
		AclManager::start();
		$this->initProvider();
	}

	/**
	 * Tests AclCacheProvider->loadAllAcls()
	 */
	public function testLoadAllAcls() {
		$this->assertEquals(1, \count(AclManager::getAcls()));
		$this->assertEquals(3, count(AclManager::getRoles()));
		$this->assertEquals(3, count(AclManager::getPermissions()));
		$this->assertEquals(3, count(AclManager::getResources()));
		$this->assertTrue(AclManager::isAllowed('USER', 'Home', 'READ'));
		$this->assertInstanceOf(AclDAOProvider::class, AclManager::getProvider(AclDAOProvider::class));
		$this->assertTrue(AclManager::existPartIn(AclManager::getAclList()->getRoleByName('USER'), AclDAOProvider::class));
		$this->assertTrue(AclManager::existAclIn(current(AclManager::getAcls()), AclDAOProvider::class));
		$this->assertFalse(AclManager::isAllowed('USER', 'Home', 'WRITE'));
		AclManager::allow('USER', 'Home', 'WRITE');
		$this->assertEquals(2, \count(AclManager::getAcls()));
		$this->assertTrue(AclManager::isAllowed('USER', 'Home', 'WRITE'));

		AclManager::removeAcl('USER', 'Home', 'WRITE');
		$this->assertFalse(AclManager::isAllowed('USER', 'Home', 'WRITE'));
		$this->assertEquals(1, \count(AclManager::getAcls()));
	}

	/**
	 * Tests AclCacheProvider->savePart()
	 */
	public function testSavePart() {
		AclManager::addRole('TESTER', [
			'USER'
		]);
		$this->assertTrue(AclManager::isAllowed('TESTER', 'Home', 'READ'));

		AclManager::removeRole('TESTER');
		$this->expectException(AclException::class);
		AclManager::isAllowed('TESTER', 'Home', 'READ');
	}

	/**
	 * Tests AclCacheProvider->saveAcl()
	 */
	public function testSaveAcl() {
		// TODO Auto-generated AclCacheProviderTest->testSaveAcl()
		$this->markTestIncomplete("saveAcl test not implemented");

		$this->aclCacheProvider->saveAcl(/* parameters */);
	}

	/**
	 * Tests AclCacheProvider->saveAll()
	 */
	public function testSaveAll() {
		$this->assertEquals(3, count(AclManager::getPermissions()));
		AclManager::addPermission('DELETE', 5);
		AclManager::start();
		$this->initProvider();
		$this->assertEquals(4, count(AclManager::getPermissions()));
		$this->assertFalse(AclManager::isAllowed('USER', 'Home', 'DELETE'));
		AclManager::setPermissionLevel('DELETE', 0);
		$this->assertTrue(AclManager::isAllowed('USER', 'Home', 'DELETE'));
		AclManager::start();
		$this->initProvider();
		// $this->assertTrue(AclManager::isAllowed('USER', 'Home', 'DELETE'));

		AclManager::removePermission('DELETE');
		AclManager::saveAll();
		$this->expectException(AclException::class);
		AclManager::isAllowed('USER', 'Home', 'DELETE');
	}

	/*
	 * Tests AclManager::reloadFromSelectedProviders()
	 */
	public function testReloadFromSelectedProviders() {
		$this->assertEquals(1, \count(AclManager::getAcls()));
		$this->assertEquals(3, count(AclManager::getRoles()));
		$this->assertEquals(3, count(AclManager::getPermissions()));
		$this->assertEquals(3, count(AclManager::getResources()));

		AclManager::reloadFromSelectedProviders([]);
		$this->assertEquals(1, count(AclManager::getResources()));
		$this->assertEquals(1, count(AclManager::getRoles()));
		$this->assertEquals(1, count(AclManager::getPermissions()));
		$this->assertEquals(0, count(AclManager::getAcls()));
		$this->initProvider();
		AclManager::reloadFromSelectedProviders('*');
		$this->assertEquals(1, \count(AclManager::getAcls()));
		$this->assertEquals(3, count(AclManager::getRoles()));
		$this->assertEquals(3, count(AclManager::getPermissions()));
		$this->assertEquals(3, count(AclManager::getResources()));
		$this->initProvider();
		AclManager::reloadFromSelectedProviders([
			AclDAOProvider::class
		]);
		$this->assertEquals(1, \count(AclManager::getAcls()));
		$this->assertEquals(3, count(AclManager::getRoles()));
		$this->assertEquals(3, count(AclManager::getPermissions()));
		$this->assertEquals(3, count(AclManager::getResources()));

		$this->assertEqualsCanonicalizing([
			AclDAOProvider::class
		], AclManager::getAclList()->getProviderClasses());
	}

	protected function initProvider() {
		AclManager::initFromProviders([
			new AclDAOProvider(Startup::$config,[
				'acl' => \models\acls\Aclelement::class,
				'role' => \models\acls\Role::class,
				'resource' => \models\acls\Resource::class,
				'permission' => \models\acls\Permission::class
			])
		]);
	}
}

