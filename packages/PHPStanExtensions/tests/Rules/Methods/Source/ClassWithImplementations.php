<?php declare(strict_types=1);

namespace Symplify\PHPStanExtensions\Tests\Rules\Methods\Source;

use GuzzleHttp\Psr7\Request;
use Symfony\Component\DependencyInjection\Container;

final class ClassWithImplementations
{
    /**
     * @var Container
     */
    private $container;

    /**
     * @var Request|null
     */
    private $request;

    public function __construct(Container $container, ?Request $request)
    {
        $this->container = $container;
        $this->request = $request;
    }

    public function getRequest(): ?Request
    {
        return $this->request;
    }

    public function getContainer(): Container
    {
        return $this->container;
    }
}
