<?php

declare(strict_types=1);

namespace Facile\MongoDbBundle\Exceptions;

/**
 * Class ExistantClientException.
 */
class ExistantClientException extends \Exception
{
    /**
     * ExistantClientException constructor.
     *
     * @param string $message
     * @param int    $code
     * @param null   $previousException
     */
    public function __construct($message, $code = 0, $previousException = null)
    {
        parent::__construct($message, $code, $previousException);
    }
}
