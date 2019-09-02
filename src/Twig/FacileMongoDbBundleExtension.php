<?php

declare(strict_types=1);

namespace Facile\MongoDbBundle\Twig;

use Facile\MongoDbBundle\Services\Explain\ExplainQueryService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

if (! class_exists('\Twig\Extension\AbstractExtension')) {
    class_alias(\Twig_Extension::class, '\Twig\Extension\AbstractExtension');
}

if (! class_exists('\Twig\TwigFunction')) {
    class_alias(\Twig_Function::class, '\Twig\TwigFunction');
}

class FacileMongoDbBundleExtension extends AbstractExtension
{
    private const METHOD_DATA_TRANSATION_MAP = [
        'aggregate' => 'Pipeline',
        'insertOne' => 'Document',
        'updateOne' => 'Update',
        'findOneAndUpdate' => 'Update',
        'replaceOne' => 'Replacement',
    ];

    /**
     * @return \Twig_SimpleFunction[]
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('dataLabelTranslate', [$this, 'queryDataTranslate']),
            new TwigFunction('isQueryExplainable', [$this, 'isQueryExplainable']),
        ];
    }

    public function queryDataTranslate(string $label, string $methodName): string
    {
        return self::METHOD_DATA_TRANSATION_MAP[$methodName] ?? $label;
    }

    public function isQueryExplainable(string $methodName): bool
    {
        return \in_array($methodName, ExplainQueryService::ACCEPTED_METHODS, true);
    }

    public function getName(): string
    {
        return 'facile_mongo_db_extesion';
    }
}
