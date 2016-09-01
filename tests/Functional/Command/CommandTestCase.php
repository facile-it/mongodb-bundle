<?php

declare(strict_types=1);

namespace Facile\MongoDbBundle\Tests\Functional\Command;

use Facile\MongoDbBundle\Tests\Functional\TestApp\TestKernel;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class CommandTestCase.
 */
class CommandTestCase extends \PHPUnit_Framework_TestCase
{
    /** @var  Application */
    private $application;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $kernel = new TestKernel('test', true);
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
}
