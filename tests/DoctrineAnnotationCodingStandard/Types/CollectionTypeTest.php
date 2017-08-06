<?php declare(strict_types = 1);

namespace DoctrineAnnotationCodingStandardTests\Types;

use Doctrine\Common\Collections\Collection;
use DoctrineAnnotationCodingStandard\ImportClassMap;
use DoctrineAnnotationCodingStandard\Types\CollectionType;
use DoctrineAnnotationCodingStandard\Types\IntegerType;
use DoctrineAnnotationCodingStandard\Types\ObjectType;
use DoctrineAnnotationCodingStandard\Types\UnqualifiedObjectType;
use PHPUnit\Framework\TestCase;

class CollectionTypeTest extends TestCase
{
    public function testQualificationWithUnqualifiableItemType()
    {
        $type = new CollectionType(new IntegerType());
        $this->assertSame($type, $type->qualify(null, new ImportClassMap()));
    }

    public function testQualification()
    {
        $type = new CollectionType(new UnqualifiedObjectType('DateTime'));

        $qualifiedType = $type->qualify(null, new ImportClassMap());
        $this->assertEquals(new CollectionType(new ObjectType(\DateTime::class)), $qualifiedType);
    }

    public function testToStringNoImport()
    {
        $type = new CollectionType(new UnqualifiedObjectType('DateTime'));
        $this->assertSame(
            'DateTime[]|\\Doctrine\\Common\\Collections\\Collection',
            $type->toString('Something', new ImportClassMap())
        );
    }

    public function testToStringWithImport()
    {
        $type = new CollectionType(new UnqualifiedObjectType('DateTime'));

        $classMap = new ImportClassMap();
        $classMap->add('Collection', Collection::class);

        $this->assertSame(
            'DateTime[]|Collection',
            $type->toString('Something', $classMap)
        );
    }

    public function testToStringWithAliasedImport()
    {
        $type = new CollectionType(new UnqualifiedObjectType('DateTime'));

        $classMap = new ImportClassMap();
        $classMap->add('RenamedCollection', Collection::class);

        $this->assertSame(
            'DateTime[]|RenamedCollection',
            $type->toString('Something', $classMap)
        );
    }
}
