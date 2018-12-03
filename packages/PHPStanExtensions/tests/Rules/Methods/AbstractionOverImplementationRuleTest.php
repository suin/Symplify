<?php declare(strict_types=1);

namespace Symplify\PHPStanExtensions\Tests\Rules\Methods;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanExtensions\Rules\Methods\AbstractionOverImplementationRule;

final class AbstractionOverImplementationRuleTest extends RuleTestCase
{
    public function testRule(): void
    {
        $this->analyse(
            [__DIR__ . '/Source/ClassWithImplementations.php'],
            [
                [
                    'You should use interface "Psr\Container\ContainerInterface" instead of "Symfony\Component\DependencyInjection\Container" class as a typehint for "$container" argument',
                    20,
                ],
                [
                    'You should use interface "Psr\Http\Message\RequestInterface" instead of "GuzzleHttp\Psr7\Request" class as a typehint for "$request" argument',
                    20,
                ],
            ]
        );
    }

    protected function getRule(): Rule
    {
        return new AbstractionOverImplementationRule($this->createBroker());
    }
}
