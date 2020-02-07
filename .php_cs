<?php

$finder = PhpCsFixer\Finder::create()
    ->in('./')
;

return PhpCsFixer\Config::create()
    ->setRules([
        '@Symfony' => true,
        'binary_operator_spaces' => ['default' => 'align'],
        'ordered_imports' => ['sort_algorithm' => 'length'],
    ])
    ->setFinder($finder)
;
