<?php

declare(strict_types=1);

namespace Facile\MongoDbBundle\Tests\Functional\TestApp;

use MongoDB\Database;
use Symfony\Component\HttpFoundation\Response;

class MainController
{
    public function __construct(private readonly Database $database) {}

    public function noop(): Response
    {
        return new Response('Hello there');
    }

    public function triggerQuery(): Response
    {
        $this->database->selectCollection('test_collection')
            ->insertOne(['foo' => 'bar']);

        return new Response('Hello there');
    }
}
