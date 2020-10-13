# ubiquity-acl
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/phpMv/ubiquity-acl/badges/quality-score.png?b=main)](https://scrutinizer-ci.com/g/phpMv/ubiquity-acl/?branch=main)
[![Build Status](https://scrutinizer-ci.com/g/phpMv/ubiquity-acl/badges/build.png?b=main)](https://scrutinizer-ci.com/g/phpMv/ubiquity-acl/build-status/main)
[![Code Intelligence Status](https://scrutinizer-ci.com/g/phpMv/ubiquity-acl/badges/code-intelligence.svg?b=main)](https://scrutinizer-ci.com/code-intelligence)
[![Code Coverage](https://scrutinizer-ci.com/g/phpMv/ubiquity-acl/badges/coverage.png?b=main)](https://scrutinizer-ci.com/g/phpMv/ubiquity-acl/?branch=main)

Access control lists for Ubiquity framework

## Samples

```php
AclManager::start();
AclManager::addRole('@USER');
AclManager::addResource('Home');
AclManager::addPermission('READ',1);
AclManager::allow('@USER','Home','READ');
```
