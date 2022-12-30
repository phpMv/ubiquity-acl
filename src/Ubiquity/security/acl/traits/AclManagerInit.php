<?php

namespace Ubiquity\security\acl\traits;

use Ubiquity\cache\CacheManager;
use Ubiquity\cache\ClassUtils;
use Ubiquity\controllers\Startup;
use Ubiquity\exceptions\AclException;
use Ubiquity\security\acl\cache\AclControllerParser;
use Ubiquity\security\acl\models\AclList;
use Ubiquity\security\acl\persistence\AclCacheProvider;
use Ubiquity\security\acl\persistence\AclDAOProvider;
use Ubiquity\security\acl\persistence\AclProviderInterface;

/**
 * @property ?AclList $aclList
 * @property array $providersPersistence
 */
trait AclManagerInit {
	/**
	 * Create AclList with default roles and resources.
	 */
	public static function start(): void {
		self::$aclList = new AclList();
		self::$aclList->init();
	}

	/**
	 * Start the Acls with AclCacheProvider (for attributes or annotations).
	 */
	public static function startWithCacheProvider(): void {
		self::start();
		self::initFromProviders([new AclCacheProvider()]);
	}

	/**
	 * Check whether the Acl service is started.
	 *
	 * @return bool
	 */
	public static function isStarted(): bool {
		return isset(self::$aclList) && (self::$aclList instanceof AclList);
	}

	/**
	 * Load acls, roles, resources and permissions from providers.
	 *
	 * @param AclProviderInterface[] $providers
	 */
	public static function initFromProviders(?array $providers = []): void {
		self::$aclList->setProviders($providers);
		if (\count($providers) > 0) {
			self::$aclList->loadAcls();
			self::$aclList->loadRoles();
			self::$aclList->loadResources();
			self::$aclList->loadPermissions();
		}
	}

	/**
	 *
	 * @param array|string $selectedProviders
	 */
	public static function reloadFromSelectedProviders($selectedProviders = '*'): void {
		$sProviders = self::$aclList->getProviders();
		self::$aclList->clear();
		$providers = [];
		foreach ($sProviders as $prov) {
			if ($selectedProviders === '*' || (\is_array($selectedProviders) && \array_search(\get_class($prov), $selectedProviders) !== false)) {
				$providers[] = $prov;
			}
		}
		self::initFromProviders($providers);
		self::$aclList->setProviders($sProviders);
	}

	/**
	 *
	 * @param string $providerClass
	 * @return AclProviderInterface|NULL
	 */
	public static function getProvider(string $providerClass):?AclProviderInterface {
		return self::$aclList->getProvider($providerClass);
	}

	public static function getModelClassesSwap(): array {
		$result = [];
		$aclList = self::getAclList();
		if (isset($aclList)) {
			foreach ($aclList->getProviders() as $prov) {
				$result += $prov->getModelClassesSwap();
			}
		}
		return $result;
	}

	public static function filterProviders(string $providerClass):void {
		$providers = self::$aclList->getProviders();
		$filter = [];
		foreach ($providers as $prov) {
			if ($prov instanceof $providerClass) {
				$filter[] = $prov;
			}
		}
		self::$aclList->setProviders($filter);
		self::$providersPersistence = $providers;
	}

	public static function removefilterProviders():void {
		self::$aclList->setProviders(self::$providersPersistence);
	}

	/**
	 * Initializes AclDAOProvider and creates ACL tables in the specified dbOffset.
	 * Do not use in production
	 * @param array $config
	 * @param string $dbOffset
	 * @param array $classes
	 *        	associative array['acl'=>'','role'=>'','resource'=>'','permission'=>'']
	 * @return AclDAOProvider
	 * @throws AclException
	 */
	public static function initializeDAOProvider(array &$config, string $dbOffset='default', array $classes=[]): AclDAOProvider {
		self::start();
		return AclDAOProvider::initializeProvider($config,$dbOffset,$classes);
	}

	/**
	 * Initialize acls cache with controllers annotations.
	 * Do not execute at runtime
	 *
	 * @param array $config
	 * @param bool $silent
	 * @throws \Ubiquity\exceptions\AclException
	 */
	public static function initCache(array &$config, bool $silent=false): void {
		self::cacheOperation($config, $silent, function ($parser) {
			$parser->save();
		}, "ACLs cache reset\n");
	}

	/**
	 * @param array $config
	 * @return bool
	 */
	public static function checkCache(array &$config): bool {
		return self::cacheOperation($config, true, function ($parser) {
			return $parser->cacheUpdated();
		});
	}

	private static function cacheOperation(array &$config, bool $silent, $operation, $message=null) {
		if (!self::isStarted()) {
			self::start();
			self::initFromProviders([
				new AclCacheProvider()
			]);
		}
		self::filterProviders(AclCacheProvider::class);
		self::reloadFromSelectedProviders([]);
		self::registerAnnotations();
		$files = \Ubiquity\cache\CacheManager::getControllersFiles($config, $silent);
		$parser = new AclControllerParser();

		foreach ($files as $file) {
			if (\is_file($file)) {
				$controller = ClassUtils::getClassFullNameFromFile($file);
				try {
					$parser->parse($controller);
				} catch (\Exception $e) {
					if ($e instanceof AclException) {
						throw $e;
					}
				}
			}
		}
		$result = $operation($parser);
		self::removefilterProviders();
		self::reloadFromSelectedProviders();
		if (!$silent) {
			echo $message;
		}
		return $result;
	}

	protected static function registerAnnotations(): void {
		CacheManager::getAnnotationsEngineInstance()->registerAcls();
	}
}