<?php
use Ubiquity\cache\CacheManager;
use Ubiquity\security\acl\AclManager;
use Ubiquity\security\acl\persistence\AclCacheProvider;
use Ubiquity\exceptions\AclException;
use Ubiquity\controllers\Startup;
use controllers\TestController;

/**
 * AclCacheProvider test case.
 */
class AclCacheProviderTest extends \Codeception\Test\Unit {

	protected function _before() {
		$config = [
			"cache" => [
				"directory" => "cache/",
				"system" => "Ubiquity\\cache\\system\\ArrayCache",
				"params" => []
			]
		];
		CacheManager::startProd($config);
		AclManager::startWithCacheProvider();
	}

	/**
	 * Tests AclCacheProvider->loadAllAcls()
	 */
	public function testLoadAllAcls() {
		$this->assertEquals(3, \count(AclManager::getAcls()));
		$this->assertEquals(2, count(AclManager::getRoles()));
		$this->assertEquals(7, count(AclManager::getPermissions()));
		$this->assertEquals(4, count(AclManager::getResources()));
		$this->assertTrue(AclManager::isAllowed('@ALL', 'Home', 'ALLOW'));
		$this->assertFalse(AclManager::isAllowed('@ALL', 'Home', 'ADMIN'));
		AclManager::allow('@ALL', 'Home', 'ADMIN');
		$this->assertEquals(4, \count(AclManager::getAcls()));
		$this->assertTrue(AclManager::isAllowed('@ALL', 'Home', 'ADMIN'));

		AclManager::removeAcl('@ALL', 'Home', 'ADMIN');
		$this->assertFalse(AclManager::isAllowed('@ALL', 'Home', 'ADMIN'));
	}

	public function testExist() {
		$this->assertInstanceOf(AclCacheProvider::class, AclManager::getProvider(AclCacheProvider::class));
		$this->assertTrue(AclManager::existPartIn(AclManager::getAclList()->getRoleByName('@OTHER'), AclCacheProvider::class));
		$this->assertTrue(AclManager::existAclIn(current(AclManager::getAcls()), AclCacheProvider::class));
	}

	/**
	 * Tests AclCacheProvider->savePart()
	 */
	public function testSavePart() {
		AclManager::addRole('@TESTER', [
			'@OTHER'
		]);
		$this->assertFalse(AclManager::isAllowed('@OTHER', 'Home', 'ADMIN'));
		$this->assertFalse(AclManager::isAllowed('@TESTER', 'Home', 'ADMIN'));
		AclManager::allow('@OTHER', 'Home', 'ADMIN');
		$this->assertTrue(AclManager::isAllowed('@TESTER', 'Home', 'ADMIN'));

		AclManager::removeRole('@TESTER');
		$this->expectException(AclException::class);
		AclManager::isAllowed('@TESTER', 'Home', 'ADMIN');
		AclManager::saveAll();
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
		AclManager::saveAll();
		AclManager::start();
		AclManager::initFromProviders([
			new AclCacheProvider()
		]);
		$this->assertEquals(8, count(AclManager::getPermissions()));
		$this->assertFalse(AclManager::isAllowed('@OTHER', 'Other', 'DELETE'));
		AclManager::setPermissionLevel('DELETE', 0);
		$this->assertTrue(AclManager::isAllowed('@OTHER', 'Other', 'DELETE'));

		AclManager::removePermission('DELETE');
		AclManager::saveAll();
		$this->expectException(AclException::class);
		AclManager::isAllowed('@OTHER', 'Other', 'DELETE');
	}

	protected function initTestController() {
		$config = [
			"cache" => [
				"directory" => "cache/",
				"system" => "Ubiquity\\cache\\system\\ArrayCache",
				"params" => []
			],
			"mvcNS" => [
				"controllers" => "controllers"
			]
		];
		CacheManager::start($config);
		AclManager::initCache($config);

		AclManager::start();
		AclManager::initFromProviders([
			new AclCacheProvider()
		]);
		AclManager::addRole('nobody');
	}

	protected function _display($callback) {
		ob_start();
		$callback();
		return ob_get_clean();
	}

	protected function _assertDisplayEquals($callback, $result) {
		$res = $this->_display($callback);
		$this->assertEquals($result, $res);
	}

	/**
	 * Tests AclManager::initCache()
	 */
	public function testInitCache() {
		$this->initTestController();
		$this->assertEquals(3, count(AclManager::getRoles()));
		$this->assertEquals(3, \count(AclManager::getAcls()));
		$this->assertEquals(4, count(AclManager::getResources()));
		$this->assertEquals(7, count(AclManager::getPermissions()));

		$this->assertTrue(AclManager::isAllowed('@ALL', 'Home', 'ALLOW'));
		$this->assertTrue(AclManager::isAllowed('@OTHER', 'Other', 'ALLOW_OTHER'));

		$this->assertFalse(AclManager::isAllowed('@ALL', 'Home', 'ADMIN'));
		$this->assertFalse(AclManager::isAllowed('@OTHER', 'Home', 'ADMIN'));
	}

	/**
	 * Tests AclManager::getPermissionMap()
	 */
	public function testNavigation() {
		$this->initTestController();

		$_SERVER['REQUEST_METHOD'] = 'GET';
		$config = [
			"cache" => [
				"directory" => "cache/",
				"system" => "Ubiquity\\cache\\system\\ArrayCache",
				"params" => []
			],
			"mvcNS" => [
				"controllers" => "controllers"
			]
		];
		$this->_assertDisplayEquals(function () use ($config) {
			$_GET["c"] = 'TestController';
			Startup::run($config);
		}, 'nobody is not allowed!');

		$this->_assertDisplayEquals(function () use ($config) {
			$_GET['role'] = '@OTHER';
			$_GET["c"] = 'TestController/allowOther';
			Startup::run($config);
		}, 'allowOther');

		$this->_assertDisplayEquals(function () use ($config) {
			$_GET['role'] = '@ALL';
			$_GET["c"] = 'TestController/allowOther';
			Startup::run($config);
		}, '@ALL is not allowed!');

		$this->_assertDisplayEquals(function () use ($config) {
			$_GET['role'] = '@OTHER';
			$_GET["c"] = 'TestController/added';
			Startup::run($config);
		}, '@OTHER is not allowed!');

		$this->_assertDisplayEquals(function () use ($config) {
			$_GET['role'] = '@ALL';
			$_GET["c"] = 'TestController/added';
			Startup::run($config);
		}, '@ALL is not allowed!');

		AclManager::associate(TestController::class, 'added', 'Other', 'ALLOW_OTHER');

		$this->_assertDisplayEquals(function () use ($config) {
			$_GET['role'] = '@ALL';
			$_GET["c"] = 'TestController/added';
			Startup::run($config);
		}, '@ALL is not allowed!');
	}
}