<?php declare(strict_types=1);

namespace Symplify\PHPStanExtensions\Tests\Rules\Methods\Source;

use Symfony\Component\DependencyInjection\Container;

final class ClassWithImplementations
{
    /**
     * @var Container
     */
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function getContainer(): Container
    {
        return $this->container;
    }
}
