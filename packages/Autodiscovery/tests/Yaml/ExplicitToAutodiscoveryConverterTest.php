<?php declare(strict_types=1);

namespace Symplify\Autodiscovery\Tests\Yaml;

use Nette\Utils\FileSystem;
use Nette\Utils\Strings;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;
use Symplify\Autodiscovery\Yaml\ExplicitToAutodiscoveryConverter;

final class ExplicitToAutodiscoveryConverterTest extends TestCase
{
    /**
     * @var ExplicitToAutodiscoveryConverter
     */
    private $explicitToAutodiscoveryConverter;

    protected function setUp(): void
    {
        $this->explicitToAutodiscoveryConverter = new ExplicitToAutodiscoveryConverter();
    }

    public function test(): void
    {
        $this->doTestFile(__DIR__ . '/Fixture/first.yaml');
    }

    private function doTestFile(string $file): void
    {
        $yamlContent = FileSystem::read($file);

        [$originalYamlContent, $expectedYamlContent] = Strings::split($yamlContent, "#\-\-\-\n#");
        $originalYaml = Yaml::parse($originalYamlContent);
        $expectedYaml = Yaml::parse($expectedYamlContent);

        $this->assertSame($expectedYaml, $this->explicitToAutodiscoveryConverter->convert($originalYaml));
    }
}
