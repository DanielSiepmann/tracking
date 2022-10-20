<?php

use PHP_CodeSniffer\Standards\Generic\Sniffs\Arrays\ArrayIndentSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\NamingConventions\UpperCaseConstantNameSniff;
use PHP_CodeSniffer\Standards\PSR1\Sniffs\Methods\CamelCapsMethodNameSniff;
use PHP_CodeSniffer\Standards\PSR12\Sniffs\ControlStructures\ControlStructureSpacingSniff as PSR12ControlStructureSpacingSniff;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\WhiteSpace\OperatorSpacingSniff;
use PhpCsFixer\Fixer\Alias\MbStrFunctionsFixer;
use PhpCsFixer\Fixer\Alias\NoAliasFunctionsFixer;
use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
use PhpCsFixer\Fixer\ArrayNotation\NoMultilineWhitespaceAroundDoubleArrowFixer;
use PhpCsFixer\Fixer\ArrayNotation\NormalizeIndexBraceFixer;
use PhpCsFixer\Fixer\ArrayNotation\NoTrailingCommaInSinglelineArrayFixer;
use PhpCsFixer\Fixer\ArrayNotation\NoWhitespaceBeforeCommaInArrayFixer;
use PhpCsFixer\Fixer\ArrayNotation\TrimArraySpacesFixer;
use PhpCsFixer\Fixer\Basic\NoMultipleStatementsPerLineFixer;
use PhpCsFixer\Fixer\Basic\NonPrintableCharacterFixer;
use PhpCsFixer\Fixer\Casing\NativeFunctionCasingFixer;
use PhpCsFixer\Fixer\Casing\NativeFunctionTypeDeclarationCasingFixer;
use PhpCsFixer\Fixer\CastNotation\CastSpacesFixer;
use PhpCsFixer\Fixer\CastNotation\ModernizeTypesCastingFixer;
use PhpCsFixer\Fixer\CastNotation\NoUnsetCastFixer;
use PhpCsFixer\Fixer\ClassNotation\ClassAttributesSeparationFixer;
use PhpCsFixer\Fixer\ClassNotation\NoNullPropertyInitializationFixer;
use PhpCsFixer\Fixer\ClassNotation\SelfAccessorFixer;
use PhpCsFixer\Fixer\Comment\MultilineCommentOpeningClosingFixer;
use PhpCsFixer\Fixer\Comment\NoEmptyCommentFixer;
use PhpCsFixer\Fixer\Comment\SingleLineCommentSpacingFixer;
use PhpCsFixer\Fixer\Comment\SingleLineCommentStyleFixer;
use PhpCsFixer\Fixer\ControlStructure\NoSuperfluousElseifFixer;
use PhpCsFixer\Fixer\ControlStructure\NoTrailingCommaInListCallFixer;
use PhpCsFixer\Fixer\ControlStructure\NoUnneededControlParenthesesFixer;
use PhpCsFixer\Fixer\ControlStructure\NoUselessElseFixer;
use PhpCsFixer\Fixer\ControlStructure\SimplifiedIfReturnFixer;
use PhpCsFixer\Fixer\ControlStructure\TrailingCommaInMultilineFixer;
use PhpCsFixer\Fixer\FunctionNotation\NoTrailingCommaInSinglelineFunctionCallFixer;
use PhpCsFixer\Fixer\FunctionNotation\NoUnreachableDefaultArgumentValueFixer;
use PhpCsFixer\Fixer\FunctionNotation\NoUselessSprintfFixer;
use PhpCsFixer\Fixer\FunctionNotation\NullableTypeDeclarationForDefaultNullValueFixer;
use PhpCsFixer\Fixer\FunctionNotation\RegularCallableCallFixer;
use PhpCsFixer\Fixer\FunctionNotation\StaticLambdaFixer;
use PhpCsFixer\Fixer\Import\FullyQualifiedStrictTypesFixer;
use PhpCsFixer\Fixer\Import\GlobalNamespaceImportFixer;
use PhpCsFixer\Fixer\Import\NoUnneededImportAliasFixer;
use PhpCsFixer\Fixer\Import\NoUnusedImportsFixer;
use PhpCsFixer\Fixer\Import\OrderedImportsFixer;
use PhpCsFixer\Fixer\LanguageConstruct\CombineConsecutiveIssetsFixer;
use PhpCsFixer\Fixer\LanguageConstruct\CombineConsecutiveUnsetsFixer;
use PhpCsFixer\Fixer\LanguageConstruct\SingleSpaceAfterConstructFixer;
use PhpCsFixer\Fixer\ListNotation\ListSyntaxFixer;
use PhpCsFixer\Fixer\NamespaceNotation\SingleBlankLineBeforeNamespaceFixer;
use PhpCsFixer\Fixer\Naming\NoHomoglyphNamesFixer;
use PhpCsFixer\Fixer\Operator\IncrementStyleFixer;
use PhpCsFixer\Fixer\Operator\NewWithBracesFixer;
use PhpCsFixer\Fixer\Operator\ObjectOperatorWithoutWhitespaceFixer;
use PhpCsFixer\Fixer\Operator\OperatorLinebreakFixer;
use PhpCsFixer\Fixer\Operator\StandardizeIncrementFixer;
use PhpCsFixer\Fixer\Operator\TernaryToElvisOperatorFixer;
use PhpCsFixer\Fixer\Operator\TernaryToNullCoalescingFixer;
use PhpCsFixer\Fixer\Operator\UnaryOperatorSpacesFixer;
use PhpCsFixer\Fixer\Phpdoc\NoBlankLinesAfterPhpdocFixer;
use PhpCsFixer\Fixer\Phpdoc\NoEmptyPhpdocFixer;
use PhpCsFixer\Fixer\Phpdoc\NoSuperfluousPhpdocTagsFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocAlignFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocIndentFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocLineSpanFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocNoAccessFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocOrderByValueFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocOrderFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocScalarFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocSeparationFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocToCommentFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocTrimConsecutiveBlankLineSeparationFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocTrimFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocTypesFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocTypesOrderFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocVarAnnotationCorrectOrderFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocVarWithoutNameFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitConstructFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitDedicateAssertFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitDedicateAssertInternalTypeFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitFqcnAnnotationFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitMethodCasingFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitMockFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitMockShortWillReturnFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitNamespacedFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitNoExpectationAnnotationFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitSetUpTearDownVisibilityFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitStrictFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitTestAnnotationFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitTestCaseStaticMethodCallsFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitTestClassRequiresCoversFixer;
use PhpCsFixer\Fixer\ReturnNotation\NoUselessReturnFixer;
use PhpCsFixer\Fixer\Semicolon\MultilineWhitespaceBeforeSemicolonsFixer;
use PhpCsFixer\Fixer\Semicolon\NoEmptyStatementFixer;
use PhpCsFixer\Fixer\Semicolon\NoSinglelineWhitespaceBeforeSemicolonsFixer;
use PhpCsFixer\Fixer\Semicolon\SemicolonAfterInstructionFixer;
use PhpCsFixer\Fixer\StringNotation\NoTrailingWhitespaceInStringFixer;
use PhpCsFixer\Fixer\StringNotation\SingleQuoteFixer;
use PhpCsFixer\Fixer\StringNotation\StringLengthToEmptyFixer;
use PhpCsFixer\Fixer\Whitespace\ArrayIndentationFixer;
use PhpCsFixer\Fixer\Whitespace\CompactNullableTypehintFixer;
use PhpCsFixer\Fixer\Whitespace\MethodChainingIndentationFixer;
use PhpCsFixer\Fixer\Whitespace\NoExtraBlankLinesFixer;
use PhpCsFixer\Fixer\Whitespace\NoSpacesAroundOffsetFixer;
use PhpCsFixer\Fixer\Whitespace\StatementIndentationFixer;
use PhpCsFixer\Fixer\Whitespace\TypesSpacesFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->sets([
        SetList::PSR_12,
    ]);

    $ecsConfig->paths([
        __DIR__ . '/Classes',
        __DIR__ . '/Tests',
        __DIR__ . '/ecs.php',
        __DIR__ . '/ext_emconf.php',
        __DIR__ . '/ext_localconf.php',
        __DIR__ . '/rector.php',
    ]);

    $ecsConfig->skip([
        CamelCapsMethodNameSniff::class => [
            __DIR__ . '/Classes/Hooks/DataHandler.php',
        ],
    ]);

    // Alias
    $ecsConfig->rule(MbStrFunctionsFixer::class);
    $ecsConfig->rule(NoAliasFunctionsFixer::class);

    // ArrayNotation
    $ecsConfig->ruleWithConfiguration(ArraySyntaxFixer::class, [
        'syntax' => 'short',
    ]);
    $ecsConfig->rule(NoMultilineWhitespaceAroundDoubleArrowFixer::class);
    $ecsConfig->rule(NormalizeIndexBraceFixer::class);
    $ecsConfig->rule(NoTrailingCommaInSinglelineArrayFixer::class);
    $ecsConfig->rule(NoWhitespaceBeforeCommaInArrayFixer::class);
    $ecsConfig->rule(TrimArraySpacesFixer::class);

    // Basic
    $ecsConfig->rule(NoMultipleStatementsPerLineFixer::class);
    $ecsConfig->rule(NonPrintableCharacterFixer::class);

    // Casing
    $ecsConfig->rule(NativeFunctionCasingFixer::class);
    $ecsConfig->rule(NativeFunctionTypeDeclarationCasingFixer::class);

    // CastNotation
    $ecsConfig->rule(CastSpacesFixer::class);
    $ecsConfig->rule(ModernizeTypesCastingFixer::class);
    $ecsConfig->rule(NoUnsetCastFixer::class);

    // ClassNotation
    $ecsConfig->ruleWithConfiguration(ClassAttributesSeparationFixer::class, [
        'elements' => [
            'const' => 'one',
            'method' => 'one',
            'property' => 'one',
            'trait_import' => 'one',
        ],
    ]);
    $ecsConfig->rule(NoNullPropertyInitializationFixer::class);
    $ecsConfig->rule(SelfAccessorFixer::class);

    $ecsConfig->rule(MultilineCommentOpeningClosingFixer::class);
    $ecsConfig->rule(NoEmptyCommentFixer::class);
    $ecsConfig->rule(SinglelineCommentSpacingFixer::class);
    $ecsConfig->rule(SingleLineCommentStyleFixer::class);

    // ControlStructure
    $ecsConfig->rule(NoSuperfluousElseifFixer::class);
    $ecsConfig->rule(NoTrailingCommaInListCallFixer::class);
    $ecsConfig->rule(NoUnneededControlParenthesesFixer::class);
    $ecsConfig->rule(NoUselessElseFixer::class);
    $ecsConfig->rule(SimplifiedIfReturnFixer::class);
    $ecsConfig->rule(TrailingCommaInMultilineFixer::class);
    $ecsConfig->rule(PSR12ControlStructureSpacingSniff::class);

    // FunctionNotation
    $ecsConfig->rule(NoTrailingCommaInSinglelineFunctionCallFixer::class);
    $ecsConfig->rule(NoUnreachableDefaultArgumentValueFixer::class);
    $ecsConfig->rule(NoUselessSprintfFixer::class);
    $ecsConfig->rule(NullableTypeDeclarationForDefaultNullValueFixer::class);
    $ecsConfig->rule(RegularCallableCallFixer::class);
    $ecsConfig->rule(StaticLambdaFixer::class);

    // Import
    $ecsConfig->rule(FullyQualifiedStrictTypesFixer::class);
    $ecsConfig->rule(GlobalNamespaceImportFixer::class);
    $ecsConfig->rule(NoUnneededImportAliasFixer::class);
    $ecsConfig->rule(NoUnusedImportsFixer::class);
    $ecsConfig->ruleWithConfiguration(OrderedImportsFixer::class, [
        'sort_algorithm' => 'alpha',
    ]);

    // LanguageConstruct
    $ecsConfig->rule(CombineConsecutiveIssetsFixer::class);
    $ecsConfig->rule(CombineConsecutiveUnsetsFixer::class);
    $ecsConfig->rule(SingleSpaceAfterConstructFixer::class);

    // ListNotation
    $ecsConfig->rule(ListSyntaxFixer::class);

    // NamespaceNotation
    $ecsConfig->rule(SingleBlankLineBeforeNamespaceFixer::class);

    // Naming
    $ecsConfig->rule(CamelCapsMethodNameSniff::class);
    $ecsConfig->rule(NoHomoglyphNamesFixer::class);
    $ecsConfig->rule(UpperCaseConstantNameSniff::class);

    // Operator
    $ecsConfig->ruleWithConfiguration(IncrementStyleFixer::class, [
        'style' => 'post',
    ]);
    $ecsConfig->ruleWithConfiguration(NewWithBracesFixer::class, [
        'anonymous_class' => false,
        'named_class' => true,
    ]);
    $ecsConfig->rule(ObjectOperatorWithoutWhitespaceFixer::class);
    $ecsConfig->ruleWithConfiguration(OperatorLinebreakFixer::class, [
        'position' => 'beginning',
    ]);
    $ecsConfig->rule(StandardizeIncrementFixer::class);
    $ecsConfig->rule(TernaryToElvisOperatorFixer::class);
    $ecsConfig->rule(TernaryToNullCoalescingFixer::class);
    $ecsConfig->rule(UnaryOperatorSpacesFixer::class);

    // Phpdoc
    $ecsConfig->rule(NoBlankLinesAfterPhpdocFixer::class);
    $ecsConfig->rule(NoEmptyPhpdocFixer::class);
    $ecsConfig->ruleWithConfiguration(NoSuperfluousPhpdocTagsFixer::class, [
        'allow_mixed' => true,
    ]);
    $ecsConfig->ruleWithConfiguration(PhpdocAlignFixer::class, [
        'align' => 'left',
    ]);
    $ecsConfig->rule(PhpdocIndentFixer::class);
    $ecsConfig->rule(PhpdocLineSpanFixer::class);
    $ecsConfig->rule(PhpdocNoAccessFixer::class);
    $ecsConfig->ruleWithConfiguration(PhpdocOrderByValueFixer::class, [
        'annotations' => [
            'covers',
            'throws',
        ],
    ]);
    $ecsConfig->rule(PhpdocOrderFixer::class);
    $ecsConfig->rule(PhpdocScalarFixer::class);
    $ecsConfig->rule(PhpdocSeparationFixer::class);
    $ecsConfig->rule(PhpdocToCommentFixer::class);
    $ecsConfig->rule(PhpdocTrimConsecutiveBlankLineSeparationFixer::class);
    $ecsConfig->rule(PhpdocTrimFixer::class);
    $ecsConfig->rule(PhpdocTypesFixer::class);
    $ecsConfig->rule(PhpdocTypesOrderFixer::class);
    $ecsConfig->rule(PhpdocVarAnnotationCorrectOrderFixer::class);
    $ecsConfig->rule(PhpdocVarWithoutNameFixer::class);

    // PhpUnit
    $ecsConfig->rule(PhpUnitConstructFixer::class);
    $ecsConfig->rule(PhpUnitDedicateAssertFixer::class);
    $ecsConfig->rule(PhpUnitDedicateAssertInternalTypeFixer::class);
    $ecsConfig->rule(PhpUnitFqcnAnnotationFixer::class);
    $ecsConfig->rule(PhpUnitMethodCasingFixer::class);
    $ecsConfig->rule(PhpUnitMockFixer::class);
    $ecsConfig->rule(PhpUnitMockShortWillReturnFixer::class);
    $ecsConfig->rule(PhpUnitNamespacedFixer::class);
    $ecsConfig->rule(PhpUnitNoExpectationAnnotationFixer::class);
    $ecsConfig->rule(PhpUnitSetUpTearDownVisibilityFixer::class);
    $ecsConfig->rule(PhpUnitStrictFixer::class);
    $ecsConfig->ruleWithConfiguration(PhpUnitTestAnnotationFixer::class, [
        'style' => 'annotation',
    ]);
    $ecsConfig->ruleWithConfiguration(PhpUnitTestCaseStaticMethodCallsFixer::class, [
        'call_type' => 'self',
    ]);
    $ecsConfig->rule(PhpUnitTestClassRequiresCoversFixer::class);

    // ReturnNotation
    $ecsConfig->rule(NoUselessReturnFixer::class);

    // Semicolon
    $ecsConfig->ruleWithConfiguration(MultilineWhitespaceBeforeSemicolonsFixer::class, [
        'strategy' => 'new_line_for_chained_calls',
    ]);
    $ecsConfig->rule(NoEmptyStatementFixer::class);
    $ecsConfig->rule(NoSinglelineWhitespaceBeforeSemicolonsFixer::class);
    $ecsConfig->rule(SemicolonAfterInstructionFixer::class);

    // StringNotation
    $ecsConfig->rule(NoTrailingWhitespaceInStringFixer::class);
    $ecsConfig->rule(SingleQuoteFixer::class);
    $ecsConfig->rule(StringLengthToEmptyFixer::class);

    // Whitespace
    $ecsConfig->rule(ArrayIndentationFixer::class);
    $ecsConfig->rule(ArrayIndentSniff::class);
    $ecsConfig->rule(CompactNullableTypehintFixer::class);
    $ecsConfig->rule(MethodChainingIndentationFixer::class);
    $ecsConfig->rule(NoExtraBlankLinesFixer::class);
    $ecsConfig->rule(NoSpacesAroundOffsetFixer::class);
    $ecsConfig->ruleWithConfiguration(OperatorSpacingSniff::class, [
        'ignoreSpacingBeforeAssignments' => false,
        'ignoreNewlines' => true,
    ]);
    $ecsConfig->rule(StatementIndentationFixer::class);
    $ecsConfig->rule(TypesSpacesFixer::class);
};
