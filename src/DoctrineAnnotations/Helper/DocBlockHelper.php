<?php

namespace DoctrineAnnotations\Helper;

use PHP_CodeSniffer\Files\File;

class DocBlockHelper
{
    /**
     * @param File $phpcsFile
     * @param int $stackPtr
     * @return string|null
     */
    public static function getVarTagContent(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        foreach ($tokens[$stackPtr]['comment_tags'] as $tagPos) {
            if ('@var' !== $tokens[$tagPos]['content']) {
                continue;
            }

            if ('T_DOC_COMMENT_WHITESPACE' !== $tokens[$tagPos + 1]['type']) {
                throw new \LogicException('Token after @tag not T_DOC_COMMENT_WHITESPACE');
            }

            if ('T_DOC_COMMENT_STRING' !== $tokens[$tagPos + 2]['type']) {
                throw new \LogicException('T_DOC_COMMENT_STRING expected after @tag');
            }

            return $tokens[$tagPos + 2]['content'];
        }

        return null;
    }
}