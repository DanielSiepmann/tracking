<?php

declare(strict_types=1);

use PhpCsFixer\Config;
use PhpCsFixer\Finder;
use PhpCsFixer\Fixer\Alias\MbStrFunctionsFixer;
use PhpCsFixer\Fixer\Alias\NoAliasFunctionsFixer;
use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
use PhpCsFixer\Fixer\ArrayNotation\NoMultilineWhitespaceAroundDoubleArrowFixer;
use PhpCsFixer\Fixer\ArrayNotation\NoWhitespaceBeforeCommaInArrayFixer;
use PhpCsFixer\Fixer\ArrayNotation\NormalizeIndexBraceFixer;
use PhpCsFixer\Fixer\ArrayNotation\ReturnToYieldFromFixer;
use PhpCsFixer\Fixer\ArrayNotation\TrimArraySpacesFixer;
use PhpCsFixer\Fixer\ArrayNotation\WhitespaceAfterCommaInArrayFixer;
use PhpCsFixer\Fixer\ArrayNotation\YieldFromArrayToYieldsFixer;
use PhpCsFixer\Fixer\AttributeNotation\AttributeEmptyParenthesesFixer;
use PhpCsFixer\Fixer\Basic\NoMultipleStatementsPerLineFixer;
use PhpCsFixer\Fixer\Basic\NoTrailingCommaInSinglelineFixer;
use PhpCsFixer\Fixer\Basic\NonPrintableCharacterFixer;
use PhpCsFixer\Fixer\Basic\NumericLiteralSeparatorFixer;
use PhpCsFixer\Fixer\Casing\ClassReferenceNameCasingFixer;
use PhpCsFixer\Fixer\Casing\NativeFunctionCasingFixer;
use PhpCsFixer\Fixer\Casing\NativeTypeDeclarationCasingFixer;
use PhpCsFixer\Fixer\CastNotation\CastSpacesFixer;
use PhpCsFixer\Fixer\CastNotation\ModernizeTypesCastingFixer;
use PhpCsFixer\Fixer\CastNotation\NoUnsetCastFixer;
use PhpCsFixer\Fixer\ClassNotation\ClassAttributesSeparationFixer;
use PhpCsFixer\Fixer\ClassNotation\NoNullPropertyInitializationFixer;
use PhpCsFixer\Fixer\ClassNotation\OrderedClassElementsFixer;
use PhpCsFixer\Fixer\ClassNotation\OrderedInterfacesFixer;
use PhpCsFixer\Fixer\ClassNotation\OrderedTraitsFixer;
use PhpCsFixer\Fixer\ClassNotation\OrderedTypesFixer;
use PhpCsFixer\Fixer\ClassNotation\SelfAccessorFixer;
use PhpCsFixer\Fixer\Comment\MultilineCommentOpeningClosingFixer;
use PhpCsFixer\Fixer\Comment\NoEmptyCommentFixer;
use PhpCsFixer\Fixer\Comment\SingleLineCommentStyleFixer;
use PhpCsFixer\Fixer\ControlStructure\NoSuperfluousElseifFixer;
use PhpCsFixer\Fixer\ControlStructure\NoUnneededBracesFixer;
use PhpCsFixer\Fixer\ControlStructure\NoUnneededControlParenthesesFixer;
use PhpCsFixer\Fixer\ControlStructure\NoUselessElseFixer;
use PhpCsFixer\Fixer\ControlStructure\SimplifiedIfReturnFixer;
use PhpCsFixer\Fixer\ControlStructure\TrailingCommaInMultilineFixer;
use PhpCsFixer\Fixer\FunctionNotation\DateTimeCreateFromFormatCallFixer;
use PhpCsFixer\Fixer\FunctionNotation\MethodArgumentSpaceFixer;
use PhpCsFixer\Fixer\FunctionNotation\NoUnreachableDefaultArgumentValueFixer;
use PhpCsFixer\Fixer\FunctionNotation\NoUselessSprintfFixer;
use PhpCsFixer\Fixer\FunctionNotation\NullableTypeDeclarationForDefaultNullValueFixer;
use PhpCsFixer\Fixer\FunctionNotation\PhpdocToParamTypeFixer;
use PhpCsFixer\Fixer\FunctionNotation\PhpdocToPropertyTypeFixer;
use PhpCsFixer\Fixer\FunctionNotation\PhpdocToReturnTypeFixer;
use PhpCsFixer\Fixer\FunctionNotation\RegularCallableCallFixer;
use PhpCsFixer\Fixer\FunctionNotation\StaticLambdaFixer;
use PhpCsFixer\Fixer\Import\FullyQualifiedStrictTypesFixer;
use PhpCsFixer\Fixer\Import\GlobalNamespaceImportFixer;
use PhpCsFixer\Fixer\Import\NoUnneededImportAliasFixer;
use PhpCsFixer\Fixer\Import\NoUnusedImportsFixer;
use PhpCsFixer\Fixer\Import\OrderedImportsFixer;
use PhpCsFixer\Fixer\LanguageConstruct\ClassKeywordFixer;
use PhpCsFixer\Fixer\LanguageConstruct\CombineConsecutiveIssetsFixer;
use PhpCsFixer\Fixer\LanguageConstruct\CombineConsecutiveUnsetsFixer;
use PhpCsFixer\Fixer\LanguageConstruct\NullableTypeDeclarationFixer;
use PhpCsFixer\Fixer\LanguageConstruct\SingleSpaceAroundConstructFixer;
use PhpCsFixer\Fixer\ListNotation\ListSyntaxFixer;
use PhpCsFixer\Fixer\NamespaceNotation\BlankLinesBeforeNamespaceFixer;
use PhpCsFixer\Fixer\Naming\NoHomoglyphNamesFixer;
use PhpCsFixer\Fixer\Operator\ConcatSpaceFixer;
use PhpCsFixer\Fixer\Operator\IncrementStyleFixer;
use PhpCsFixer\Fixer\Operator\LongToShorthandOperatorFixer;
use PhpCsFixer\Fixer\Operator\NewWithParenthesesFixer;
use PhpCsFixer\Fixer\Operator\NoUselessConcatOperatorFixer;
use PhpCsFixer\Fixer\Operator\ObjectOperatorWithoutWhitespaceFixer;
use PhpCsFixer\Fixer\Operator\OperatorLinebreakFixer;
use PhpCsFixer\Fixer\Operator\StandardizeIncrementFixer;
use PhpCsFixer\Fixer\Operator\TernaryOperatorSpacesFixer;
use PhpCsFixer\Fixer\Operator\TernaryToElvisOperatorFixer;
use PhpCsFixer\Fixer\Operator\TernaryToNullCoalescingFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitConstructFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitDataProviderReturnTypeFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitDataProviderStaticFixer;
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
use PhpCsFixer\Fixer\Phpdoc\NoBlankLinesAfterPhpdocFixer;
use PhpCsFixer\Fixer\Phpdoc\NoEmptyPhpdocFixer;
use PhpCsFixer\Fixer\Phpdoc\NoSuperfluousPhpdocTagsFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocAlignFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocIndentFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocLineSpanFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocNoAccessFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocOrderByValueFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocOrderFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocParamOrderFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocScalarFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocSeparationFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocToCommentFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocTrimConsecutiveBlankLineSeparationFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocTrimFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocTypesFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocTypesOrderFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocVarAnnotationCorrectOrderFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocVarWithoutNameFixer;
use PhpCsFixer\Fixer\ReturnNotation\NoUselessReturnFixer;
use PhpCsFixer\Fixer\Semicolon\MultilineWhitespaceBeforeSemicolonsFixer;
use PhpCsFixer\Fixer\Semicolon\NoEmptyStatementFixer;
use PhpCsFixer\Fixer\Semicolon\NoSinglelineWhitespaceBeforeSemicolonsFixer;
use PhpCsFixer\Fixer\Semicolon\SemicolonAfterInstructionFixer;
use PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer;
use PhpCsFixer\Fixer\StringNotation\NoTrailingWhitespaceInStringFixer;
use PhpCsFixer\Fixer\StringNotation\SingleQuoteFixer;
use PhpCsFixer\Fixer\StringNotation\StringImplicitBackslashesFixer;
use PhpCsFixer\Fixer\StringNotation\StringLengthToEmptyFixer;
use PhpCsFixer\Fixer\Whitespace\ArrayIndentationFixer;
use PhpCsFixer\Fixer\Whitespace\CompactNullableTypeDeclarationFixer;
use PhpCsFixer\Fixer\Whitespace\MethodChainingIndentationFixer;
use PhpCsFixer\Fixer\Whitespace\NoExtraBlankLinesFixer;
use PhpCsFixer\Fixer\Whitespace\NoSpacesAroundOffsetFixer;
use PhpCsFixer\Fixer\Whitespace\StatementIndentationFixer;
use PhpCsFixer\Fixer\Whitespace\TypeDeclarationSpacesFixer;
use PhpCsFixer\Fixer\Whitespace\TypesSpacesFixer;
use PhpCsFixer\Preg;

/**
 * Transforms a given class to php-cs-fixer rule name.
 * That way we can use auto completion, phpstan, etc.
 *
 * @param class-string $className
 */
function transform(string $className): string
{
    $nameParts = explode('\\', $className);
    $name = mb_substr(end($nameParts), 0, -mb_strlen('Fixer'));

    return mb_strtolower(Preg::replace(
        '/(?<!^)((?=[\p{Lu}][^\p{Lu}])|(?<![\p{Lu}])(?=[\p{Lu}]))/',
        '_',
        $name
    ));
}

return (new Config())
    ->setRiskyAllowed(true)
    ->setFinder(
        (new Finder())
            ->ignoreVCSIgnored(true)
            ->in(realpath(__DIR__))
    )
    ->setRules([
        '@PSR12' => true,
        transform(ArrayIndentationFixer::class) => true,
        transform(ArraySyntaxFixer::class) => [
            'syntax' => 'short',
        ],
        transform(AttributeEmptyParenthesesFixer::class) => true,
        transform(BlankLinesBeforeNamespaceFixer::class) => true,
        transform(CastSpacesFixer::class) => true,
        transform(ClassAttributesSeparationFixer::class) => [
            'elements' => [
                'const' => 'one',
                'method' => 'one',
                'property' => 'one',
                'trait_import' => 'one',
            ],
        ],
        transform(ClassKeywordFixer::class) => true,
        transform(ClassReferenceNameCasingFixer::class) => true,
        transform(CombineConsecutiveIssetsFixer::class) => true,
        transform(CombineConsecutiveUnsetsFixer::class) => true,
        transform(CompactNullableTypeDeclarationFixer::class) => true,
        transform(ConcatSpaceFixer::class) => [
            'spacing' => 'one',
        ],
        transform(DateTimeCreateFromFormatCallFixer::class) => true,
        transform(DeclareStrictTypesFixer::class) => true,
        transform(FullyQualifiedStrictTypesFixer::class) => [
            'import_symbols' => true,
            'leading_backslash_in_global_namespace' => true,
        ],
        transform(GlobalNamespaceImportFixer::class) => true,
        transform(IncrementStyleFixer::class) => [
            'style' => 'post',
        ],
        transform(ListSyntaxFixer::class) => true,
        transform(LongToShorthandOperatorFixer::class) => true,
        transform(MbStrFunctionsFixer::class) => true,
        transform(MethodArgumentSpaceFixer::class) => [
            'on_multiline' => 'ensure_fully_multiline',
            'attribute_placement' => 'standalone',
        ],
        transform(MethodChainingIndentationFixer::class) => true,
        transform(ModernizeTypesCastingFixer::class) => true,
        transform(MultilineCommentOpeningClosingFixer::class) => true,
        transform(MultilineWhitespaceBeforeSemicolonsFixer::class) => [
            'strategy' => 'new_line_for_chained_calls',
        ],
        transform(NativeFunctionCasingFixer::class) => true,
        transform(NativeTypeDeclarationCasingFixer::class) => true,
        transform(NewWithParenthesesFixer::class) => [
            'anonymous_class' => false,
            'named_class' => true,
        ],
        transform(NoAliasFunctionsFixer::class) => true,
        transform(NoBlankLinesAfterPhpdocFixer::class) => true,
        transform(NoEmptyCommentFixer::class) => true,
        transform(NoEmptyPhpdocFixer::class) => true,
        transform(NoEmptyStatementFixer::class) => true,
        transform(NoExtraBlankLinesFixer::class) => true,
        transform(NoHomoglyphNamesFixer::class) => true,
        transform(NoMultilineWhitespaceAroundDoubleArrowFixer::class) => true,
        transform(NoMultipleStatementsPerLineFixer::class) => true,
        transform(NoNullPropertyInitializationFixer::class) => true,
        transform(NoSinglelineWhitespaceBeforeSemicolonsFixer::class) => true,
        transform(NoSpacesAroundOffsetFixer::class) => true,
        transform(NoSuperfluousElseifFixer::class) => true,
        transform(NoSuperfluousPhpdocTagsFixer::class) => [
            'allow_mixed' => true,
        ],
        transform(NoTrailingCommaInSinglelineFixer::class) => true,
        transform(NoTrailingWhitespaceInStringFixer::class) => true,
        transform(NoUnneededBracesFixer::class) => true,
        transform(NoUnneededControlParenthesesFixer::class) => true,
        transform(NoUnneededImportAliasFixer::class) => true,
        transform(NoUnreachableDefaultArgumentValueFixer::class) => true,
        transform(NoUnsetCastFixer::class) => true,
        transform(NoUnusedImportsFixer::class) => true,
        transform(NoUselessConcatOperatorFixer::class) => true,
        transform(NoUselessElseFixer::class) => true,
        transform(NoUselessReturnFixer::class) => true,
        transform(NoUselessSprintfFixer::class) => true,
        transform(NoWhitespaceBeforeCommaInArrayFixer::class) => true,
        transform(NonPrintableCharacterFixer::class) => true,
        transform(NormalizeIndexBraceFixer::class) => true,
        transform(NullableTypeDeclarationFixer::class) => true,
        transform(NullableTypeDeclarationForDefaultNullValueFixer::class) => true,
        transform(NumericLiteralSeparatorFixer::class) => [
            'strategy' => NumericLiteralSeparatorFixer::STRATEGY_NO_SEPARATOR,
        ],
        transform(ObjectOperatorWithoutWhitespaceFixer::class) => true,
        transform(OperatorLinebreakFixer::class) => [
            'position' => 'beginning', ],
        transform(OrderedClassElementsFixer::class) => [
            'order' => [
                'use_trait',
                'case',
                'constant_public',
                'constant_protected',
                'constant_private',
                'property_public',
                'property_protected',
                'property_private',
                'construct',
                'destruct',
                'phpunit',
                'method_public',
                'method_protected',
                'method_private',
                'magic',
            ],
            'case_sensitive' => true,
        ],
        transform(OrderedImportsFixer::class) => [
            'sort_algorithm' => 'alpha',
            'case_sensitive' => true,
        ],
        transform(OrderedInterfacesFixer::class) => [
            'case_sensitive' => true,
        ],
        transform(OrderedTraitsFixer::class) => [
            'case_sensitive' => true,
        ],
        transform(OrderedTypesFixer::class) => [
            'case_sensitive' => true,
        ],
        transform(PhpUnitConstructFixer::class) => true,
        transform(PhpUnitDataProviderReturnTypeFixer::class) => true,
        transform(PhpUnitDataProviderStaticFixer::class) => [
            'force' => true,
        ],
        transform(PhpUnitDedicateAssertFixer::class) => true,
        transform(PhpUnitDedicateAssertInternalTypeFixer::class) => true,
        transform(PhpUnitFqcnAnnotationFixer::class) => true,
        transform(PhpUnitMethodCasingFixer::class) => true,
        transform(PhpUnitMockFixer::class) => true,
        transform(PhpUnitMockShortWillReturnFixer::class) => true,
        transform(PhpUnitNamespacedFixer::class) => true,
        transform(PhpUnitNoExpectationAnnotationFixer::class) => true,
        transform(PhpUnitSetUpTearDownVisibilityFixer::class) => true,
        transform(PhpUnitStrictFixer::class) => true,
        transform(PhpUnitTestAnnotationFixer::class) => [
            'style' => 'annotation',
        ],
        transform(PhpUnitTestCaseStaticMethodCallsFixer::class) => [
            'call_type' => 'self',
        ],
        transform(PhpdocAlignFixer::class) => [
            'align' => 'left',
        ],
        transform(PhpdocIndentFixer::class) => true,
        transform(PhpdocLineSpanFixer::class) => true,
        transform(PhpdocNoAccessFixer::class) => true,
        transform(PhpdocOrderByValueFixer::class) => [
            'annotations' => [
                'covers', 'throws',
            ],
        ],
        transform(PhpdocOrderFixer::class) => true,
        transform(PhpdocParamOrderFixer::class) => true,
        transform(PhpdocScalarFixer::class) => true,
        transform(PhpdocSeparationFixer::class) => [
            'groups' => [
                ['see'],
                ['throw'],
                ['param'],
                ['return'],
            ],
        ],
        transform(PhpdocToCommentFixer::class) => true,
        transform(PhpdocToParamTypeFixer::class) => true,
        transform(PhpdocToPropertyTypeFixer::class) => true,
        transform(PhpdocToReturnTypeFixer::class) => true,
        transform(PhpdocTrimConsecutiveBlankLineSeparationFixer::class) => true,
        transform(PhpdocTrimFixer::class) => true,
        transform(PhpdocTypesFixer::class) => true,
        transform(PhpdocTypesOrderFixer::class) => [
            'case_sensitive' => true,
        ],
        transform(PhpdocVarAnnotationCorrectOrderFixer::class) => true,
        transform(PhpdocVarWithoutNameFixer::class) => true,
        transform(RegularCallableCallFixer::class) => true,
        transform(ReturnToYieldFromFixer::class) => true,
        transform(SelfAccessorFixer::class) => true,
        transform(SemicolonAfterInstructionFixer::class) => true,
        transform(SimplifiedIfReturnFixer::class) => true,
        transform(SingleLineCommentStyleFixer::class) => true,
        transform(SingleQuoteFixer::class) => true,
        transform(SingleSpaceAroundConstructFixer::class) => true,
        transform(StandardizeIncrementFixer::class) => true,
        transform(StatementIndentationFixer::class) => true,
        transform(StaticLambdaFixer::class) => true,
        transform(StringImplicitBackslashesFixer::class) => [
            'single_quoted' => 'ignore',
        ],
        transform(StringLengthToEmptyFixer::class) => true,
        transform(TernaryOperatorSpacesFixer::class) => true,
        transform(TernaryToElvisOperatorFixer::class) => true,
        transform(TernaryToNullCoalescingFixer::class) => true,
        transform(TrailingCommaInMultilineFixer::class) => true,
        transform(TrimArraySpacesFixer::class) => true,
        transform(TypeDeclarationSpacesFixer::class) => true,
        transform(TypesSpacesFixer::class) => true,
        transform(WhitespaceAfterCommaInArrayFixer::class) => [
            'ensure_single_space' => true,
        ],
        transform(YieldFromArrayToYieldsFixer::class) => true,
    ])
;
