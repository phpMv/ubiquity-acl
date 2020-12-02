<?php
namespace Ubiquity\security\acl\cache;

use Ubiquity\controllers\Controller;
use Ubiquity\orm\parser\Reflexion;
use Ubiquity\security\acl\AclManager;
use Ubiquity\cache\ClassUtils;
use Ubiquity\exceptions\AclException;

/**
 * Ubiquity\security\acl\cache$AclControllerParser
 * This class is part of Ubiquity
 *
 * @author jc
 * @version 1.0.0
 *
 */
class AclControllerParser {

	protected $controllerClass;

	protected $mainResource;

	protected $mainPermission;

	protected $permissionMap;

	public function __construct() {
		$this->permissionMap = new PermissionsMap();
	}

	public function init() {
		$this->permissionMap->init();
	}

	protected function parseMethods($methods) {
		$hasPermission = false;
		$controllerClass = $this->controllerClass;
		$controller = ClassUtils::getClassSimpleName($controllerClass);
		foreach ($methods as $method) {
			$this->parseMethod($method, $hasPermission, $controller);
		}
		if ($hasPermission || $this->mainResource != null || $this->mainPermission != null) {
			$permission = 'ALL';
			$resource = $this->mainResource ? $this->mainResource->name : $controller;
			$this->permissionMap->addAction($controller, '*', $resource, $this->mainPermission ? $this->mainPermission->name : 'ALL');
			AclManager::addResource($resource, $controller . '.*');
			if (isset($this->mainPermission)) {
				$permission = $this->mainPermission->name;
				AclManager::addPermission($this->mainPermission->name, ($this->mainPermission->level) ?? 0);
			}
			$annotsAllow = Reflexion::getAnnotationClass($controllerClass, '@allow');
			if (\is_array($annotsAllow) && \count($annotsAllow) > 0) {
				$this->addAllows($annotsAllow, $controller, '*', $resource, $permission);
			}
		}
	}

	protected function parseMethod(\ReflectionMethod $method, bool &$hasPermission, $controller) {
		$action = $method->name;
		$permission = NULL;
		$resource = NULL;
		$controllerClass = $this->controllerClass;
		if ($method->getDeclaringClass()->getName() === $controllerClass) {
			try {
				$annotResource = Reflexion::getAnnotationMethod($controllerClass, $action, '@resource');
				$annotPermission = Reflexion::getAnnotationMethod($controllerClass, $action, '@permission');
				if ($annotResource) {
					$resource = $annotResource->name;
					AclManager::addResource($annotResource->name, $controller . '.' . $action);
				}
				if ($annotPermission) {
					$permission = $annotPermission->name;
					AclManager::addPermission($annotPermission->name, $annotPermission->level ?? 0);
					$resource ??= $this->mainResource ? $this->mainResource->name : NULL;
					$hasPermission = true;
				} else {
					$resource ??= $this->mainResource ? $this->mainResource->name : ($controller . '.' . $action);
				}
				$annotsAllow = Reflexion::getAnnotationsMethod($controllerClass, $action, '@allow');
				if (\is_array($annotsAllow) && \count($annotsAllow) > 0) {
					$this->addAllows($annotsAllow, $controller, $action, $resource, $permission);
				}
				if ($permission !== null && $resource !== null) {
					$this->permissionMap->addAction($controllerClass, $action, $resource, $permission);
				}
			} catch (\Exception $e) {
				// Exception in controller code
			}
		}
	}

	protected function addAllows($annotsAllow, $controller, $action, &$resource, &$permission) {
		foreach ($annotsAllow as $annotAllow) {
			if (isset($annotAllow->resource) && isset($resource) && $resource !== $annotAllow->resource && $permission != null) {
				throw new AclException("Resources {$resource} and {$annotAllow->resource} are in conflict for action {$controller}.{$action}");
			}
			if (isset($annotAllow->permission) && isset($permission) && $permission !== $annotAllow->permission) {
				throw new AclException("Permissions {$permission} and {$annotAllow->permission} are in conflict for action {$controller}.{$action}");
			}
			if ($permission === null && $annotAllow->permission === null) {
				$resource = $controller . '.' . $action;
			}
			AclManager::addAndAllow($annotAllow->role, $resource = $annotAllow->resource ?? $resource, $permission = $annotAllow->permission ?? $permission);
		}
	}

	public function parse($controllerClass) {
		$this->controllerClass = $controllerClass;
		$reflect = new \ReflectionClass($controllerClass);
		if (! $reflect->isAbstract() && $reflect->isSubclassOf(Controller::class)) {
			try {
				$annotsResource = Reflexion::getAnnotationClass($controllerClass, '@resource');
				$annotsPermission = Reflexion::getAnnotationClass($controllerClass, '@permission');
				$annotAllows = Reflexion::getAnnotationClass($controllerClass, '@allow');
			} catch (\Exception $e) {
				// When controllerClass generates an exception
			}
			$this->mainResource = $annotsResource[0] ?? null;
			$this->mainPermission = $annotsPermission[0] ?? null;
			if (\is_array($annotAllows) && \count($annotAllows) > 0) {
				$resource = $this->mainResource ? $this->mainResource->name : $reflect->getShortName();
				$permission = $this->mainPermission ? $this->mainPermission->name : 'ALL';
				$this->addAllows($annotAllows, $controllerClass, null, $resource, $permission);
			}
			$methods = Reflexion::getMethods($controllerClass, \ReflectionMethod::IS_PUBLIC);
			$this->parseMethods($methods);
		}
	}

	public function save() {
		$this->permissionMap->save();
		AclManager::saveAll();
	}
}

