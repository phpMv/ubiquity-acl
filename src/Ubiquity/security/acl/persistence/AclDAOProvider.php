<?php
namespace Ubiquity\security\acl\persistence;

use Ubiquity\cache\CacheManager;
use Ubiquity\cache\ClassUtils;
use Ubiquity\controllers\Startup;
use Ubiquity\db\reverse\DbGenerator;
use Ubiquity\exceptions\AclException;
use Ubiquity\orm\DAO;
use Ubiquity\orm\reverse\DatabaseReversor;
use Ubiquity\scaffolding\creators\ClassCreator;
use Ubiquity\security\acl\models\AbstractAclPart;
use Ubiquity\security\acl\models\AclElement;
use Ubiquity\security\acl\models\Permission;
use Ubiquity\security\acl\models\Resource;
use Ubiquity\security\acl\models\Role;

/**
 * Load and save Acls with a database using DAO.
 * Ubiquity\security\acl\persistence$AclDAOProvider
 * This class is part of Ubiquity
 *
 * @author jc
 * @version 1.0.3
 *
 */
class AclDAOProvider implements AclProviderInterface {

	protected string $aclClass;

	protected string $roleClass;

	protected string $permissionClass;

	protected string $resourceClass;
	
	/**
	 * @param array $config The $config array
	 * @param array $classes
	 *        	associative array['acl'=>'','role'=>'','resource'=>'','permission'=>'']
	 */
	public function __construct(array &$config, array $classes = []) {
		Startup::$config=$config;
		$this->setClasses($classes);
	}

	private function setClasses(array $classes): void {
		$this->aclClass = $classes['acl'] ?? AclElement::class;
		$this->roleClass = $classes['role'] ?? Role::class;
		$this->resourceClass = $classes['resource'] ?? Resource::class;
		$this->permissionClass = $classes['permission'] ?? Permission::class;
	}

	/**
	 * Initialize the cache for the ACL models.
	 * @param $config
	 */
	public function initModelsCache(&$config) {
		CacheManager::start($config);
		CacheManager::createOrmModelCache($this->aclClass);
		CacheManager::createOrmModelCache($this->roleClass);
		CacheManager::createOrmModelCache($this->resourceClass);
		CacheManager::createOrmModelCache($this->permissionClass);
	}

	/**
	 * Defines the database offset used for ACL.
	 * @param string $dbOffset
	 * @param  bool $persist
	 */
	public function setDbOffset(string $dbOffset = 'default', bool $persist=true): void {
		DAO::setModelDatabase($this->aclClass, $dbOffset);
		DAO::setModelDatabase($this->resourceClass, $dbOffset);
		DAO::setModelDatabase($this->roleClass, $dbOffset);
		DAO::setModelDatabase($this->permissionClass, $dbOffset);
		if ($persist) {
			CacheManager::storeModelsDatabases(DAO::getModelsDatabase ());
		}
	}

	/**
	 * Generates the models.
	 * @param string $dbOffset default
	 * @param ?array $classes associative array['acl'=>'','role'=>'','resource'=>'','permission'=>'']
	 */
	public function createModels(string $dbOffset='default', ?array $classes=null): void {
		$classes??=[
			'acl'=>'models\\AclElement','role'=>'models\\Role','resource'=>'models\\Resource','permission'=>'models\\Permission'
		];
		$this->setClasses($classes);
		$this->createModel($classes['acl'] ?? $this->aclClass,AclElement::class, $dbOffset);
		$this->createModel($classes['role'] ?? $this->roleClass,Role::class, $dbOffset);
		$this->createModel($classes['resource'] ?? $this->resourceClass,Resource::class, $dbOffset);
		$this->createModel($classes['permission'] ?? $this->permissionClass,Permission::class, $dbOffset);
	}

	public function createModel(string $modelName, string $refName, string $dbOffset='default'): void {
		if ($modelName!==$refName) {
			$className=ClassUtils::getClassSimpleName($modelName);
			$ns=ClassUtils::getNamespaceFromCompleteClassname($modelName);
			$cCreator=new ClassCreator($className, $ns, ' extends \\'.$refName);
			if ($dbOffset!=='default') {
				$annot=CacheManager::getAnnotationsEngineInstance()->getAnnotation($cCreator, 'database', ['name'=>$dbOffset]);
				$cCreator->addClassAttribute($annot);
			}
			$cCreator->generate();
		}
	}

	/**
	 * Generates the tables for ACL model classes.
	 * @param string $dbOffset
	 * @param bool $createDb
	 * @throws AclException
	 */
	public function generateDbTables(string $dbOffset='default',bool $createDb=false):void{
		$this->setDbOffset($dbOffset);
		$generator = new DatabaseReversor(new DbGenerator(), $dbOffset);
		$activeOffsetValue=DAO::getDbOffset(Startup::$config, $dbOffset);
		if (($dbName=$activeOffsetValue['dbName']??'')!='') {
			$generator->setModels([$this->aclClass,$this->roleClass,$this->resourceClass,$this->permissionClass]);
			$generator->createDatabase($dbName, $createDb);
			$db=DAO::getDatabase($dbOffset);
			$db->beginTransaction();
			$db->execute($generator->__toString());
			$db->commit();
		} else {
			throw new AclException('dbName key is not present or his value is empty!');
		}
	}

	/**
	 *
	 * {@inheritdoc}
	 * @see \Ubiquity\security\acl\persistence\AclProviderInterface::loadAllAcls()
	 */
	public function loadAllAcls(): array {
		$result= DAO::getAll($this->aclClass);
		foreach ($result as $elm) {
			$elm->setType(AclDAOProvider::class);
		}
		return $result;
	}

	/**
	 *
	 * {@inheritdoc}
	 * @see \Ubiquity\security\acl\persistence\AclProviderInterface::saveAcl()
	 */
	public function saveAcl(AclElement $aclElement) {
		$aclElement->_rest=[];
		if (!$this->existPart($aclElement->getResource())) {
			$this->savePart($aclElement->getResource());
		}
		if (!$this->existPart($aclElement->getPermission())) {
			$this->savePart($aclElement->getPermission());
		}
		if (!$this->existPart($aclElement->getRole())) {
			$this->savePart($aclElement->getRole());
		}
		$object = $this->castElement($aclElement);
		if ($this->existAcl($aclElement)) {
			$res=DAO::update($object);
		} else {
			$res = DAO::insert($object);
		}
		if ($res) {
			$aclElement->setId($object->getId());
		}
		return $res;
	}

	/**
	 *
	 * {@inheritdoc}
	 * @see \Ubiquity\security\acl\persistence\AclProviderInterface::removeAcl()
	 */
	public function removeAcl(AclElement $aclElement) {
		return DAO::remove($aclElement);
	}

	protected function loadElements(string $className): array {
		$elements = DAO::getAll($className);
		$result = [];
		foreach ($elements as $elm) {
			$elm->setType(AclDAOProvider::class);
			$result[$elm->getName()] = $elm;
		}
		return $result;
	}

	protected function castElement($part) {
		$class = $this->getModelClasses()[get_class($part)] ?? get_class($part);
		return $part->castAs($class);
	}

	/**
	 *
	 * {@inheritdoc}
	 * @see \Ubiquity\security\acl\persistence\AclProviderInterface::loadAllPermissions()
	 */
	public function loadAllPermissions(): array {
		return $this->loadElements($this->permissionClass);
	}

	/**
	 *
	 * {@inheritdoc}
	 * @see \Ubiquity\security\acl\persistence\AclProviderInterface::loadAllResources()
	 */
	public function loadAllResources(): array {
		return $this->loadElements($this->resourceClass);
	}

	/**
	 *
	 * {@inheritdoc}
	 * @see \Ubiquity\security\acl\persistence\AclProviderInterface::loadAllRoles()
	 */
	public function loadAllRoles(): array {
		return $this->loadElements($this->roleClass);
	}

	/**
	 *
	 * {@inheritdoc}
	 * @see \Ubiquity\security\acl\persistence\AclProviderInterface::savePart()
	 */
	public function savePart(\Ubiquity\security\acl\models\AbstractAclPart $part) {
		$object = $this->castElement($part);
		if ($this->existPart($part)) {
			$object->_rest=[];
			$res = DAO::update($object);
		} else {
			$res=DAO::insert($object);
		}
		if ($res) {
			$part->setName($object->getName());
		}
		return $res;
	}

	/**
	 *
	 * {@inheritdoc}
	 * @see \Ubiquity\security\acl\persistence\AclProviderInterface::updatePart()
	 */
	public function updatePart(string $id, \Ubiquity\security\acl\models\AbstractAclPart $part) {
		$object=$this->castElement($part);
		$object->_rest=[];
		$object->_pkv['___name']=$id;
		return DAO::update($object);
	}

	/**
	 *
	 * {@inheritdoc}
	 * @see \Ubiquity\security\acl\persistence\AclProviderInterface::removePart()
	 */
	public function removePart(\Ubiquity\security\acl\models\AbstractAclPart $part) {
		return DAO::remove($this->castElement($part));
	}

	public function isAutosave(): bool {
		return true;
	}

	public function saveAll(): void {}

	public function existPart(AbstractAclPart $part): bool {
		$elm = $this->castElement($part);
		return DAO::exists(\get_class($elm), 'name= ?', [
			$elm->getName()
		]);
	}

	public function existAcl(AclElement $aclElement): bool {
		$elm = $this->castElement($aclElement);
		return DAO::exists(\get_class($elm), 'id= ?', [
			$elm->getId()
		]);
	}

	public function getDetails(): array {
		return [
			'user' => $this->roleClass,
			'archive' => $this->resourceClass,
			'unlock alternate' => $this->permissionClass,
			'lock' => $this->aclClass
		];
	}

	public function getModelClassesSwap(): array {
		$swap = $this->getModelClasses();
		$classes = \array_values($swap);
		$result = [];
		foreach ($classes as $class) {
			$result[$class] = $swap;
		}
		return $result;
	}

	public function getModelClasses(): array {
		return [
			AclElement::class => $this->aclClass,
			Role::class => $this->roleClass,
			Resource::class => $this->resourceClass,
			Permission::class => $this->permissionClass
		];
	}

	public function clearAll(): void {}

	/**
	 * Initializes AclDAOProvider and creates ACL tables in the specified dbOffset.
	 * Do not use in production
	 *
	 * @param array $config
	 * @param string $dbOffset
	 * @param array $classes
	 *        	associative array['acl'=>'','role'=>'','resource'=>'','permission'=>'']
	 * @return AclDAOProvider
	 * @throws AclException
	 */
	public static function initializeProvider(array $config,string $dbOffset='default',array $classes = []): AclDAOProvider {
		$dbProvider=new AclDAOProvider($config,$classes);
		$dbProvider->initModelsCache($config);
		$dbProvider->setDbOffset($dbOffset);
		$dbProvider->generateDbTables($dbOffset);
		return $dbProvider;
	}
	
	public function cacheUpdated(): bool {
		return false;
	}
}
