<?php declare(strict_types=1);

namespace Symplify\Autodiscovery\Yaml;

use Nette\Utils\Strings;
use ReflectionClass;
use Symfony\Component\Filesystem\Filesystem;

final class ExplicitToAutodiscoveryConverter
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * @param mixed[] $yaml
     * @return mixed[]
     */
    public function convert(array $yaml, string $filePath): array
    {
        // nothing to change
        if (! isset($yaml['services'])) {
            return $yaml;
        }

        $services = [];
        foreach ($yaml['services'] as $name => $service) {
            // anonymous service
            if ($service === null) {
                $services[] = $name;
                unset($yaml['services'][$name]);
            }
            // @todo
        }

        // determine longest common namespace
        $servicesByNamespace = [];
        foreach ($services as $service) {
            // @todo better solve
            $namespace = Strings::before($service, '\\', -1);
            $servicesByNamespace[$namespace][] = $service;
        }

        foreach ($servicesByNamespace as $namespace => $services) {
            $yaml['services'][$namespace . '\\'] = [
                'resource' => $this->getRelativeClassLocation($services[0], $filePath),
            ];
        }

        return $yaml;
    }

    private function getRelativeClassLocation(string $class, string $configFilePath): string
    {
        $reflectionClass = new ReflectionClass($class);

        $classDirectory = dirname($reflectionClass->getFileName());
        $configDirectory = dirname($configFilePath);

        $relativePath = $this->filesystem->makePathRelative($classDirectory, $configDirectory);

        return rtrim($relativePath, '/');
    }
}
