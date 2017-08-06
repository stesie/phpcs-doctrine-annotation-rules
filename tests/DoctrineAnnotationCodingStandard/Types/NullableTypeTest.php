<?php declare(strict_types = 1);

namespace DoctrineAnnotationCodingStandardTests\Types;

use DoctrineAnnotationCodingStandard\ImportClassMap;
use DoctrineAnnotationCodingStandard\Types\IntegerType;
use DoctrineAnnotationCodingStandard\Types\NullableType;
use DoctrineAnnotationCodingStandard\Types\ObjectType;
use DoctrineAnnotationCodingStandard\Types\UnqualifiedObjectType;
use PHPUnit\Framework\TestCase;

class NullableTypeTest extends TestCase
{
    public function testQualificationWithUnqualifiableItemType()
    {
        $type = new NullableType(new IntegerType());
        $this->assertSame($type, $type->qualify(null, new ImportClassMap()));
    }

    public function testQualification()
    {
        $type = new NullableType(new UnqualifiedObjectType('DateTime'));

        $qualifiedType = $type->qualify(null, new ImportClassMap());
        $this->assertEquals(new NullableType(new ObjectType(\DateTime::class)), $qualifiedType);
    }

    public function testToString()
    {
        $this->assertSame('int|null', (new NullableType(new IntegerType()))->toString(null, new ImportClassMap()));
    }
}
