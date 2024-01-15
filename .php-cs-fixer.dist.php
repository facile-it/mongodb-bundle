<?php

/*
 * Additional rules or rules to override.
 * These rules will be added to default rules or will override them if the same key already exists.
 */

$additionalRules = [
    'blank_line_after_opening_tag' => false,
];

$rulesProvider = new Facile\CodingStandards\Rules\CompositeRulesProvider([
    new Facile\CodingStandards\Rules\DefaultRulesProvider(),
    new Facile\CodingStandards\Rules\ArrayRulesProvider($additionalRules),
]);

$config = new PhpCsFixer\Config();
$config
    ->setUsingCache(true)
    ->setRiskyAllowed(true)
    ->setRules($rulesProvider->getRules());

$finder = PhpCsFixer\Finder::create();

/*
 * You can set manually these paths:
 */
$autoloadPathProvider = new Facile\CodingStandards\AutoloadPathProvider();
$finder->in($autoloadPathProvider->getPaths());

$config->setFinder($finder);

return $config;
