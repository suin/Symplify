<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder;

use Symfony\Component\Finder\Finder;
use Symplify\PackageBuilder\FileSystem\FinderSanitizer;
use Symplify\PackageBuilder\FileSystem\SmartFileInfo;

final class PackageComposerFinder
{
    /**
     * @var string[]
     */
    private $packageDirectories = [];

    /**
     * @var FinderSanitizer
     */
    private $finderSanitizer;

    /**
     * @param string[] $packageDirectories
     */
    public function __construct(array $packageDirectories, FinderSanitizer $finderSanitizer)
    {
        $this->packageDirectories = $packageDirectories;
        $this->finderSanitizer = $finderSanitizer;
    }

    public function getRootPackageComposerFile(): SmartFileInfo
    {
        return new SmartFileInfo('composer.json');
    }

    /**
     * @return SmartFileInfo[]
     */
    public function getPackageComposerFiles(): array
    {
        $finder = Finder::create()
            ->files()
            ->in($this->packageDirectories)
            ->exclude('templates') // "init" command template data
            ->name('composer.json');

        if (! $this->isPHPUnit()) {
            $finder->notPath('#tests#');
        }

        return $this->finderSanitizer->sanitize($finder);
    }

    private function isPHPUnit(): bool
    {
        // defined by PHPUnit
        return defined('PHPUNIT_COMPOSER_INSTALL') || defined('__PHPUNIT_PHAR__');
    }
}
