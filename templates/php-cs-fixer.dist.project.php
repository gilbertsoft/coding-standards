<?php

declare(strict_types=1);

use Gilbertsoft\CodingStandards\CsFixerConfig;
use Gilbertsoft\CodingStandards\License;

$config = CsFixerConfig::create();
$config
    ->setLicenseHeader('vendor/package', License::MIT)
    ->getFinder()
    ->exclude(__DIR__ . '/tools')
    ->in(__DIR__)
;

return $config;
