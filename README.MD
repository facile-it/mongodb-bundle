# mongo-bundle

Bundle service integration of official [mongodb/mongo-php-library](https://github.com/mongodb/mongo-php-library) driver library, ([mongodb/mongodb](https://packagist.org/packages/mongodb/mongodb) on packagist)

### Installation

First of all, you need to require this library through composer:

```bash
composer require algatux/mongo-bundle
```

Then, enable the bundle on the `AppKernel` class:

```php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new MongoBundle\MongoBundle(),
    );

    // ...

    return $bundles
}
```

### Configuration

Here is the configuration reference:

```yaml
mongo_bundle:

    # If not defined, the first connection will be taken.
    default_connection:   ~
    connections:

        # Prototype
        name:

            # Your MongoDB host address
            host:                 ~ # Required

            # Your MongoDB database name
            database:             ~ # Required

            port:                 27017
            username:             ''
            password:             ''
```

If you have only one connection to configure, this can be simplified to this:

```yaml
mongo_bundle:
    # Your MongoDB host address
    host:                 ~ # Required

    # Your InfluxDB database name
    database:             ~ # Required
    
    port:                 27017
    username:             ''
    password:             ''
```

### Services

You can directly access to the `MongoDB\Database` with those services:

```php
$this->get('mongo.connection'); // Default connection
$this->get('mongo.connection.{connectionName}'); // default if not specified
```

To manipulate the database, please read the official [documentation](http://mongodb.github.io/mongo-php-library/classes/database/)
