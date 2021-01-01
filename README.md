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
