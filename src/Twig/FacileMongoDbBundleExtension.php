<?php declare(strict_types = 1);

namespace Facile\MongoDbBundle\Twig;

class FacileMongoDbBundleExtension extends \Twig_Extension
{
    public function getFunctions()
    {
        return array(
            new \Twig_Simplefunction('filterLabelTranslate', array($this, 'queryFilterTranslate')),
            new \Twig_Simplefunction('dataLabelTranslate', array($this, 'queryDataTranslate')),
        );
    }

    /**
     * @param string $label
     * @param string $methodName
     *
     * @return string
     */
    public function queryFilterTranslate(string $label, string $methodName)
    {
        switch(strtolower($methodName)) {
            default:
                return $label;
        }
    }

    /**
     * @param string $label
     * @param string $methodName
     *
     * @return string
     */
    public function queryDataTranslate(string $label, string $methodName)
    {
        switch(strtolower($methodName)) {
            case 'aggregate':
                return 'Pipeline';
            case 'insertOne':
                return 'Document';
            case 'updateOne':
            case 'findOneAndUpdate':
                return 'Update';
            case 'replaceOne':
                return 'Replacement';
            default:
                return $label;
        }
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