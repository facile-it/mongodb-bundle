<?php declare(strict_types=1);

namespace Facile\MongoDbBundle\Models;

/**
 * Class ClientConfiguration.
 * @internal
 */
final class ClientConfiguration
{
    /** @var string */
    private $uri;
    /** @var array */
    private $options;

    /**
     * ClientConfiguration constructor.
     *
     * @param string $uri
     * @param array $options
     */
    public function __construct(
        string $uri,
        array $options = []
    ) {
        $this->uri = $uri;
        $this->options = $options;
    }

    /**
     * @return string
     */
    public function getUri(): string
    {
        return $this->uri;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->cleanOptions($this->options);
    }

    /**
     * @param array $options
     *
     * @return array
     */
    private function cleanOptions(array $options): array
    {
        return array_filter(
            $options,
            function ($value) {
                return ! empty($value) || \is_int($value) || \is_bool($value) || \is_float($value);
            }
        );
    }
}
