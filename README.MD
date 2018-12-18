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
[![Dependency Status](https://www.versioneye.com/user/projects/57b5b728090d4d0039befc7c/badge.svg?style=flat-square)](https://www.versioneye.com/user/projects/57b5b728090d4d0039befc7c)

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
        proto:            'mongodb' # optional, see https://docs.mongodb.com/manual/reference/connection-string/
        hosts:            # Required - will compose your mongo connection url (mongodb://host1:3062,host2:27017)
            - { host: host1, port: 3062 } # this 
            - { host: host2 }
        username:         ''
        password:         ''
        authSource:       '' # the database name associated with the user’s credentials, defaults to connection
                                database_name if not specified
        replicaSet:       '' # default null (no replica) (experimental)
        ssl:              false
        connectTimeoutMS: 3000 # default null (no timeout)
        readPreference:   primaryPreferred # see https://docs.mongodb.com/manual/reference/read-preference/#primary for info
        
      other_client:   ~ # same as upper configuration
      
    # connections section, these represents your Database object reference
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

Feel free to contribute by opening a pull request, if you find a bug or to suggest a new feature.
If you like docker, this repository is provided with a dev environment with some scripts to prepare and use it.
All you need is docker and docker-compose installed on your system.

```
 make setup  # will build the needed containers and setup the project
```

```
 make start  # will start the needed containers and put you inside the php-cli container
```

```
 make test  # will launch the test suite
```
Note: All these scripts are meant to be used outside the containers.

#### Port binding setup

During the setup a `docker-compose.override.yml` file is created from the `docker-compose.override.yml.dist` template.
Change the port binding configuration inside it to fit you needs.
