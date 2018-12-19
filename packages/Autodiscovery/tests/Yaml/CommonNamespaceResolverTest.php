<?php declare(strict_types=1);

namespace Symplify\Autodiscovery\Tests\Yaml;

use Iterator;
use PHPUnit\Framework\TestCase;
use Symplify\Autodiscovery\Yaml\CommonNamespaceResolver;

final class CommonNamespaceResolverTest extends TestCase
{
    /**
     * @var CommonNamespaceResolver
     */
    private $commonNamespaceResolver;

    protected function setUp(): void
    {
        $this->commonNamespaceResolver = new CommonNamespaceResolver();
    }

    /**
     * @param string[] $classes
     * @dataProvider provideData()
     */
    public function test(array $classes, ?string $expectedCommonNamespace): void
    {
        $this->assertSame($expectedCommonNamespace, $this->commonNamespaceResolver->resolve($classes));
    }

    public function provideData(): Iterator
    {
        yield [['App\FirstClass', 'App\AnotherClass'], 'App'];
        yield [['App\Wohoo\FirstClass', 'App\Wohoo\AnotherClass'], 'App\Wohoo'];
        yield [
            [
                'Shopsys\FrameworkBundle\Model\Payment\PaymentRepository',
                'Shopsys\FrameworkBundle\Component\DataFixture\PersistentReferenceFacade',
                'Shopsys\FrameworkBundle\Model\AdvancedSearch\ProductAdvancedSearchConfig',
            ],
            'Shopsys\FrameworkBundle',
        ];
    }
}
