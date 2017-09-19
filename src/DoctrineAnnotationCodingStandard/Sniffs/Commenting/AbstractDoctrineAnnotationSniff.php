<?php declare(strict_types = 1);

namespace DoctrineAnnotationCodingStandard\Sniffs\Commenting;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\DocParser;
use DoctrineAnnotationCodingStandard\Exception\ParseErrorException;
use DoctrineAnnotationCodingStandard\ImportClassMap;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

abstract class AbstractDoctrineAnnotationSniff implements Sniff
{
    /**
     * @var ImportClassMap
     */
    private $imports;

    /**
     * The current file's namespace, if any
     *
     * @var string|null
     */
    private $namespace;

    /**
     * @var File|null
     */
    private $lastSeenFile;

    /**
     * @param File $phpcsFile
     * @param int $stackPtr
     * @param array $annotations
     */
    abstract protected function sniffDocblock(File $phpcsFile, int $stackPtr, array $annotations);

    public function __construct()
    {
        $this->resetState();
    }

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
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
     * @param File $phpcsFile
     * @param int $stackPtr
     * @return int|void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        if ($phpcsFile !== $this->lastSeenFile) {
            $this->resetState();
            $this->lastSeenFile = $phpcsFile;
        }

        $tokens = $phpcsFile->getTokens();

        switch ($tokens[$stackPtr]['type']) {
            case 'T_NAMESPACE':
                $this->resetState();
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
     * @return ImportClassMap
     */
    public function getImports(): ImportClassMap
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

        if ($tokens[$stackPtr + 1]['type'] !== 'T_WHITESPACE') {
            throw new ParseErrorException('Token after T_NAMESPACE not T_WHITESPACE');
        }

        $namespace = '';
        $stackPtr += 2;

        while (in_array($tokens[$stackPtr]['type'], ['T_STRING', 'T_NS_SEPARATOR'])) {
            $namespace .= $tokens[$stackPtr]['content'];
            $stackPtr++;
        }

        if ($tokens[$stackPtr]['type'] === 'T_WHITESPACE') {
            $stackPtr++;
        }

        if ($tokens[$stackPtr]['type'] !== 'T_SEMICOLON') {
            throw new ParseErrorException('Parse error after T_NAMESPACE, T_SEMICOLON expected');
        }

        if (empty($namespace)) {
            throw new ParseErrorException('Empty namespace not valid');
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

        if ($this->isTraitUse($phpcsFile, $stackPtr)) {
            return;
        }

        if ($tokens[$stackPtr + 1]['type'] === 'T_OPEN_PARENTHESIS') {
            // ignore "function use(...)"
            return;
        }

        if ($tokens[$stackPtr + 1]['type'] !== 'T_WHITESPACE') {
            throw new ParseErrorException('Token after T_USE not T_WHITESPACE');
        }

        $use = '';
        $stackPtr += 2;

        if ($tokens[$stackPtr]['type'] === 'T_OPEN_PARENTHESIS') {
            // ignore "function use (...)"
            return;
        }

        while (in_array($tokens[$stackPtr]['type'], ['T_STRING', 'T_NS_SEPARATOR'])) {
            $use .= $tokens[$stackPtr]['content'];
            $stackPtr++;
        }

        if ($tokens[$stackPtr]['type'] === 'T_WHITESPACE') {
            $stackPtr++;
        }

        if ($tokens[$stackPtr]['type'] === 'T_AS') {
            if ($tokens[$stackPtr + 1]['type'] !== 'T_WHITESPACE') {
                throw new ParseErrorException('Token after T_AS not T_WHITESPACE');
            }

            if ($tokens[$stackPtr + 2]['type'] !== 'T_STRING') {
                throw new ParseErrorException('T_STRING expected after [T_AS][T_WHITESPACE]');
            }

            $alias = $tokens[$stackPtr + 2]['content'];
            $stackPtr += 3;
        } else {
            $ptr = strrpos($use, '\\');

            if ($ptr === false) {
                $alias = $use;
            } else {
                $alias = substr($use, $ptr + 1);
            }
        }

        if ($tokens[$stackPtr]['type'] === 'T_WHITESPACE') {
            $stackPtr++;
        }

        if ($tokens[$stackPtr]['type'] !== 'T_SEMICOLON') {
            throw new ParseErrorException(
                \sprintf('Parse error on line %d: after T_USE, T_SEMICOLON expected', $tokens[$stackPtr]['line'])
            );
        }

        $this->imports->add($alias, $use);
    }

    /**
     * @param File $phpcsFile
     * @param int $stackPtr
     * @return array
     */
    private function parseDocblockWithDoctrine(File $phpcsFile, int $stackPtr): array
    {
        $content = $this->getDocblockContent($phpcsFile, $stackPtr);

        $imports = array_merge($this->imports->toArray(), ['__NAMESPACE__' => $this->namespace]);

        $parser = new DocParser();
        $parser->setIgnoreNotImportedAnnotations(true);
        $parser->setImports($imports);

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

    private function resetState()
    {
        $this->imports = new ImportClassMap();
        $this->namespace = null;
    }

    /**
     * @param File $phpcsFile
     * @param int $usePtr
     * @return bool
     */
    private function isTraitUse(File $phpcsFile, int $usePtr): bool
    {
        return $phpcsFile->findPrevious([T_CLASS, T_ANON_CLASS, T_TRAIT, T_INTERFACE], $usePtr) !== false;
    }
}
