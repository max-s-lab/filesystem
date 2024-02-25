<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude([
        'vendor'
    ]);

return (new PhpCsFixer\Config())
    ->setRules([
        '@PSR12' => true,
        'array_syntax' => [
            'syntax' => 'short'
        ],
        'single_quote' => true
    ])
    ->setFinder($finder);
