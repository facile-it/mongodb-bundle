<?php

declare(strict_types=1);

namespace Facile\MongoDbBundle\Tests\Functional\Controller;

use Facile\MongoDbBundle\Tests\Functional\TestApp\TestKernelWithProfiler;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;

class ProfilerControllerTest extends WebTestCase
{
    use ProphecyTrait;

    protected static function getKernelClass(): string
    {
        return TestKernelWithProfiler::class;
    }

    protected function setUp(): void
    {
        $this->cleanUpDir(__DIR__ . '/../../../var/cache');
        parent::setUp();
    }

    public function test_explainAction(): void
    {
        $client = self::createClient();

        $client->request('GET', '/trigger_query');
        $this->assertResponseIsSuccessful();
        $crawler = $client->request('GET', '/_profiler/latest?panel=mongodb');

        $this->assertResponseIsSuccessful();
        $this->assertHeadersArePresent($crawler, 'http://localhost/trigger_query');
        $explainTable = $crawler->filterXPath('//table[2]');
        $this->assertCrawlerTextContainsString('insertOne', $explainTable);
        $this->assertCrawlerTextContainsString('test_collection', $explainTable);
        $this->assertCrawlerTextContainsString('{ "foo": "bar" }', $explainTable);
    }

    public function test_explainAction_error(): void
    {
        $client = self::createClient();

        $client->request('GET', '/noop');
        $this->assertResponseIsSuccessful();
        $crawler = $client->request('GET', '/_profiler/latest?panel=mongodb');
        $this->assertResponseIsSuccessful();

        $this->assertHeadersArePresent($crawler, 'http://localhost/noop');
        $explainTable = $crawler->filterXPath('//table[2]');
        $this->assertCrawlerTextContainsString('No queries', $explainTable);
    }

    private function cleanUpDir(string $dir): bool
    {
        if (! file_exists($dir)) {
            return false;
        }

        $it = new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new \RecursiveIteratorIterator($it, \RecursiveIteratorIterator::CHILD_FIRST);

        foreach ($files as $file) {
            if ($file->isDir()) {
                $this->cleanUpDir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }

        return rmdir($dir);
    }

    private function assertHeadersArePresent(Crawler $crawler, string $expectedTitle): void
    {
        $this->assertCrawlerTextContainsString($expectedTitle, $crawler->filterXPath('//h2[1]'));
        $this->assertCrawlerTextContainsString('Mongo DB Query Metrics', $crawler->filterXPath('//h2[2]'));
        $this->assertCrawlerTextContainsString('Connections list', $crawler->filterXPath('//h2[3]'));
        $this->assertCrawlerTextContainsString('Queries Detail', $crawler->filterXPath('//h2[4]'));
        $this->assertCrawlerTextContainsString('test_client.testFunctionaldb', $crawler->filterXPath('//table[1]'));
    }

    private function assertCrawlerTextContainsString(string $needle, Crawler $explainTable): void
    {
        // silence 4.4 deprecation about whitespace normalization
        $this->assertStringContainsString($needle, $explainTable->text('', true));
    }
}
