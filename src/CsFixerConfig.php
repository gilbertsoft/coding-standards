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

use TYPO3\CodingStandards\CsFixerConfig as BaseCsFixerConfig;

use function array_replace_recursive;

final class CsFixerConfig extends BaseCsFixerConfig
{
    /**
     * @var array<string, mixed>
     */
    private const GILBERTSOFT_RULES = [
        '@PER' => true,
        'declare_strict_types' => true,
        //'fully_qualified_strict_types' => true, // conflicts currently with Rector
        'global_namespace_import' => [
            'import_classes' => true,
            'import_constants' => true,
            'import_functions' => true,
        ],
        'heredoc_indentation' => true,
        'heredoc_to_nowdoc' => true,
        'no_unneeded_import_alias' => true,
        'ordered_imports' => [
            'imports_order' => ['class', 'function', 'const'],
            'sort_algorithm' => 'alpha',
        ],
        'phpdoc_align' => true,
        'phpdoc_annotation_without_dot' => true,
        'phpdoc_indent' => true,
        'phpdoc_inline_tag_normalizer' => true,
        'phpdoc_line_span' => true,
        'phpdoc_no_useless_inheritdoc' => true,
        'phpdoc_order' => true,
        'phpdoc_order_by_value' => true,
        'phpdoc_separation' => true,
        'phpdoc_single_line_var_spacing' => true,
        'phpdoc_summary' => true,
        'phpdoc_tag_casing' => true,
        'phpdoc_tag_type' => true,
        'phpdoc_to_comment' => [
            'ignored_tags' => [
                'phpstan-ignore-line',
                'phpstan-ignore-next-line',
                'todo',
            ],
        ],
        'phpdoc_trim_consecutive_blank_line_separation' => true,
        'phpdoc_types_order' => [
            'null_adjustment' => 'always_last',
            'sort_algorithm' => 'alpha',
        ],
        'phpdoc_var_annotation_correct_order' => true,
        'phpdoc_var_without_name' => true,
        'self_accessor' => true,
    ];

    public function __construct(string $name = 'Gilbertsoft')
    {
        self::$typo3Rules = array_replace_recursive(self::$typo3Rules, self::GILBERTSOFT_RULES);

        parent::__construct($name);
    }

    /**
     * @param string $license One of the \Gilbertsoft\CodingStandards\License constants
     *
     * @return $this
     */
    public function setLicenseHeader(
        string $package,
        string $license
    ): self {
        $this->setHeader(License::getHeader($package, $license), true);

        return $this;
    }
}
