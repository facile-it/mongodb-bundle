<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Facile\MongoDbBundle\Command\DropCollectionCommand;
use Facile\MongoDbBundle\Command\DropDatabaseCommand;
use Facile\MongoDbBundle\Command\LoadFixturesCommand;
use Symfony\Component\EventDispatcher\EventDispatcher;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('facile_mongo_db.event_dispatcher', EventDispatcher::class)
        ->private();

    $services->set('facile_mongo_db.command.drop_database', DropDatabaseCommand::class)
        ->args([service('service_container')])
        ->tag('console.command');

    $services->set('facile_mongo_db.command.drop_collection', DropCollectionCommand::class)
        ->args([service('service_container')])
        ->tag('console.command');

    $services->set('facile_mongo_db.command.load_fixtures', LoadFixturesCommand::class)
        ->args([service('service_container')])
        ->tag('console.command');
};
