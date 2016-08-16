# facile-it/mongodb-bundle

Bundle service integration of official [mongodb/mongo-php-library](https://github.com/mongodb/mongo-php-library) driver library, ([mongodb/mongodb](https://packagist.org/packages/mongodb/mongodb) on packagist)

[![PHP Version](https://img.shields.io/badge/PHP-%3E%3D7.0-blue.svg)](https://img.shields.io/badge/PHP-%3E%3D7.0-blue.svg)
[![Latest Stable Version](https://poser.pugx.org/facile-it/mongodb-bundle/v/stable)](https://packagist.org/packages/facile-it/mongodb-bundle)
[![Latest Unstable Version](https://poser.pugx.org/facile-it/mongodb-bundle/v/unstable)](https://packagist.org/packages/facile-it/mongodb-bundle)
[![License](https://poser.pugx.org/facile-it/mongodb-bundle/license)](https://packagist.org/packages/facile-it/mongodb-bundle)

[![Build Status](https://travis-ci.org/facile-it/mongodb-bundle.svg?branch=master)](https://travis-ci.org/facile-it/mongodb-bundle)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/facile-it/mongodb-bundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/facile-it/mongodb-bundle/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/facile-it/mongodb-bundle/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/facile-it/mongodb-bundle/?branch=master)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/f220fc5f-de85-42ed-a3fb-b895818ea0ac/mini.png)](https://insight.sensiolabs.com/projects/f220fc5f-de85-42ed-a3fb-b895818ea0ac)

### Installation

First of all, you need to require this library through composer:

```bash
composer require facile-it/mongodb-bundle
```

Then, enable the bundle on the `AppKernel` class:

```php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Facile\MongoDbBundle\FacileMongoDbBundle(),
    );

    // ...

    return $bundles
}
```

### Configuration

Here is the configuration reference:

```yaml
mongo_db_bundle:

    # clients section, here you can define connection to different servers or with different credentials
    clients:
    
      foo_client_name:    #choose your client name
        host:             ~ # Required
        port:             27017
        username:         ''
        password:         ''
        replicaSet:       '' # default null (no replica)
        ssl:              false
        connectTimeoutMS: 3000 # default null (no timeout)
        
      other_client:   ~ # same as upper configuration
      
    # connections section, theese represents your Database object reference
    connections:
    
      foo_db:
        client_name:    foo_client_name # Required - your client name in clients section
        database_name:  'foo_db' # Required
        
      other_db:
        client_name:    ~
        database_name:  ~
        
      foo_db_2:
        client_name:    ~
        database_name:  ~
```

### Services

You can directly access to the `MongoDB\Database` with those services:

```php
$this->get('mongo.connection'); // Default connection (first declared)
$this->get('mongo.connection.{connectionName}'); // [test_db, other_db, test_db_2] for example
```

To manipulate the database, please read the official [documentation](http://mongodb.github.io/mongo-php-library/classes/database/)
