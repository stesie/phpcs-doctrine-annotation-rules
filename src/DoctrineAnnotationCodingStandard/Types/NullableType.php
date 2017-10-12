<?php declare(strict_types = 1);

namespace DoctrineAnnotationCodingStandard\Types;

use DoctrineAnnotationCodingStandard\ImportClassMap;

class NullableType implements Type, QualifyableObjectType
{
    /**
     * @var Type
     */
    private $itemType;

    /**
     * @var bool
     */
    private $maybe;

    public function __construct(Type $itemType, bool $maybe = false)
    {
        $this->itemType = $itemType;
        $this->maybe = $maybe;
    }

    /**
     * @param string|null $namespace
     * @param ImportClassMap $imports
     * @return string
     */
    public function toString(string $namespace = null, ImportClassMap $imports): string
    {
        return \sprintf('%s|null', $this->itemType->toString($namespace, $imports));
    }

    /**
     * @param Type $other
     * @return bool
     */
    public function isEqual(Type $other): bool
    {
        if ($this->maybe && $this->itemType->isEqual($other)) {
            return true;
        }

        return $other instanceof self &&
            $this->itemType->isEqual($other->itemType);
    }

    /**
     * @param string|null $namespace
     * @param ImportClassMap $imports
     * @param string $mode
     * @return Type
     */
    public function qualify(string $namespace = null, ImportClassMap $imports, string $mode = self::MODE_PHP_STANDARD): Type
    {
        if (!$this->itemType instanceof QualifyableObjectType) {
            return $this;
        }

        return new self($this->itemType->qualify($namespace, $imports, $mode), $this->maybe);
    }
}
