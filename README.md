# ubiquity-acl
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/phpMv/ubiquity-acl/badges/quality-score.png?b=main)](https://scrutinizer-ci.com/g/phpMv/ubiquity-acl/?branch=main)
[![Build Status](https://scrutinizer-ci.com/g/phpMv/ubiquity-acl/badges/build.png?b=main)](https://scrutinizer-ci.com/g/phpMv/ubiquity-acl/build-status/main)
[![Code Intelligence Status](https://scrutinizer-ci.com/g/phpMv/ubiquity-acl/badges/code-intelligence.svg?b=main)](https://scrutinizer-ci.com/code-intelligence)
[![Code Coverage](https://scrutinizer-ci.com/g/phpMv/ubiquity-acl/badges/coverage.png?b=main)](https://scrutinizer-ci.com/g/phpMv/ubiquity-acl/?branch=main)

Access control lists for Ubiquity framework

## Samples

### Defining ACLs at runtime
#### One by one
```php
AclManager::start();
AclManager::addRole('@USER');
AclManager::addResource('Home');
AclManager::addPermission('READ',1);
AclManager::allow('@USER','Home','READ');
```
#### By grouping
```php
AclManager::start();
AclManager::addAndAllow('@USER','Home','READ');
```
### Defining ACLs with annotations or attributes
#### Starting
```php
use Ubiquity\security\acl\AclManager;
use Ubiquity\security\acl\persistence\AclCacheProvider;

AclManager::start();
AclManager::initFromProviders([
	new AclCacheProvider()
]);
```

#### Defining ACLs in controllers

##### A controller as a resource, authorized for a role
With annotations:
```php
namespace controllers;
/**
 * @resource('Main')
 * @allow('role'=>'@USER')
 */
class TestAclController extends ControllerBase {
	use AclControllerTrait;
}
```

With attributes:
```php
namespace controllers;
use Ubiquity\attributes\items\acl\Resource;
use Ubiquity\attributes\items\acl\Allow;

#[Resource('Main')]
#[Allow(role: '@USER')]
class TestAclController extends ControllerBase {
	use AclControllerTrait;
}
```

#### Overriding
It is necessary to override the _getRole method so that it returns the role of the active user:

```php
namespace controllers;
use Ubiquity\attributes\items\acl\Resource;
use Ubiquity\attributes\items\acl\Allow;use Ubiquity\utils\http\USession;
use Ubiquity\utils\http\USession;

#[Resource('Main')]
#[Allow(role: '@USER')]
class TestAclController extends ControllerBase {
	use AclControllerTrait;
	
	public function _getRole(){
	    $activeUser=USession::get('activeUser');
	    if(isset($activeUser)){
	        return $activeUser->getRole();
	    }
	}
}
```

### Defining ACLs with Database
The ACLs defined in the database are additional to the ACLs defined via annotations or attributes.

#### Initializing
The initialization allows to create the tables associated to the ACLs (`Role`, `Resource`, `Permission`, `AclElement`).
It needs to be done only once, and in dev mode only.
```php
use Ubiquity\controllers\Startup;
use Ubiquity\security\acl\AclManager;

$config=Startup::$config;
AclManager::initializeDAOProvider($config, 'default');
```

#### Starting
In `app/config/services.php` file :
```php
use Ubiquity\security\acl\AclManager;
use Ubiquity\security\acl\persistence\AclCacheProvider;
use Ubiquity\security\acl\persistence\AclDAOProvider;
use Ubiquity\orm\DAO;

DAO::start();//Optional, to use only if dbOffset is not default

AclManager::start();
AclManager::initFromProviders([
	new AclCacheProvider(), new AclDAOProvider($config)
]);
```
