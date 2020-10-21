<?php
use Ubiquity\cache\CacheManager;
use Ubiquity\security\acl\AclManager;
use Ubiquity\security\acl\persistence\AclCacheProvider;
use Ubiquity\exceptions\AclException;
use Ubiquity\controllers\Startup;

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
		AclManager::start();
		AclManager::initFromProviders([
			new AclCacheProvider()
		]);
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
		$this->assertFalse(AclManager::isAllowed('USER', 'Home', 'WRITE'));
		AclManager::allow('USER', 'Home', 'WRITE');
		$this->assertEquals(2, \count(AclManager::getAcls()));
		$this->assertTrue(AclManager::isAllowed('USER', 'Home', 'WRITE'));

		AclManager::removeAcl('USER', 'Home', 'WRITE');
		$this->assertFalse(AclManager::isAllowed('USER', 'Home', 'WRITE'));
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
		AclManager::addPermission('DELETE', 5);
		AclManager::saveAll();
		AclManager::start();
		AclManager::initFromProviders([
			new AclCacheProvider()
		]);
		$this->assertEquals(4, count(AclManager::getPermissions()));
		$this->assertFalse(AclManager::isAllowed('USER', 'Home', 'DELETE'));
		AclManager::setPermissionLevel('DELETE', 0);
		$this->assertTrue(AclManager::isAllowed('USER', 'Home', 'DELETE'));

		AclManager::removePermission('DELETE');
		AclManager::saveAll();
		$this->expectException(AclException::class);
		AclManager::isAllowed('USER', 'Home', 'DELETE');
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
		$this->assertEquals(3, count(AclManager::getResources()));
		$this->assertEquals(1, \count(AclManager::getAcls()));
		$this->assertEquals(3, count(AclManager::getRoles()));
		$this->assertEquals(3, count(AclManager::getPermissions()));

		AclManager::initCache($config);

		AclManager::start();
		AclManager::initFromProviders([
			new AclCacheProvider()
		]);
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
		$this->assertEquals(4, count(AclManager::getRoles()));
		$this->assertEquals(4, \count(AclManager::getAcls()));
		$this->assertEquals(6, count(AclManager::getResources()));
		$this->assertEquals(7, count(AclManager::getPermissions()));

		$this->assertTrue(AclManager::isAllowed('@ALL', 'Home', 'ALLOW'));
		$this->assertTrue(AclManager::isAllowed('@OTHER', 'Other', 'ALLOW_OTHER'));

		$this->assertFalse(AclManager::isAllowed('@ALL', 'Home', 'ADMIN'));
		$this->assertFalse(AclManager::isAllowed('@OTHER', 'Home', 'ADMIN'));

		AclManager::removeResource('IndexResource');
		AclManager::removeResource('Other');
		AclManager::removeResource('TestController.allowOther2');
		AclManager::removeAcl('@ALL', 'Home', 'ALLOW');
		AclManager::removeAcl('@OTHER', 'Other', 'ALLOW_OTHER');
		AclManager::removeAcl('@OTHER', 'TestController.allowOther2', 'ALL');
		AclManager::removePermission('ALLOW');
		AclManager::removePermission('ALLOW_OTHER');
		AclManager::removePermission('NEW_PERMISSION');
		AclManager::removePermission('ADMIN');
		AclManager::removeRole('@OTHER');
		$this->assertEquals(3, count(AclManager::getRoles()));
		$this->assertEquals(3, count(AclManager::getResources()));
		$this->assertEquals(1, \count(AclManager::getAcls()));
		$this->assertEquals(3, count(AclManager::getPermissions()));
		AclManager::saveAll();
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
		}, 'index');

		$this->_assertDisplayEquals(function () use ($config) {
			$_GET['role'] = '@OTHER';
			$_GET["c"] = 'TestController/allowOther';
			Startup::run($config);
		}, 'allowOther');

		$this->_assertDisplayEquals(function () use ($config) {
			$_GET['role'] = '@USER';
			$_GET["c"] = 'TestController/allowOther';
			Startup::run($config);
		}, '@USER is not allowed!');
	}
}

