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

use Gilbertsoft\CodingStandards\CsFixerConfig;
use Gilbertsoft\CodingStandards\License;

$config = CsFixerConfig::create();
$config
    ->setLicenseHeader('gilbertsoft/coding-standards', License::TYPO3_MIT)
    ->addRules([
        'fully_qualified_strict_types' => true,
    ])
    ->getFinder()
    ->in(__DIR__ . '/src')
    ->in(__DIR__ . '/tests')
    ->append([__DIR__ . '/.php-cs-fixer.dist.php'])
    ->append([__DIR__ . '/rector.php'])
;

return $config;
