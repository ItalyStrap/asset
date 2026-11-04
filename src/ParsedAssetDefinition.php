<?php

declare(strict_types=1);

namespace ItalyStrap\Asset;

use InvalidArgumentException;

use function array_key_exists;
use function sprintf;

final readonly class ParsedAssetDefinition
{
    private function __construct(private array $config)
    {
    }

    public static function fromArray(array $config): self
    {
        foreach (self::requiredKeys() as $key) {
            if (! array_key_exists($key, $config)) {
                throw new InvalidArgumentException(sprintf(
                    'Missing parsed asset key "%s"',
                    $key
                ));
            }
        }

        return new self($config);
    }

    /**
     * @return array<string>
     */
    private static function requiredKeys(): array
    {
        return [
            Asset::HANDLE,
            ConfigBuilder::FILE_NAME,
            Asset::URL,
            Asset::VERSION,
            Asset::SHOULD_LOAD,
            Asset::DEPENDENCIES,
            Asset::IN_FOOTER,
            Asset::TYPE,
            // Downstream consumers still read these normalized aliases from the parsed config.
            'enqueue',
            'dependencies',
            'inFooter',
        ];
    }

    public function toArray(): array
    {
        return $this->config;
    }
}
