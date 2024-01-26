# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.6.0] (2024-01-26)
### Added 
* Support for Symfony 7
* CI coverage for PHP 8.1, 8.2, 8.3
### Removed
* Support for PHP < 7.4
* Support for Symfony < 4.4
* Support for ext-mongo < 1.6
* Support for mongodb/mongodb < 1.5
* Docker images dev-dependency from https://github.com/ilario-pierbattista/docker-php-mongodb-bundle

## [1.5.0] (2022-01-06)
### Added
* Symfony 6 support (#121) by [@Jean85](https://github.com/Jean85).

## [1.4.0] (2021-03-06)
### Added
* PHP 8 support (#114) by [@Jean85](https://github.com/Jean85).

## [1.3.0] (2021-02-28)
### Added
* This changelog with (#109) by [@Jean85](https://github.com/Jean85).
### Fixed
* Fixed issue with MongoQuerySerializer (#112) by [@starred-gijs](https://github.com/starred-gijs).
* Fixed Symfony 5 support and solved 5.1 deprecations (#111) by [@Jean85](https://github.com/Jean85).

## [1.2.0] (2021-02-19)
### Added
* Add support for `symfony/framework-bundle` 5 by [@Jean85](https://github.com/Jean85).
### Changed
* Replace Travis with GitHub Actions (#106) by [@Jean85](https://github.com/Jean85).

## [1.1.0] (2021-01-14)
### Changed
* The `authSource` key in configuration doesn't fallback anymore on `<database name>` or `admin`
### Fixed
* Use FQCN for the controller (#100, thanks [@starred-gijs](https://github.com/starred-gijs))

## [1.0.0] (2019-06-12)
### Changed
* Driver options factory (to have SSL driver options) (#89, thanks [@antoniojlm84](https://github.com/antoniojlm84))
### Removed
* Removed support for PHP < 7.1
### Fixed
* Fix deprecations for Symfony 5.1 (#95)
* Fix event dispatcher deprecations in SF 4.3 (#80)
* Fix debug issue using other environment than dev (#83, thanks [@djgxp](https://github.com/djgxp))

## [0.7.3] (2019-05-27)
### Fixed
* Remove deprecations (#74)

## [0.7.2] (2019-05-03)
### Fixed
* Ordered fixtures loading fix (#72)

## [0.7.1] (2019-03-12)
### Fixed
* Fix `MongoFixtureInterface` signature (#68)

## [0.7.0] (2018-12-19)
### Added
* Add uri configuration option to client (#64, thanks [@duxet](https://github.com/duxet))

## [0.6.7] (2018-10-13)
### Changed
* Added return type to `OrderedFixtureInterface::getOrder`

## [0.6.6] (2018-10-12)
### Added
* Add ability to specify order of loading fixtures (#54, thanks [@mmskl](https://github.com/mmskl))

## [0.6.5] (2018-01-18)
### Added
* Database name added to profiler query row
### Changed
* Small CS fixes for performance improvements (#47)
### Fixed
* Fix Profiler errors on Symfony4+ (#50, thanks [@Dameon87](https://github.com/Dameon87))

## [0.6.4] (2018-01-03)
### Changed
* Revert bundle container configuration back to XML where possible

## [0.6.3] (2018-01-03)
### Changed
* Register commands manually to avoid SF 3.4 deprecation (#49)

## [0.6.2] (2017-12-16)
### Added
* Added support for Symfony 4
### Fixed
* Fix another special case in the query serialization (#43)
* Fixed PHPDoc (#46)

## [0.6.1] (2017-11-16)
### Fixed
* Fixed query serialization

## [0.6.0] (2017-11-14)
### Added
* Added authSource in the configuration options (#40)
### Fixed
* Fix MongoQuerySerializer

## 0.5.0 (2017-08-29)
### Added
* Added explain query service
* Added explain to profiler

## 0.4.5 (2017-08-25)
### Added
* Added a docker dev/test environment (#37)
* Show read preference in profiler

## 0.4.4 (2017-07-07)
### Fixed
* Profiler template style fixes for long collection names
* Fixed profiler filters and data labels, now they show a more appropriated description (31)

## 0.4.3 (2017-04-15)
### Changed
* Changed date representation in the profiler (#36)

## 0.4.2 (2017-04-11)
### Added
 * Added support for `bsonSerialize` while serializing the query for the profiler (#35)

## 0.4.1 (2017-04-10)
### Added
* Added read preference option to `Configuration` and `ClientConfiguration` class (#34)

## 0.4.0 (2017-03-31)
### Changed
* Refactored support for multiple hosts (#32)

## 0.3.3 (2017-03-24)
### Added
* Added support for `Collection:distinct` method (#33)
* Added support for multiple hosts
### Changed
* Make some more classes `final`: `MongoLogEventSerializer` `MongoDbBundleExtension` `DataCollectorListener` `ConnectionEvent` `QueryEvent` `MongoFixturesLoader` `ClientConfiguration` `Query` `ClientRegistry` `ConnectionFactory`
* Rename `MongoLogEventSerializer` in `MongoQuerySerializer`
* Rename `MongoLogger` in `MongoQueryLogger`

## 0.3.2 (2017-01-25)
### Changed
* Make the `mongo.client_registry` service public (#29)

## 0.3.1 (2016-12-30)
### Added
* Added configuration option to disable data collection

## 0.3.0 (2016-12-19)
### Changed
* Query logging now uses events (#28)

## 0.2.2 (2016-12-19)
### Added
* Added `LoadFixturesCommand` for loading fixtures (#27)

## 0.2.1 (2016-10-11)
### Changed
* Collapsible elements in the profiler now have dedicated button to avoid UX issues (#15, #25)

## 0.2.0 (2016-10-07)
### Added
* Added support for fixtures and indexes loading (#12, #21, #23)
### Changed
* Replaced XML configuration files with bundle extension (#17)
* Refactored MongoLogEventSerializer (#18)
* Mark `Capsule` class as `@internal` (#19)
### Fixed
* Fix web profiler (#22)

## 0.1.5 (2016-09-10)
### Fixed
 * Remove logger from client in prod environment (#13)

## 0.1.4 (2016-09-08)
### Added
* Aggregate collection method profiled
* Created MongoLogEventSerializer
### Changed
* Refactored LogEvent DataCollector preSerialization 

## 0.1.3 (2016-09-08)
## Changed
 * Collection updateOne method is now profiled (#10)
## Fixed
 * Solves profiler serialization problem with nested objects

## 0.1.2 (2016-09-02)
### Changed
 * Commands that extend AbstractCommand now trow exception if specified connection does not exist

## 0.1.1 (2016-09-01)
### Changed
* Improved profiler panel layout - added options clickable info

## 0.1 (2016-09-01)
First unstable release

## 0.1-alpha (2016-06-30)
First release

[Unreleased]: https://github.com/facile-it/mongodb-bundle/compare/1.6.0..master
[1.6.0]: https://github.com/facile-it/mongodb-bundle/compare/1.5.0..1.6.0
[1.5.0]: https://github.com/facile-it/mongodb-bundle/compare/1.4.0..1.5.0
[1.4.0]: https://github.com/facile-it/mongodb-bundle/compare/1.3.0..1.4.0
[1.3.0]: https://github.com/facile-it/mongodb-bundle/compare/1.2.0..1.3.0
[1.2.0]: https://github.com/facile-it/mongodb-bundle/compare/1.1.0..1.2.0
[1.1.0]: https://github.com/facile-it/mongodb-bundle/compare/1.0.0..1.1.0
[1.0.0]: https://github.com/facile-it/mongodb-bundle/compare/0.7.3..1.0.0
[0.7.3]: https://github.com/facile-it/mongodb-bundle/compare/0.7.2..0.7.3
[0.7.2]: https://github.com/facile-it/mongodb-bundle/compare/0.7.1..0.7.2
[0.7.1]: https://github.com/facile-it/mongodb-bundle/compare/0.7.0..0.7.1
[0.7.0]: https://github.com/facile-it/mongodb-bundle/compare/0.6.7..0.7.0
[0.6.7]: https://github.com/facile-it/mongodb-bundle/compare/0.6.6..0.6.7
[0.6.6]: https://github.com/facile-it/mongodb-bundle/compare/0.6.5..0.6.6
[0.6.5]: https://github.com/facile-it/mongodb-bundle/compare/0.6.4..0.6.5
[0.6.4]: https://github.com/facile-it/mongodb-bundle/compare/0.6.3..0.6.4
[0.6.3]: https://github.com/facile-it/mongodb-bundle/compare/0.6.2..0.6.3
[0.6.2]: https://github.com/facile-it/mongodb-bundle/compare/0.6.1..0.6.2
[0.6.1]: https://github.com/facile-it/mongodb-bundle/compare/0.6.0..0.6.1
[0.6.0]: https://github.com/facile-it/mongodb-bundle/compare/0.5.0..0.6.0
