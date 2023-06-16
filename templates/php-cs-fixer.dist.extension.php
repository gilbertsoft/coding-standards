<?php

declare(strict_types=1);

use Gilbertsoft\CodingStandards\CsFixerConfig;
use Gilbertsoft\CodingStandards\License;

$config = CsFixerConfig::create();
$config
    ->setLicenseHeader('vendor/package', License::TYPO3_GPL_3_0_OR_LATER)
    ->getFinder()
    ->exclude(__DIR__ . '/tools')
    ->in(__DIR__)
;

return $config;
