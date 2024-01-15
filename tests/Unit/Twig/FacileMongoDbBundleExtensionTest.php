<?php

declare(strict_types=1);

namespace Facile\MongoDbBundle\Tests\Unit\Twig;

use Facile\MongoDbBundle\Twig\FacileMongoDbBundleExtension;
use PHPUnit\Framework\TestCase;

class FacileMongoDbBundleExtensionTest extends TestCase
{
    /**
     * @dataProvider labelMethodProvider
     */
    public function test_queryDataTranslate(string $label, string $methodname, string $expected)
    {
        $ext = new FacileMongoDbBundleExtension();
        $this->assertEquals($expected, $ext->queryDataTranslate($label, $methodname));
    }

    /**
     * @dataProvider explainMethodsProvider
     */
    public function test_isQueryExplainable(string $methodname, bool $expected)
    {
        $ext = new FacileMongoDbBundleExtension();
        $this->assertEquals($expected, $ext->isQueryExplainable($methodname));
    }

    public function test_get_name()
    {
        $ext = new FacileMongoDbBundleExtension();
        $this->assertEquals('facile_mongo_db_extesion', $ext->getName());
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

    public function explainMethodsProvider(): array
    {
        return [
            ['aggregate', true],
            ['count', true],
            ['distinct', true],
            ['find', true],
            ['findOne', true],
            ['findOneAndUpdate', true],
            ['findOneAndDelete', true],
            ['deleteOne', true],
            ['deleteMany', true],
            ['updateOne', false],
            ['insertOne', false],
            ['replaceOne', false],
        ];
    }
}
