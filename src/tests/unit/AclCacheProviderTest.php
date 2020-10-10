<?php
use Ubiquity\cache\CacheManager;
use Ubiquity\security\acl\AclManager;
use Ubiquity\security\acl\persistence\AclCacheProvider;

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
	}

	/**
	 * Tests AclCacheProvider->loadAllAcls()
	 */
	public function testLoadAllAcls() {
		AclManager::start();
		AclManager::initFromProviders([
			new AclCacheProvider()
		]);
		$this->assertEquals(1, \count(AclManager::getAcls()));
	}

	/**
	 * Tests AclCacheProvider->savePart()
	 */
	public function testSavePart() {
		// TODO Auto-generated AclCacheProviderTest->testSavePart()
		$this->markTestIncomplete("savePart test not implemented");

		$this->aclCacheProvider->savePart(/* parameters */);
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
		// TODO Auto-generated AclCacheProviderTest->testSaveAll()
		$this->markTestIncomplete("saveAll test not implemented");

		$this->aclCacheProvider->saveAll(/* parameters */);
	}
}

