<?php declare(strict_types = 1);

namespace DoctrineAnnotationCodingStandard;

class ImportClassMap
{
    /**
     * @var string[]
     */
    private $forwardMap = [];

    /**
     * @var string[]
     */
    private $backwardMap = [];

    public function add(string $alias, string $fqcn)
    {
        $this->forwardMap[strtolower($alias)] = $fqcn;
        $this->backwardMap[$fqcn] = $alias;
    }

    public function classByAlias(string $alias): string
    {
        return $this->forwardMap[strtolower($alias)];
    }

    public function aliasByClass(string $className): string
    {
        return $this->backwardMap[$className];
    }

    public function hasAlias(string $alias): bool
    {
        return isset($this->forwardMap[strtolower($alias)]);
    }

    /**
     * @return string[]
     */
    public function toArray(): array
    {
        return $this->forwardMap;
    }
}
