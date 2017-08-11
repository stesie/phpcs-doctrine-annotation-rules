<?php declare(strict_types = 1);

namespace DoctrineAnnotationCodingStandard\Sniffs\Commenting;

use DoctrineAnnotationCodingStandard\Helper\DocBlockHelper;
use DoctrineAnnotationCodingStandard\Helper\DoctrineMappingHelper;
use DoctrineAnnotationCodingStandard\Helper\TypeHelper;
use DoctrineAnnotationCodingStandard\Types\QualifyableObjectType;
use DoctrineAnnotationCodingStandard\Types\Type;
use PHP_CodeSniffer\Files\File;

class VarTagSniff extends AbstractDoctrineAnnotationSniff
{
    const CODE_NO_VAR_TAG = 'NoVarTag';
    const CODE_WRONG_VAR_TAG = 'WrongVarTag';

    /**
     * @param File $phpcsFile
     * @param int $stackPtr
     * @param array $annotations
     */
    protected function sniffDocblock(File $phpcsFile, int $stackPtr, array $annotations)
    {
        if (!DoctrineMappingHelper::isDoctrineMappedProperty($annotations)) {
            return;
        }

        $tokens = $phpcsFile->getTokens();
        $closerTokenPtr = $tokens[$stackPtr]['comment_closer'];

        $varTagContent = DocBlockHelper::getVarTagContent($phpcsFile, $stackPtr);
        $expectedType = $this->qualify(DoctrineMappingHelper::getMappedType($annotations));

        if ($varTagContent === null) {
            $error = 'There must be a @var tag on Doctrine mapped properties';

            if ($tokens[$stackPtr]['line'] === $tokens[$closerTokenPtr]['line']) {
                $phpcsFile->addError($error, $stackPtr, self::CODE_NO_VAR_TAG);
                $fix = false;
            } else {
                $fix = $phpcsFile->addFixableError($error, $stackPtr, self::CODE_NO_VAR_TAG);
            }

            if ($fix) {
                $phpcsFile->fixer->beginChangeset();
                $phpcsFile->fixer->addContentBefore(
                    $closerTokenPtr,
                    \sprintf(
                        "* @var %s\n%s",
                        $expectedType->toString($this->getNamespace(), $this->getImports()),
                        DocBlockHelper::getIndentationWhitespace($phpcsFile, $closerTokenPtr)
                    )
                );
                $phpcsFile->fixer->endChangeset();
            }

            return;
        }

        $actualType = TypeHelper::fromString($varTagContent, $this->getNamespace(), $this->getImports());

        if (!$expectedType->isEqual($actualType)) {
            $error = \sprintf(
                'Expected @var type of "%s", got "%s"',
                $expectedType->toString($this->getNamespace(), $this->getImports()),
                $actualType->toString($this->getNamespace(), $this->getImports())
            );

            if ($tokens[$stackPtr]['line'] === $tokens[$closerTokenPtr]['line']) {
                $phpcsFile->addError($error, $stackPtr, self::CODE_WRONG_VAR_TAG);
                $fix = false;
            } else {
                $fix = $phpcsFile->addFixableError($error, $stackPtr, self::CODE_WRONG_VAR_TAG);
            }

            if ($fix) {
                DocBlockHelper::replaceVarTagContent(
                    $phpcsFile,
                    $stackPtr,
                    $expectedType->toString($this->getNamespace(), $this->getImports())
                );
            }
        }
    }

    /**
     * @param Type $type
     * @return Type
     */
    private function qualify(Type $type): Type
    {
        if (!$type instanceof QualifyableObjectType) {
            return $type;
        }

        return $type->qualify($this->getNamespace(), $this->getImports());
    }
}
