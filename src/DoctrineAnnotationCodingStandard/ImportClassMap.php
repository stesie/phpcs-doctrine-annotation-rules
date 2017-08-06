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

    /**
     * @param string $className
     * @return bool|string
     */
    public function aliasByClass(string $className)
    {
        return $this->backwardMap[$className] ?? false;
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
