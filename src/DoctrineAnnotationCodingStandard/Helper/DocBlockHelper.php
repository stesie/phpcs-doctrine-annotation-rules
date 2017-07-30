<?php

namespace DoctrineAnnotationCodingStandard\Helper;

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

            return self::fetchTagContent($tokens, $tagPos);
        }

        return null;
    }

    /**
     * @param File $phpcsFile
     * @param int $stackPtr
     * @param string[] $imports
     * @param string $class
     * @return string|null
     */
    public static function findTagByClass(File $phpcsFile, $stackPtr, $imports, $class)
    {
        $tokens = $phpcsFile->getTokens();

        foreach ($tokens[$stackPtr]['comment_tags'] as $tagPos) {
            $tagName = substr($tokens[$tagPos]['content'], 1);

            $parenPos = strpos($tagName, '(');
            if (false !== $parenPos) {
                $tagName = substr($tagName, 0, $parenPos);
            }

            if ($class === self::expandClassName($imports, $tagName)) {
                return self::fetchTagContent($tokens, $tagPos);
            }
        }

        return null;
    }

    /**
     * @param array $tokens
     * @param int $tagPos
     * @return string
     */
    private static function fetchTagContent($tokens, $tagPos)
    {
        $tagName = substr($tokens[$tagPos]['content'], 1);

        $parenPos = strpos($tagName, '(');
        if (false !== $parenPos) {
            return substr($tagName, $parenPos);
        }

        if ('T_DOC_COMMENT_WHITESPACE' !== $tokens[$tagPos + 1]['type']) {
            throw new \LogicException('Token after @tag not T_DOC_COMMENT_WHITESPACE');
        }

        if ('T_DOC_COMMENT_STRING' !== $tokens[$tagPos + 2]['type']) {
            throw new \LogicException('T_DOC_COMMENT_STRING expected after @tag');
        }

        return $tokens[$tagPos + 2]['content'];
    }

    /**
     * @param $imports
     * @param $tagName
     * @return string
     */
    private static function expandClassName($imports, $tagName)
    {
        $nsSeparator = strpos($tagName, '\\');
        if (false !== $nsSeparator) {
            $alias = strtolower(substr($tagName, 0, $nsSeparator));

            if (isset($imports[$alias])) {
                $tagName = $imports[$alias] . substr($tagName, $nsSeparator);
            }
        }
        return $tagName;
    }
}