<?php declare(strict_types = 1);

namespace DoctrineAnnotationCodingStandard\Sniffs\Commenting;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\DocParser;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

abstract class AbstractDoctrineAnnotationSniff implements Sniff
{
    /**
     * Imports used by current file (lowercase alias as key)
     *
     * @var string[]
     */
    private $imports = [];

    /**
     * The current file's namespace, if any
     *
     * @var string|null
     */
    private $namespace;

    /**
     * @param File $phpcsFile
     * @param int $stackPtr
     * @param array $annotations
     */
    abstract protected function sniffDocblock(File $phpcsFile, int $stackPtr, array $annotations);

    /**
     * @return array
     */
    public function register(): array
    {
        /** @noinspection PhpDeprecationInspection */
        AnnotationRegistry::registerLoader('class_exists');

        return [
            T_NAMESPACE,
            T_USE,
            T_DOC_COMMENT_OPEN_TAG,
        ];
    }

    /**
     * @param File $phpcsFile
     * @param int $stackPtr
     * @return int|void
     */ // @codingStandardsIgnoreLine
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        switch ($tokens[$stackPtr]['type']) {
            case 'T_NAMESPACE':
                $this->processNamespace($phpcsFile, $stackPtr);
                break;

            case 'T_USE':
                $this->processUse($phpcsFile, $stackPtr);
                break;

            case 'T_DOC_COMMENT_OPEN_TAG':
                $this->processDocblock($phpcsFile, $stackPtr);
                break;

            default:
                throw new \LogicException('Unhandled token type: ' . $tokens[$stackPtr]['type']);
        }
    }

    /**
     * @return string[]
     */
    public function getImports(): array
    {
        return $this->imports;
    }

    /**
     * @return null|string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * @param File $phpcsFile
     * @param int $stackPtr
     * @return string
     */
    private function getDocblockContent(File $phpcsFile, int $stackPtr): string
    {
        $tokens = $phpcsFile->getTokens();
        $content = '';

        for ($tokenIndex = $stackPtr; $tokenIndex <= $tokens[$stackPtr]['comment_closer']; $tokenIndex++) {
            $content .= $tokens[$tokenIndex]['content'];
        }

        return $content;
    }

    /**
     * @param File $phpcsFile
     * @param int $stackPtr
     */
    private function processNamespace(File $phpcsFile, int $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if ('T_WHITESPACE' !== $tokens[$stackPtr + 1]['type']) {
            throw new \LogicException('Token after T_NAMESPACE not T_WHITESPACE');
        }

        $namespace = '';
        $stackPtr += 2;

        while ('T_SEMICOLON' !== $tokens[$stackPtr]['type']) {
            $namespace .= $tokens[$stackPtr]['content'];
            $stackPtr++;
        }

        $this->namespace = $namespace;
    }

    /**
     * @param File $phpcsFile
     * @param int $stackPtr
     */
    private function processUse(File $phpcsFile, int $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if ('T_WHITESPACE' !== $tokens[$stackPtr + 1]['type']) {
            throw new \LogicException('Token after T_USE not T_WHITESPACE');
        }

        $use = '';
        $stackPtr += 2;

        while (in_array($tokens[$stackPtr]['type'], ['T_STRING', 'T_NS_SEPARATOR'])) {
            $use .= $tokens[$stackPtr]['content'];
            $stackPtr++;
        }

        if ('T_WHITESPACE' === $tokens[$stackPtr]['type']) {
            $stackPtr++;
        }

        if ('T_AS' === $tokens[$stackPtr]['type']) {
            if ('T_WHITESPACE' !== $tokens[$stackPtr + 1]['type']) {
                throw new \LogicException('Token after T_AS not T_WHITESPACE');
            }

            if ('T_STRING' !== $tokens[$stackPtr + 2]['type']) {
                throw new \LogicException('T_STRING expected after [T_AS][T_WHITESPACE]');
            }

            $alias = $tokens[$stackPtr + 2]['content'];
            $stackPtr += 3;
        } else {
            $ptr = strrpos($use, '\\');

            if (false === $ptr) {
                $alias = $use;
            } else {
                $alias = substr($use, $ptr + 1);
            }
        }

        if ('T_WHITESPACE' === $tokens[$stackPtr]['type']) {
            $stackPtr++;
        }

        if ('T_SEMICOLON' !== $tokens[$stackPtr]['type']) {
            throw new \LogicException('Parse error after T_USE, T_SEMICOLON expected');
        }

        $this->imports[strtolower($alias)] = $use;
    }

    /**
     * @param File $phpcsFile
     * @param int $stackPtr
     * @return array
     */
    private function parseDocblockWithDoctrine(File $phpcsFile, int $stackPtr): array
    {
        $content = $this->getDocblockContent($phpcsFile, $stackPtr);

        $parser = new DocParser();
        $parser->setIgnoreNotImportedAnnotations(true);
        $parser->setImports($this->imports);

        return $parser->parse($content);
    }

    /**
     * @param File $phpcsFile
     * @param int $stackPtr
     */
    private function processDocblock(File $phpcsFile, int $stackPtr)
    {
        $annotations = $this->parseDocblockWithDoctrine($phpcsFile, $stackPtr);
        $this->sniffDocblock($phpcsFile, $stackPtr, $annotations);
    }
}
