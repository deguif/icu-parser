<?php

$finder = PhpCsFixer\Finder::create()
    ->in(['src/'])
;

return ($config = new PhpCsFixer\Config())
    ->setFinder($finder)
    ->setRules([
        '@PHP71Migration' => true,
        '@PhpCsFixer' => true,
        '@PHPUnit75Migration:risky' => true,
        '@PSR2' => true,
        '@Symfony' => true,
        'array_syntax' => ['syntax' => 'short'],
        'is_null' => true,
        'list_syntax' => ['syntax' => 'short'],
        'method_argument_space' => ['on_multiline' => 'ensure_fully_multiline'],
        'native_constant_invocation' => true,
        'native_function_invocation' => ['include' => ['@all']],
        'no_alias_functions' => true,
        'no_useless_else' => true,
        'nullable_type_declaration_for_default_null_value' => true,
        'ordered_imports' => true,
        'php_unit_dedicate_assert' => ['target' => 'newest'],
        'php_unit_test_class_requires_covers' => false,
        'phpdoc_order' => true,
        'phpdoc_types_order' => ['null_adjustment' => 'always_last'],
        'static_lambda' => true,
        'ternary_to_null_coalescing' => true,
        'visibility_required' => ['elements' => ['property', 'method', 'const']],
        'void_return' => true,
    ])
;
