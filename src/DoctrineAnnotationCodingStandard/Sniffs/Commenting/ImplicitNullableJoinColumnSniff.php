<?php declare(strict_types = 1);

namespace DoctrineAnnotationCodingStandard\Sniffs\Commenting;

use Doctrine\ORM\Mapping\JoinColumn;
use DoctrineAnnotationCodingStandard\Helper\DocBlockHelper;
use DoctrineAnnotationCodingStandard\Helper\DoctrineMappingHelper;
use PHP_CodeSniffer\Files\File;

class ImplicitNullableJoinColumnSniff extends AbstractDoctrineAnnotationSniff
{
    const CODE_NO_JOIN_COLUMN = 'NoJoinColumn';
    const CODE_NO_NULLABLE_PROPERTY = 'NoNullableProperty';

    /**
     * @param File $phpcsFile
     * @param int $stackPtr
     * @param array $annotations
     */
    protected function sniffDocblock(File $phpcsFile, int $stackPtr, array $annotations)
    {
        if (!DoctrineMappingHelper::isMappedDoctrineToOneJoin($annotations)) {
            return;
        }

        /** @var JoinColumn|null $joinColumn */
        $joinColumn = DocBlockHelper::findAnnotationByClass(JoinColumn::class, $annotations);

        if ($joinColumn === null) {
            $error = 'There must be a @JoinColumn tag on Doctrine mapped relations';
            $phpcsFile->addError($error, $stackPtr, self::CODE_NO_JOIN_COLUMN);
            return;
        }

        if ($joinColumn->nullable === false) {
            // explicit false -> alright
            return;
        }

        $content = DocBlockHelper::findTagByClass($phpcsFile, $stackPtr, $this->getImports(), JoinColumn::class);

        if (!preg_match('/nullable\s*=\s*true/', $content)) {
            $error = 'There must be an explicit nullable property on @JoinColumn tag';
            $phpcsFile->addError($error, $stackPtr, self::CODE_NO_NULLABLE_PROPERTY);
            return;
        }
    }
}
