<?php
use Ubiquity\cache\CacheManager;
use Ubiquity\security\acl\AclManager;
use Ubiquity\security\acl\persistence\AclCacheProvider;
use Ubiquity\exceptions\AclException;

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
}

