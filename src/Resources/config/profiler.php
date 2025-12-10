<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Facile\MongoDbBundle\Controller\ProfilerController;
use Facile\MongoDbBundle\DataCollector\MongoDbDataCollector;
use Facile\MongoDbBundle\Event\Listener\DataCollectorListener;
use Facile\MongoDbBundle\Services\Explain\ExplainQueryService;
use Facile\MongoDbBundle\Services\Loggers\MongoQueryLogger;
use Facile\MongoDbBundle\Twig\FacileMongoDbBundleExtension;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('facile_mongo_db.logger', MongoQueryLogger::class)
        ->private();

    $services->set('facile_mongo_db.data_collector.listener', DataCollectorListener::class)
        ->private()
        ->args(['$logger' => service('facile_mongo_db.logger')]);

    $services->set('facile_mongo_db.data_collector', MongoDbDataCollector::class)
        ->private()
        ->call('setLogger', [service('facile_mongo_db.logger')])
        ->tag('data_collector', ['id' => 'mongodb', 'priority' => 250, 'template' => '@FacileMongoDb/Collector/mongo.html.twig']);

    $services->set('mongo.explain_query_service', ExplainQueryService::class)
        ->public()
        ->args([service('mongo.client_registry')]);

    $services->set('facile_mongo_db.twig_extension', FacileMongoDbBundleExtension::class)
        ->private()
        ->tag('twig.extension');

    $services->set(ProfilerController::class, ProfilerController::class)
        ->args([
            service('mongo.explain_query_service'),
            service('profiler')->nullOnInvalid(),
        ])
        ->tag('controller.service_arguments');
};
