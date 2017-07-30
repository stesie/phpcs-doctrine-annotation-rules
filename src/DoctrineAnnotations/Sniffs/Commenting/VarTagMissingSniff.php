<?php

namespace DoctrineAnnotations\Sniffs\Commenting;

use DoctrineAnnotations\Helper\DocBlockHelper;
use PHP_CodeSniffer\Files\File;

class VarTagMissingSniff extends AbstractDoctrineAnnotationSniff
{

    /**
     * @param File $phpcsFile
     * @param int $stackPtr
     * @param array $annotations
     */
    protected function sniffDocblock(File $phpcsFile, $stackPtr, $annotations)
    {
        if (!$this->isDoctrineMappedProperty($annotations)) {
            return;
        }

        if (null === DocBlockHelper::getVarTagContent($phpcsFile, $stackPtr)) {
            $error = 'There must be a @var tag on Doctrine mapped properties';
            $phpcsFile->addError($error, $stackPtr, 'NoVarTag');
        }
    }
}