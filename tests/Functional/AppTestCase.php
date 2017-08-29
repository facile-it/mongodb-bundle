<?php

declare(strict_types=1);

namespace Facile\MongoDbBundle\Tests\Functional;

use Facile\MongoDbBundle\Tests\Functional\TestApp\TestKernel;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class AppTestCase.
 */
class AppTestCase extends TestCase
{
    /** @var  Application */
    private $application;

    private $env = 'test';

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $kernel = new TestKernel($this->env, true);
        $kernel->boot();
        $this->application = new Application($kernel);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
        $this->application = null;
    }

    /**
     * @return Application
     */
    protected function getApplication(): Application
    {
        return $this->application;
    }

    /**
     * @return ContainerInterface
     */
    protected function getContainer(): ContainerInterface
    {
        return $this->application->getKernel()->getContainer();
    }

    protected function setEnvDev()
    {
        $this->env = 'dev';
    }
}
