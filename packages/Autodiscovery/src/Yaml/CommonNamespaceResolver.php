<?php declare(strict_types=1);

namespace Symplify\Autodiscovery\Yaml;

use Nette\Utils\Strings;

final class CommonNamespaceResolver
{
    /**
     * @param string[] $classes
     */
    public function resolve(array $classes): ?string
    {
        $namespace = null;

        $namespaceNestingLevel = substr_count($classes[0], '\\');
        for ($i = 1; $i <= $namespaceNestingLevel; ++$i) {
            foreach ($classes as $class) {
                $namespace = Strings::before($class, '\\', $i);

                foreach ($classes as $classAgain) {
                    if (! Strings::startsWith($classAgain, $namespace . '\\')) {
                        return $namespace;
                    }
                }
            }
        }

        return $namespace;
    }
}
