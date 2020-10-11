<?php
use Ubiquity\security\acl\persistence\AclDAOProvider;
use Ubiquity\cache\CacheManager;
use Ubiquity\security\acl\AclManager;
use Ubiquity\controllers\Startup;
use Ubiquity\orm\DAO;

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
				"acls" => [
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
		DAO::setModelsDatabases([
			\models\acls\Aclelement::class => 'acls',
			\models\acls\Role::class => 'acls',
			\models\acls\Resource::class => 'acls',
			\models\acls\Permission::class => 'acls'
		]);
		AclManager::start();
		AclManager::initFromProviders([
			new AclDAOProvider([
				'acl' => \models\acls\Aclelement::class,
				'role' => \models\acls\Role::class,
				'resource' => \models\acls\Resource::class,
				'permission' => \models\acls\Permission::class
			])
		]);
	}

	/**
	 * Tests AclCacheProvider->loadAllAcls()
	 */
	public function testLoadAllAcls() {
		$this->assertEquals(1, \count(AclManager::getAcls()));
		$this->assertEquals(3, count(AclManager::getRoles()));
		$this->assertEquals(4, count(AclManager::getPermissions()));
		$this->assertEquals(3, count(AclManager::getResources()));
		$this->assertTrue(AclManager::isAllowed('USER', 'Home', 'READ'));
		$this->assertFalse(AclManager::isAllowed('USER', 'Home', 'WRITE'));
		AclManager::allow('USER', 'Home', 'WRITE');
		$this->assertEquals(2, \count(AclManager::getAcls()));
		$this->assertTrue(AclManager::isAllowed('USER', 'Home', 'WRITE'));
	}

	/**
	 * Tests AclCacheProvider->savePart()
	 */
	public function testSavePart() {
		AclManager::addRole('TESTER', [
			'USER'
		]);
		$this->assertTrue(AclManager::isAllowed('TESTER', 'Home', 'READ'));
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
		AclManager::addPermission('DELETE', 5);
		AclManager::start();
		AclManager::initFromProviders([
			new AclDAOProvider()
		]);
		$this->assertEquals(4, count(AclManager::getPermissions()));
		$this->assertFalse(AclManager::isAllowed('USER', 'Home', 'DELETE'));
		AclManager::setPermissionLevel('DELETE', 0);
		$this->assertTrue(AclManager::isAllowed('USER', 'Home', 'DELETE'));
	}
}

