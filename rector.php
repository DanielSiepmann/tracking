<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Core\ValueObject\PhpVersion;
use Rector\Php55\Rector\String_\StringClassNameToClassConstantRector;
use Rector\PostRector\Rector\NameImportingPostRector;
use Ssch\TYPO3Rector\Configuration\Typo3Option;
use Ssch\TYPO3Rector\FileProcessor\TypoScript\Rector\v9\v0\FileIncludeToImportStatementTypoScriptRector;
use Ssch\TYPO3Rector\Rector\General\ConvertImplicitVariablesToExplicitGlobalsRector;
use Ssch\TYPO3Rector\Rector\General\ExtEmConfRector;
use Ssch\TYPO3Rector\Set\Typo3LevelSetList;

return static function (RectorConfig $rectorConfig): void {
    $parameters = $rectorConfig->parameters();

    $rectorConfig->sets([
        Typo3LevelSetList::UP_TO_TYPO3_11,
    ]);

    $rectorConfig->paths([
        __DIR__ . '/Classes',
        __DIR__ . '/Tests',
        __DIR__ . '/Configuration',
        __DIR__ . '/ext_*.php',
    ]);

    // In order to have a better analysis from phpstan we teach it here some more things
    $rectorConfig->phpstanConfig(Typo3Option::PHPSTAN_FOR_RECTOR_PATH);

    // FQN classes are not imported by default. If you don't do it manually after every Rector run, enable it by:
    $rectorConfig->importNames();

    // Disable parallel otherwise non php file processing is not working i.e. typoscript
    $rectorConfig->disableParallel();

    // this will not import root namespace classes, like \DateTime or \Exception
    $rectorConfig->importShortClasses(true);

    // Define your target version which you want to support
    $rectorConfig->phpVersion(PhpVersion::PHP_74);

    $rectorConfig->skip([
        __DIR__ . '/.github/*',
        NameImportingPostRector::class => [
            'ext_localconf.php',
            'ext_tables.php',
            'ClassAliasMap.php',
            __DIR__ . '/**/Configuration/*.php',
            __DIR__ . '/**/Configuration/**/*.php',
        ],
    ]);

    $rectorConfig->rule(StringClassNameToClassConstantRector::class);

    // Add some general TYPO3 rules
    $rectorConfig->rule(ConvertImplicitVariablesToExplicitGlobalsRector::class);
    $rectorConfig->ruleWithConfiguration(ExtEmConfRector::class, [
        ExtEmConfRector::ADDITIONAL_VALUES_TO_BE_REMOVED => [],
    ]);

    $rectorConfig->rule(FileIncludeToImportStatementTypoScriptRector::class);
};