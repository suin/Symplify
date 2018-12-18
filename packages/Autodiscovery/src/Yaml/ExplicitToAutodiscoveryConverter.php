<?php declare(strict_types=1);

namespace Symplify\Autodiscovery\Yaml;

use Nette\Utils\Strings;

final class ExplicitToAutodiscoveryConverter
{
    /**
     * @param mixed[] $yaml
     * @return mixed[]
     */
    public function convert(array $yaml): array
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
        $namespaces = [];
        foreach ($services as $service) {
            $namespaces[] = Strings::before($service, '\\', -1);
        }

        $uniqueNamespaces = array_unique($namespaces);

        // 1 namespace
        if (count($uniqueNamespaces) === 1) {
            $yaml['services'][$uniqueNamespaces[0] . '\\'] = [
                'resource' => '../src', # assumption
            ];
        }

        return $yaml;
    }
}
