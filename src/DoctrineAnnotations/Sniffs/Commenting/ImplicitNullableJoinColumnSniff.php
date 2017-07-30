<?php

namespace DoctrineAnnotations\Sniffs\Commenting;

use Doctrine\ORM\Mapping\JoinColumn;
use DoctrineAnnotations\Helper\DocBlockHelper;
use DoctrineAnnotations\Helper\DoctrineMappingHelper;
use PHP_CodeSniffer\Files\File;

class ImplicitNullableJoinColumnSniff extends AbstractDoctrineAnnotationSniff
{
    /**
     * @param File $phpcsFile
     * @param int $stackPtr
     * @param array $annotations
     */
    protected function sniffDocblock(File $phpcsFile, $stackPtr, $annotations)
    {
        if (!DoctrineMappingHelper::isDoctrineJoin($annotations)) {
            return;
        }

        /** @var JoinColumn $joinColumn */
        $joinColumn = DoctrineMappingHelper::findAnnotationByClass(JoinColumn::class, $annotations);

        if (null === $joinColumn) {
            $error = 'There must be a @JoinColumn tag on Doctrine mapped relations';
            $phpcsFile->addError($error, $stackPtr, 'NoJoinColumn');
            return;
        }

        if (false === $joinColumn->nullable) {
            // explicit false -> alright
            return;
        }

        $content = DocBlockHelper::findTagByClass($phpcsFile, $stackPtr, $this->getImports(), JoinColumn::class);

        if (!preg_match('/nullable=true/', $content)) {
            $error = 'There must be an explicit nullable property on @JoinColumn tag';
            $phpcsFile->addError($error, $stackPtr, 'NoNullableProperty');
            return;
        }
    }
}