<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\Strict\BlankLineAfterStrictTypesFixer;

use Symplify\EasyCodingStandard\Testing\AbstractContainerAwareCheckerTestCase;

/**
 * @see \Symplify\CodingStandard\Fixer\Strict\BlankLineAfterStrictTypesFixer
 */
final class BlankLineAfterStrictTypesFixerTest extends AbstractContainerAwareCheckerTestCase
{
    /**
     * @dataProvider provideCorrectCases()
     */
    public function testCorrectCases(string $file): void
    {
        $this->doTestCorrectFile($file);
    }

    /**
     * @return string[][]
     */
    public function provideCorrectCases(): array
    {
        return [
            [__DIR__ . '/correct/correct.php.inc'],
        ];
    }

    /**
     * @dataProvider provideWrongToFixedCases()
     */
    public function testFix(string $wrongFile, string $fixedFile): void
    {
        $this->doTestWrongToFixedFile($wrongFile, $fixedFile);
    }

    /**
     * @return string[][]
     */
    public function provideWrongToFixedCases(): array
    {
        return [
            [__DIR__ . '/wrong/wrong.php.inc', __DIR__ . '/fixed/fixed.php.inc'],
            [__DIR__ . '/wrong/wrong2.php.inc', __DIR__ . '/fixed/fixed.php.inc'],
        ];
    }

    protected function provideConfig(): string
    {
        return __DIR__ . '/config.yml';
    }
}
