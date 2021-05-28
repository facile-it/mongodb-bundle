# Facile.it MongoDB Bundle

This bundle integrates the official [mongodb/mongo-php-library] driver library ([mongodb/mongodb] on packagist) 
with your Symfony app.

[![PHP](https://img.shields.io/badge/php-%5E7.2%7C%5E8.0-blue.svg)](https://img.shields.io/badge/php-%5E7.0-blue.svg)
[![MongoDB](https://img.shields.io/badge/MongoDB-%5E3.0-lightgrey.svg)](https://img.shields.io/badge/MongoDB-%5E3.0-lightgrey.svg)
[![ext-mongodb](https://img.shields.io/badge/ext_mongodb-%5E1.1.5-orange.svg)](https://img.shields.io/badge/ext_mongodb-%5E1.1.5-orange.svg)

[![Latest Stable Version](https://poser.pugx.org/facile-it/mongodb-bundle/v/stable)](https://packagist.org/packages/facile-it/mongodb-bundle)
[![Latest Unstable Version](https://poser.pugx.org/facile-it/mongodb-bundle/v/unstable)](https://packagist.org/packages/facile-it/mongodb-bundle) [![Total Downloads](https://poser.pugx.org/facile-it/mongodb-bundle/downloads)](https://packagist.org/packages/facile-it/mongodb-bundle) 
[![License](https://poser.pugx.org/facile-it/mongodb-bundle/license)](https://packagist.org/packages/facile-it/mongodb-bundle)

[![Flex Support](https://img.shields.io/badge/Flex-supported-brightgreen.svg)]()
![CI](https://github.com/facile-it/mongodb-bundle/workflows/CI/badge.svg)
![Static analysis](https://github.com/facile-it/mongodb-bundle/workflows/Static%20analysis/badge.svg)
[![codecov](https://codecov.io/gh/facile-it/mongodb-bundle/branch/master/graph/badge.svg?token=gEhvCteV7k)](https://codecov.io/gh/facile-it/mongodb-bundle)

[mongodb/mongo-php-library]: https://github.com/mongodb/mongo-php-library
[mongodb/mongodb]: https://packagist.org/packages/mongodb/mongodb

## Features

- Provide instances of `MongoDB\Database` as [services](#services) for your Symfony app.
- Add a [query profiling](#query-profiling) section to the profiler toolbar.
- Capability for loading [data fixtures](#fixtures).

## Installation

First, you need to require this library through composer:

```bash
composer require facile-it/mongodb-bundle
```

Then, enable the bundle on the `AppKernel` class:

```php
// app/AppKernel.php
class AppKernel extends Kernel {

    public function registerBundles()
    {
        return [
            // ...
            new Facile\MongoDbBundle\FacileMongoDbBundle(),
        ];
    }
}
```

## Configuration

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
        username: 'what-a-secret'
        password: 'what-a-secret'
        authSource: '' # the database name with the user’s credentials
        replicaSet: '' # default null (no replica) (experimental)
        ssl: false
        connectTimeoutMS: 3000 # default null (no timeout)
        readPreference: primaryPreferred # see https://docs.mongodb.com/manual/reference/read-preference/#primary for info

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
        
    # Service reference to manage driver options context - see example below
    driverOptions: "app.mongodb.context_service" # default null
```

### Driver options

Example service to manage driver options. This class must implement Facile\MongoDbBundle\Services\DriverOptions\DriverOptionsInterface.

```yml
# config/packages/facile_it_mongodb.yaml
mongo_db_bundle:
    ...
    driverOptions: "app.mongodb.context_service"

# config/services.yaml
app.mongodb.context_service:
    class: 'App\Services\DriverOptionsService'
    arguments:
        - context: { cafile: 'example/route/file.crt' }
```

## Services

You can directly access to the `MongoDB\Database` with those services:

```php
$this->get('mongo.connection'); // Default connection (first declared)
$this->get('mongo.connection.{connectionName}'); // [test_db, other_db, test_db_2] for example
```

To manipulate the database, please read the official [documentation](http://mongodb.github.io/mongo-php-library/classes/database/)

## Query Profiling

On dev environment all queries executed by the library MongoDB\Collection class are profiled and showed inside the Symfony web profiler.

[![Profiler Toolbar](https://github.com/facile-it/mongodb-bundle/blob/master/docs/img/profiler_toolbar.png)](https://github.com/facile-it/mongodb-bundle/blob/master/docs/img/profiler_toolbar.png)

[![Profiler Panel](https://github.com/facile-it/mongodb-bundle/blob/master/docs/img/profiler_panel.png)](https://github.com/facile-it/mongodb-bundle/blob/master/docs/img/profiler_panel.png)

## Fixtures
This bundle supports doctrine style fixtures.
To create one you will need to add a php class in your bundle's `/DataFixtures/Mongo` directory.
For Example `src/AppBundle/DataFixtures/Mongo/LoadTaskDataFixture.php`.

Note: This class must implement at least the `MongoFixtureInterface`, if you need container support you can
extend `AbstractContainerAwareFixture` to use the `getContainer` method.

### Ordering
Since [0.6.6](https://github.com/facile-it/mongodb-bundle/releases/tag/0.6.6) it's possibile to specify the order by
which the fixtures are loaded.
Make sure it implements `OrderedFixtureInterface`.
Fixtures with lower values of `getOrder` will be loaded first.

### Commands
Some commands are provided:

* `mongodb:database:drop` To drop a database.
* `mongodb:collection:drop` To drop a collection.
* `mongodb:fixtures:load` To load the fixtures.

## Upgrade notes

### Upgrading to 1.1.0
The release [1.1.0] introduced a BC change in the configuration semantic.

Before 1.1.0, if not explicitly provided, the value of `authSource` was set to the database name specified
in the connection section, or to `admin` if even the database name was empty.

With 1.1.0, if the `authSource` key is not set in the configuration, it won't be set in connection options either.

Take a look to [CHANGELOG.md](CHANGELOG.md) for further details.

[1.1.0]: https://github.com/facile-it/mongodb-bundle/releases/tag/1.1.0

## Contributing

Feel free to contribute by opening a pull request.
Bug fixes or feature suggestions are always welcome.

### Development environment

If you like docker then there are some scripts to set up an insulated development environment.
Please be sure to have `docker` and `docker-compose` installed on your system.

To set up the project:
```bash
make setup
```

To start the container and log into the php:
```bash
make start
```

To execute the test suite:
```bash
make test
```

Note: All the above are meant to be used outside the containers.

### Port binding setup

During the set up a `docker-compose.override.yml` file is created from the `docker-compose.override.yml.dist` template.
Change the port binding configuration inside it to fit you needs.
