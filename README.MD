# facile-it/mongodb-bundle

Bundle service integration of official [mongodb/mongo-php-library](https://github.com/mongodb/mongo-php-library) driver library, ([mongodb/mongodb](https://packagist.org/packages/mongodb/mongodb) on packagist)

[![PHP](https://img.shields.io/badge/php-%5E7.0-blue.svg)](https://img.shields.io/badge/php-%5E7.0-blue.svg)
[![MongoDB](https://img.shields.io/badge/MongoDB-%5E3.0-lightgrey.svg)](https://img.shields.io/badge/MongoDB-%5E3.0-lightgrey.svg)
[![ext-mongodb](https://img.shields.io/badge/ext_mongodb-%5E1.1.5-orange.svg)](https://img.shields.io/badge/ext_mongodb-%5E1.1.5-orange.svg)
[![Latest Stable Version](https://poser.pugx.org/facile-it/mongodb-bundle/v/stable)](https://packagist.org/packages/facile-it/mongodb-bundle)
[![Latest Unstable Version](https://poser.pugx.org/facile-it/mongodb-bundle/v/unstable)](https://packagist.org/packages/facile-it/mongodb-bundle) [![Total Downloads](https://poser.pugx.org/facile-it/mongodb-bundle/downloads)](https://packagist.org/packages/facile-it/mongodb-bundle) 
[![License](https://poser.pugx.org/facile-it/mongodb-bundle/license)](https://packagist.org/packages/facile-it/mongodb-bundle)

[![Flex Support](https://img.shields.io/badge/Flex-supported-brightgreen.svg)]()
[![Build Status](https://travis-ci.org/facile-it/mongodb-bundle.svg?branch=master)](https://travis-ci.org/facile-it/mongodb-bundle)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/facile-it/mongodb-bundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/facile-it/mongodb-bundle/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/facile-it/mongodb-bundle/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/facile-it/mongodb-bundle/?branch=master)

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
    
    data_collection: true # set to false to disable data collection
    
    # clients section, here you can define connection to different servers or with different credentials
    clients:
    
      foo_client_name:    # choose your client name
        uri: 'mongodb://host1:3062,host2' # default null (will use hosts to build connection URI)
        hosts:            # required if uri is not set - will compose your connection URI (mongodb://host1:3062,host2:27017)
            - { host: host1, port: 3062 } # this 
            - { host: host2 }
        username: ''
        password: ''
        authSource: '' # the database name associated with the user’s credentials, defaults to connection
                       # 'database_name' if not specified
        replicaSet: '' # default null (no replica) (experimental)
        ssl: false
        connectTimeoutMS: 3000 # default null (no timeout)
        readPreference: primaryPreferred # see https://docs.mongodb.com/manual/reference/read-preference/#primary for info
        driverOptions:  
            context:  ~ # see https://www.php.net/manual/en/mongodb-driver-manager.construct.php
        
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

### Fixtures
This bundle supports doctrine style fixtures.
To create one you will need to add a php class in your bundle's `/DataFixtures/Mongo` directory. 
For Example `src/AppBundle/DataFixtures/Mongo/LoadTaskDataFixture.php`.

Note: This class must implement at least the `MongoFixtureInterface`, if you need container support you can 
extend `AbstractContainerAwareFixture` to use the `getContainer` method.

#### Ordering
Since [0.6.6](https://github.com/facile-it/mongodb-bundle/releases/tag/0.6.6) it's possibile to specify the order by 
which the fixtures are loaded.
Make sure it implements `OrderedFixtureInterface`.
Fixtures with lower values of `getOrder` will be loaded first.

### Commands

Some commands are provided:

* `mongodb:database:drop`: To drop a database.
* `mongodb:collection:drop`: To drop a collection.
* `mongodb:fixtures:load`: To load the fixtures.

### Query Profiling

On dev environment all queries executed by the library MongoDB\Collection class are profiled and showed inside the symfony web profiler.

[![Profiler Toolbar](https://github.com/facile-it/mongodb-bundle/blob/master/docs/img/profiler_toolbar.png)](https://github.com/facile-it/mongodb-bundle/blob/master/docs/img/profiler_toolbar.png)

[![Profiler Panel](https://github.com/facile-it/mongodb-bundle/blob/master/docs/img/profiler_panel.png)](https://github.com/facile-it/mongodb-bundle/blob/master/docs/img/profiler_panel.png)

### Contributing

Feel free to contribute by opening a pull request.
Bug fixes or feature suggestions are always welcome.

#### Development environment

If you like docker there are some scripts to setup an insulated development environment.
Please be sure to have `docker` and `docker-compose` installed on your system.

To setup the project:
```bash
make setup
```

To start the container and log into the php-cli:
```bash
make start
```

To execute the test suite:
```bash
make test
```

Note: All the above are meant to be used outside the containers.

#### Port binding setup

During the setup a `docker-compose.override.yml` file is created from the `docker-compose.override.yml.dist` template.
Change the port binding configuration inside it to fit you needs.
