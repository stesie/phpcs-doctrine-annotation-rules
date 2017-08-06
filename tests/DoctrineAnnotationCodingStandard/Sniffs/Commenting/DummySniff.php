<?php declare(strict_types = 1);

namespace DoctrineAnnotationCodingStandardTests\Sniffs\Commenting;

use DoctrineAnnotationCodingStandard\Sniffs\Commenting\AbstractDoctrineAnnotationSniff;
use PHP_CodeSniffer\Files\File;

class DummySniff extends AbstractDoctrineAnnotationSniff
{
    /**
     * @var object[]
     */
    private $annotations;

    /**
     * @param File $phpcsFile
     * @param int $stackPtr
     * @param array $annotations
     */
    protected function sniffDocblock(File $phpcsFile, int $stackPtr, array $annotations)
    {
        $this->annotations = $annotations;
    }

    /**
     * @return array
     */
    public function getAnnotations(): array
    {
        return $this->annotations;
    }
}
