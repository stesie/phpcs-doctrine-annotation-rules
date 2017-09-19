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
        $varTagPos = self::getVarTagPosition($phpcsFile, $stackPtr);

        if ($varTagPos === null) {
            return null;
        }

        return self::fetchTagContent($phpcsFile->getTokens(), $varTagPos);
    }

    /**
     * @param File $phpcsFile
     * @param int $stackPtr
     * @return int|null
     */
    public static function getVarTagPosition(File $phpcsFile, int $stackPtr)
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

            return $tagPos;
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
        $content = ($parenPos !== false) ? substr($tagName, $parenPos) : '';

        while (in_array($tokens[++$tagPos]['type'], ['T_DOC_COMMENT_WHITESPACE', 'T_DOC_COMMENT_STRING'])) {
            $content .= $tokens[$tagPos]['content'];
        }

        return trim($content);
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

    public static function getIndentationWhitespace(File $phpcsFile, int $stackPtr): string
    {
        $tokens = $phpcsFile->getTokens();

        if ($tokens[$stackPtr]['column'] === 1) {
            return '';
        }

        if ($tokens[$stackPtr - 1]['code'] !== T_DOC_COMMENT_WHITESPACE) {
            throw new ParseErrorException('Expected T_DOC_COMMENT_WHITESPACE as indentation');
        }

        if ($tokens[$stackPtr - 1]['column'] !== 1) {
            throw new ParseErrorException('Non-whitespace before T_DOC_COMMENT_WHITESPACE');
        }

        return $tokens[$stackPtr - 1]['content'];
    }

    public static function replaceVarTagContent(File $phpcsFile, int $stackPtr, string $content)
    {
        $tokens = $phpcsFile->getTokens();
        $tagPos = self::getVarTagPosition($phpcsFile, $stackPtr);

        if ($tagPos === null) {
            throw new \LogicException('@var tag missing');
        }

        $varTagLineNumber = $tokens[$tagPos]['line'];

        if ($tokens[$tagPos + 1]['type'] !== 'T_DOC_COMMENT_WHITESPACE') {
            throw new ParseErrorException('Token after @var tag not T_DOC_COMMENT_WHITESPACE');
        }

        if ($tokens[$tagPos + 2]['line'] === $varTagLineNumber
            && $tokens[$tagPos + 2]['type'] !== 'T_DOC_COMMENT_STRING') {
            throw new ParseErrorException('T_DOC_COMMENT_STRING expected after @var tag');
        }

        $phpcsFile->fixer->beginChangeset();

        if ($tokens[$tagPos + 2]['line'] !== $varTagLineNumber) {
            $phpcsFile->fixer->replaceToken($tagPos + 1, sprintf(" %s\n", $content));
        } else {
            $phpcsFile->fixer->replaceToken($tagPos + 2, $content);
        }

        $phpcsFile->fixer->endChangeset();
    }
}
