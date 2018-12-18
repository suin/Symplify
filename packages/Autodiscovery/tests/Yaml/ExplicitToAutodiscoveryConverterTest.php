<?php declare(strict_types=1);

namespace Symplify\Autodiscovery\Tests\Yaml;

use Nette\Utils\FileSystem;
use Nette\Utils\Strings;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem as SymfonyFilesystem;
use Symfony\Component\Yaml\Yaml;
use Symplify\Autodiscovery\Yaml\ExplicitToAutodiscoveryConverter;

final class ExplicitToAutodiscoveryConverterTest extends TestCase
{
    /**
     * @var string
     */
    private const SPLIT_PATTERN = "#\-\-\-\n#";

    /**
     * @var ExplicitToAutodiscoveryConverter
     */
    private $explicitToAutodiscoveryConverter;

    protected function setUp(): void
    {
        $this->explicitToAutodiscoveryConverter = new ExplicitToAutodiscoveryConverter(new SymfonyFilesystem());
    }

    public function test(): void
    {
        $this->doTestFile(__DIR__ . '/Fixture/first.yaml');
//        $this->doTestFile(__DIR__ . '/Fixture/tags_with_values.yaml');
    }

    private function doTestFile(string $file): void
    {
        $yamlContent = FileSystem::read($file);

        [$originalYamlContent, $expectedYamlContent] = $this->splitFile($yamlContent);

        $originalYaml = Yaml::parse($originalYamlContent);
        $expectedYaml = Yaml::parse($expectedYamlContent);

        $this->assertSame($expectedYaml, $this->explicitToAutodiscoveryConverter->convert($originalYaml, $file));
    }

    /**
     * @return string[]
     */
    private function splitFile(string $yamlContent): array
    {
        if (Strings::match($yamlContent, self::SPLIT_PATTERN)) {
            return Strings::split($yamlContent, self::SPLIT_PATTERN);
        }

        return [$yamlContent, $yamlContent];
    }
}
