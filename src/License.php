<?php

declare(strict_types=1);

/*
 * This file is part of the gilbertsoft/coding-standards package.
 *
 * (c) Gilbertsoft <gilbertsoft.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

namespace Gilbertsoft\CodingStandards;

use InvalidArgumentException;
use RuntimeException;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Style\StyleInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TYPO3\CodingStandards\Setup;

use function is_dir;
use function rtrim;

use const DIRECTORY_SEPARATOR;

final class License
{
    /**
     * @var string
     */
    public const MIT = 'MIT';

    /**
     * @var string
     */
    public const GPL_3_0_OR_LATER = 'GPL-3.0-or-later';

    /**
     * @var string
     */
    public const TYPO3_MIT = 'TYPO3-MIT';

    /**
     * @var string
     */
    public const TYPO3_GPL_3_0_OR_LATER = 'TYPO3-GPL-3.0-or-later';

    /**
     * @var string[]
     */
    public const LICENSES = [
       self::MIT,
       self::GPL_3_0_OR_LATER,
       self::TYPO3_MIT,
       self::TYPO3_GPL_3_0_OR_LATER,
    ];

    /**
     * @var string
     */
    private const TYPO3_SHARE = 'The TYPO3 project - inspiring people to share!';

    /**
     * @var string
     */
    private const TARGET_FILE_NAME = 'LICENSE.md';

    private readonly string $targetDir;

    private readonly Setup $setup;

    private readonly StyleInterface $style;

    public static function getHeader(string $package, string $license): string
    {
        return match ($license) {
            self::MIT => str_replace(
                '{package}',
                $package,
                <<<'EOH'
                This file is part of the {package} package.

                (c) Gilbertsoft <gilbertsoft.org>

                For the full copyright and license information, please view the LICENSE
                file that was distributed with this source code.
                EOH
            ),
            self::GPL_3_0_OR_LATER => str_replace(
                '{package}',
                $package,
                <<<'EOH'
                This file is part of the {package} package.

                Copyright (C) 2023  Gilbertsoft <gilbertsoft.org>

                This program is free software: you can redistribute it and/or modify
                it under the terms of the GNU General Public License as published by
                the Free Software Foundation, either version 3 of the License, or
                (at your option) any later version.

                This program is distributed in the hope that it will be useful,
                but WITHOUT ANY WARRANTY; without even the implied warranty of
                MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
                GNU General Public License for more details.

                You should have received a copy of the GNU General Public License
                along with this program.  If not, see <https://www.gnu.org/licenses/>.
                EOH
            ),
            self::TYPO3_MIT => self::getHeader($package, self::MIT) . "\n\n" . self::TYPO3_SHARE,
            self::TYPO3_GPL_3_0_OR_LATER => self::getHeader($package, self::GPL_3_0_OR_LATER) .
                "\n\n" . self::TYPO3_SHARE,
            default => throw new InvalidArgumentException(sprintf('Invalid license %s.', $license), 1_660_299_465),
        };
    }

    public static function getLicenseTemplate(string $license): string
    {
        return match ($license) {
            self::MIT, self::TYPO3_MIT => 'LICENSE_MIT.md',
            self::GPL_3_0_OR_LATER, self::TYPO3_GPL_3_0_OR_LATER => 'LICENSE_GPL_3_0_OR_LATER.md',
            default => throw new InvalidArgumentException(sprintf('Invalid license %s.', $license), 1_660_299_465),
        };
    }

    /**
     * @param array<int, string> $templatesDirs
     *
     * @throws RuntimeException
     */
    public function __construct(
        string $targetDir = '',
        array $templatesDirs = [],
        StyleInterface $style = null,
    ) {
        // Setup targetDir
        if ($targetDir === '') {
            $targetDir = '.';
        }

        if (!is_dir($targetDir)) {
            throw new RuntimeException(sprintf("Target directory '%s' does not exist.", $targetDir));
        }

        // Normalize separators on Windows
        if ('\\' === DIRECTORY_SEPARATOR) {
            $targetDir = \str_replace('\\', '/', $targetDir); // @codeCoverageIgnore
        }

        $this->targetDir = rtrim($targetDir, '/');

        // Setup StyleInterface
        if (!$style instanceof StyleInterface) {
            $arrayInput = new ArrayInput([]);
            $arrayInput->setInteractive(false);
            $nullOutput = new NullOutput();
            $style = new SymfonyStyle($arrayInput, $nullOutput);
        }

        $this->style = $style;
        $this->setup = new Setup($style, $targetDir, $templatesDirs);
    }

    public function copy(bool $force, string $license): bool
    {
        $targetFilePath = $this->targetDir . '/' . self::TARGET_FILE_NAME;

        if (!$force && file_exists($targetFilePath)) {
            $this->style->error(\sprintf(
                'A %s file already exists, nothing copied. Use the --force option to overwrite the file.',
                self::TARGET_FILE_NAME
            ));
            return false;
        }

        copy(
            $this->setup->getTemplateFilePath(self::getLicenseTemplate($license)),
            $targetFilePath
        );
        $this->style->success(\sprintf('%s created.', self::TARGET_FILE_NAME));

        return true;
    }
}
