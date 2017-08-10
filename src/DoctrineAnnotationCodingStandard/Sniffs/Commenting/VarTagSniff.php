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

        $varTagContent = DocBlockHelper::getVarTagContent($phpcsFile, $stackPtr);

        if ($varTagContent === null) {
            $error = 'There must be a @var tag on Doctrine mapped properties';
            $phpcsFile->addError($error, $stackPtr, self::CODE_NO_VAR_TAG);

            return;
        }

        $expectedType = $this->qualify(DoctrineMappingHelper::getMappedType($annotations));
        $actualType = $this->qualify(TypeHelper::fromString($varTagContent));

        if (!$expectedType->isEqual($actualType)) {
            $error = \sprintf(
                'Expected @var type of "%s", got "%s"',
                $expectedType->toString($this->getNamespace(), $this->getImports()),
                $actualType->toString($this->getNamespace(), $this->getImports())
            );
            $phpcsFile->addError($error, $stackPtr, self::CODE_WRONG_VAR_TAG);
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
