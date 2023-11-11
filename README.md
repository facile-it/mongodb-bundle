# Facile.it MongoDB Bundle

This bundle integrates the official [mongodb/mongo-php-library] driver library ([mongodb/mongodb] on packagist) 
with your Symfony app.

[![PHP](https://img.shields.io/badge/php-%5E7.4%7C%5E8.0-blue.svg)](https://img.shields.io/badge/php-%5E7.0-blue.svg)
[![MongoDB](https://img.shields.io/badge/MongoDB-%5E3.0-lightgrey.svg)](https://img.shields.io/badge/MongoDB-%5E3.0-lightgrey.svg)
[![ext-mongodb](https://img.shields.io/badge/ext_mongodb-%5E1.6.0-orange.svg)](https://img.shields.io/badge/ext_mongodb-%5E1.6.0-orange.svg)
[![Flex Support](https://img.shields.io/badge/Flex-supported-brightgreen.svg)]()

[![Latest Stable Version](https://poser.pugx.org/facile-it/mongodb-bundle/v/stable)](https://packagist.org/packages/facile-it/mongodb-bundle)
[![Latest Unstable Version](https://poser.pugx.org/facile-it/mongodb-bundle/v/unstable)](https://packagist.org/packages/facile-it/mongodb-bundle) [![Total Downloads](https://poser.pugx.org/facile-it/mongodb-bundle/downloads)](https://packagist.org/packages/facile-it/mongodb-bundle) 
[![License](https://poser.pugx.org/facile-it/mongodb-bundle/license)](https://packagist.org/packages/facile-it/mongodb-bundle)


[![CI](https://github.com/facile-it/mongodb-bundle/actions/workflows/ci.yaml/badge.svg)](https://github.com/facile-it/mongodb-bundle/actions/workflows/ci.yaml)
[![Static analysis](https://github.com/facile-it/mongodb-bundle/actions/workflows/static-analysis.yaml/badge.svg)](https://github.com/facile-it/mongodb-bundle/actions/workflows/static-analysis.yaml)
[![codecov](https://codecov.io/gh/facile-it/mongodb-bundle/branch/master/graph/badge.svg?token=gEhvCteV7k)](https://codecov.io/gh/facile-it/mongodb-bundle)

[mongodb/mongo-php-library]: https://github.com/mongodb/mongo-php-library
[mongodb/mongodb]: https://packagist.org/packages/mongodb/mongodb

## Features

- Provide instances of `MongoDB\Database` as [services](docs/Documentation.md#database-as-a-service) for your Symfony app.
- Add a [query profiling](docs/Documentation.md#query-profiling) section to the profiler toolbar.
- Capability for loading [data fixtures](docs/Documentation.md#fixtures).

Try it now!

    composer require facile-it/mongodb-bundle

## Resources

- [Documentation](docs/Documentation.md)
- [Upgrading notes](docs/Upgrade.md)
- [Contribution guidelines](docs/Contributing.md)
