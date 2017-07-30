<?php

namespace DoctrineAnnotations\Sniffs\Commenting;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\DocParser;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Embedded;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\OneToOne;
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
    abstract protected function sniffDocblock(File $phpcsFile, $stackPtr, $annotations);


    /**
     * @return int[]
     */
    public function register()
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
     */
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
     * @param File $phpcsFile
     * @param int $stackPtr
     * @return string
     */
    private function getDocblockContent(File $phpcsFile, $stackPtr)
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
    private function processNamespace(File $phpcsFile, $stackPtr)
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
    private function processUse(File $phpcsFile, $stackPtr)
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
     * @param $stackPtr
     * @return array
     */
    private function parseDocblockWithDoctrine(File $phpcsFile, $stackPtr)
    {
        $content = $this->getDocblockContent($phpcsFile, $stackPtr);

        $parser = new DocParser();
        $parser->setIgnoreNotImportedAnnotations(true);
        $parser->setImports($this->imports);

        return $parser->parse($content);
    }

    /**
     * @param array $annotations
     * @return bool
     */
    protected function isDoctrineMappedProperty($annotations)
    {
        foreach ($annotations as $doctrineTag) {
            switch (get_class($doctrineTag)) {
                case Column::class:
                case Embedded::class:
                case OneToOne::class:
                case OneToMany::class:
                case ManyToOne::class:
                case ManyToMany::class:
                    return true;
            }
        }

        return false;
    }

    /**
     * @param File $phpcsFile
     * @param int $stackPtr
     */
    private function processDocblock(File $phpcsFile, $stackPtr)
    {
        $annotations = $this->parseDocblockWithDoctrine($phpcsFile, $stackPtr);

        $this->sniffDocblock($phpcsFile, $stackPtr, $annotations);
    }
}