<?php declare(strict_types=1);

namespace Facile\MongoDbBundle\Services;

/**
 * Class DriverOptionsFactory.
 * @internal
 */
final class DriverOptionsFactory implements DriverOptionsInterface
{
    const ALLOWED_CONTEXT_OPTIONS = ["cafile","capath", "allow_self_signed", "passphrase", "local_cert"];

    /**
     * @param array $configuration
     * @return array
     */
    public function buildDriverOptions(array $configuration): array
    {
        if (isset($configuration['driverOptions'])) {
            foreach ($configuration['driverOptions'] as $key => $option) {
                if ($key == 'context') {
                    $configuration['driverOptions'][$key] = $this->buildContext($option);
                }
            }
        }

        return $configuration['driverOptions'] ?? [];
    }

    /**
     * @param array $contextOptions
     * @return resource
     */
    public function buildContext(array $contextOptions)
    {
        $context = '';

        if (!empty($contextOptions)) {
            $contextAux = [];
            foreach ($this::ALLOWED_CONTEXT_OPTIONS as $option) {
                if (isset($contextOptions[$option])) {
                    $contextAux[$option] = $contextOptions[$option];
                }
            }

            $context = stream_context_create(["ssl" => $contextAux]);
        }

        return $context;
    }
}
