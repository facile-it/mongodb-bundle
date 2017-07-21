<?php declare(strict_types=1);

namespace Facile\MongoDbBundle\Tests\Unit\Twig;

use Facile\MongoDbBundle\Twig\FacileMongoDbBundleExtension;
use PHPUnit\Framework\TestCase;

class FacileMongoDbBundleExtensionTest extends TestCase
{
    /**
     * @param string $label
     * @param string $methodname
     * @param string $expected
     *
     * @dataProvider labelMethodProvider
     */
    public function test_queryDataTranslate(string $label, string $methodname, string $expected)
    {
        $ext = new FacileMongoDbBundleExtension();
        $this->assertEquals($expected, $ext->queryDataTranslate($label, $methodname));
    }

    public function test_queryFilterTranslate()
    {
        $ext = new FacileMongoDbBundleExtension();
        $this->assertEquals('label', $ext->queryFilterTranslate('label', ''));
        $this->assertEquals('label1', $ext->queryFilterTranslate('label1', ''));
        $this->assertEquals('label2', $ext->queryFilterTranslate('label2', ''));
    }

    public function labelMethodProvider(): array
    {
        return [
            ['filters', 'aggregate', 'Pipeline'],
            ['filters', 'insertOne', 'Document'],
            ['filters', 'updateOne', 'Update'],
            ['filters', 'findOneAndUpdate', 'Update'],
            ['filters', 'replaceOne', 'Replacement'],
            ['filters', 'find', 'filters'],
        ];
    }
}