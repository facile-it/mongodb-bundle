<?php

declare(strict_types=1);

namespace Facile\MongoDbBundle\Tests\Functional;

use Facile\MongoDbBundle\Tests\Functional\TestApp\TestKernel;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AppTestCase extends TestCase
{
    private ?Application $application = null;

    private string $env = 'test';

    protected function setUp(): void
    {
        parent::setUp();

        $kernel = new TestKernel($this->env, true);
        $kernel->boot();
        $this->application = new Application($kernel);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->application = null;
    }

    protected function getApplication(): Application
    {
        return $this->application;
    }

    protected function getContainer(): ContainerInterface
    {
        return $this->application->getKernel()->getContainer();
    }

    protected function setEnvDev()
    {
        $this->env = 'dev';
    }
}
