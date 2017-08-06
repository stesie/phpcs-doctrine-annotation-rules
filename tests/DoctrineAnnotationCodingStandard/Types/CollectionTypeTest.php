<?php declare(strict_types = 1);

namespace DoctrineAnnotationCodingStandardTests\Types;

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
}
