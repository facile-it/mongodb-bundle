<?php

use Facile\CodingStandards\Rules\ArrayRulesProvider;
use Facile\CodingStandards\Rules\CompositeRulesProvider;
use Facile\CodingStandards\Rules\DefaultRulesProvider;

$config = require __DIR__ . '/.php_cs.dist';

$additionalRules = [
    'phpdoc_inline_tag' => true,
    'blank_line_after_opening_tag' => false,
    'class_attributes_separation' => [
        'elements' => ['method']
    ]
];

$rulesProvider = new Facile\CodingStandards\Rules\CompositeRulesProvider([
    new Facile\CodingStandards\Rules\DefaultRulesProvider(),
    new Facile\CodingStandards\Rules\ArrayRulesProvider($additionalRules),
]);

$config->setRules($rulesProvider->getRules());

return $config;
