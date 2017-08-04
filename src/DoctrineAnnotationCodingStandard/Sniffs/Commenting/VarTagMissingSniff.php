<?php declare(strict_types = 1);

namespace DoctrineAnnotationCodingStandard\Sniffs\Commenting;

use DoctrineAnnotationCodingStandard\Helper\DocBlockHelper;
use DoctrineAnnotationCodingStandard\Helper\DoctrineMappingHelper;
use PHP_CodeSniffer\Files\File;

class VarTagMissingSniff extends AbstractDoctrineAnnotationSniff
{
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

        if (DocBlockHelper::getVarTagContent($phpcsFile, $stackPtr) === null) {
            $error = 'There must be a @var tag on Doctrine mapped properties';
            $phpcsFile->addError($error, $stackPtr, 'NoVarTag');
        }
    }
}
