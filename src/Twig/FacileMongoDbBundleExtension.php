<?php declare(strict_types = 1);

namespace Facile\MongoDbBundle\Twig;

use Facile\MongoDbBundle\Services\Explain\ExplainQueryService;

class FacileMongoDbBundleExtension extends \Twig_Extension
{
    private $methodDataTranslationMap  = [
        'aggregate' => 'Pipeline',
        'insertOne' => 'Document',
        'updateOne' => 'Update',
        'findOneAndUpdate' => 'Update',
        'replaceOne' => 'Replacement',
    ];

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_Simplefunction('filterLabelTranslate', array($this, 'queryFilterTranslate')),
            new \Twig_Simplefunction('dataLabelTranslate', array($this, 'queryDataTranslate')),
            new \Twig_Simplefunction('isQueryExplainable', array($this, 'isQueryExplainable')),
        ];
    }

    /**
     * @param string $label
     * @param string $methodName
     *
     * @return string
     */
    public function queryFilterTranslate(string $label, string $methodName): string
    {
        return $label;
    }

    /**
     * @param string $label
     * @param string $methodName
     *
     * @return string
     */
    public function queryDataTranslate(string $label, string $methodName): string
    {
        return $this->methodDataTranslationMap[$methodName] ?? $label;
    }

    public function isQueryExplainable(string $methodName): bool
    {
        return in_array($methodName, ExplainQueryService::$acceptedMethods);
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'facile_mongo_db_extesion';
    }
}