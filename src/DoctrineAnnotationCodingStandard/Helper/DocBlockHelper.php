<?php declare(strict_types = 1);

namespace DoctrineAnnotationCodingStandard\Helper;

use DoctrineAnnotationCodingStandard\Exception\ParseErrorException;
use DoctrineAnnotationCodingStandard\ImportClassMap;
use PHP_CodeSniffer\Files\File;

class DocBlockHelper
{
    /**
     * @param File $phpcsFile
     * @param int $stackPtr
     * @return string|null
     */
    public static function getVarTagContent(File $phpcsFile, int $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if (!isset($tokens[$stackPtr])) {
            throw new \OutOfRangeException();
        }

        if ($tokens[$stackPtr]['type'] !== 'T_DOC_COMMENT_OPEN_TAG') {
            throw new \InvalidArgumentException();
        }

        foreach ($tokens[$stackPtr]['comment_tags'] as $tagPos) {
            if ($tokens[$tagPos]['content'] !== '@var') {
                continue;
            }

            return self::fetchTagContent($tokens, $tagPos);
        }

        return null;
    }

    /**
     * @param File $phpcsFile
     * @param int $stackPtr
     * @param ImportClassMap $imports
     * @param string $class
     * @return string|null
     */
    public static function findTagByClass(File $phpcsFile, int $stackPtr, ImportClassMap $imports, string $class)
    {
        $tokens = $phpcsFile->getTokens();

        if (!isset($tokens[$stackPtr])) {
            throw new \OutOfRangeException();
        }

        if ($tokens[$stackPtr]['type'] !== 'T_DOC_COMMENT_OPEN_TAG') {
            throw new \InvalidArgumentException();
        }

        foreach ($tokens[$stackPtr]['comment_tags'] as $tagPos) {
            $tagName = substr($tokens[$tagPos]['content'], 1);

            $parenPos = strpos($tagName, '(');
            if ($parenPos !== false) {
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
    private static function fetchTagContent(array $tokens, int $tagPos): string
    {
        $tagName = substr($tokens[$tagPos]['content'], 1);

        $parenPos = strpos($tagName, '(');
        if ($parenPos !== false) {
            return substr($tagName, $parenPos);
        }

        if ($tokens[$tagPos + 1]['type'] !== 'T_DOC_COMMENT_WHITESPACE') {
            throw new ParseErrorException('Token after @tag not T_DOC_COMMENT_WHITESPACE');
        }

        if ($tokens[$tagPos + 2]['type'] !== 'T_DOC_COMMENT_STRING') {
            throw new ParseErrorException('T_DOC_COMMENT_STRING expected after @tag');
        }

        return trim($tokens[$tagPos + 2]['content']);
    }

    /**
     * @param ImportClassMap $imports
     * @param string $tagName
     * @return string
     */
    private static function expandClassName(ImportClassMap $imports, string $tagName): string
    {
        $nsSeparator = strpos($tagName, '\\');
        if ($nsSeparator !== false) {
            $alias = strtolower(substr($tagName, 0, $nsSeparator));

            if ($imports->hasAlias($alias)) {
                $tagName = $imports->classByAlias($alias) . substr($tagName, $nsSeparator);
            }
        }
        return $tagName;
    }

    /**
     * @param string $className
     * @param array $annotations
     * @return object|null
     */
    public static function findAnnotationByClass(string $className, array $annotations)
    {
        foreach ($annotations as $doctrineTag) {
            if ($className === get_class($doctrineTag)) {
                return $doctrineTag;
            }
        }

        return null;
    }
}
