<?php

$header = <<<'EOF'
This file is part of staccato listable component

(c) Krystian Karaś <dev@karashome.pl>

This source file is subject to the MIT license that is bundled
with this source code in the file LICENSE.
EOF;

return PhpCsFixer\Config::create()
    ->setRiskyAllowed(true)
    ->setRules(array(
        '@Symfony' => true,
        '@Symfony:risky' => true,
        'header_comment' => array('header' => $header),
        'combine_consecutive_unsets' => true,
        'concat_space' => array('spacing' => 'one'),
        'array_syntax' => array('syntax' => 'long'),
        'no_extra_consecutive_blank_lines' => array('break', 'continue', 'extra', 'return', 'throw', 'use', 'parenthesis_brace_block', 'square_brace_block', 'curly_brace_block'),
        'no_useless_else' => true,
        'no_useless_return' => true,
        'ordered_class_elements' => true,
        'ordered_imports' => true,
        'php_unit_strict' => false,
        'psr4' => true,
        'strict_comparison' => false,
        'strict_param' => false,
    ))
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->exclude('vendor')
            ->in(__DIR__)
    )
;
