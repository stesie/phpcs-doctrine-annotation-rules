<?php declare(strict_types = 1);

namespace DoctrineAnnotationCodingStandardTests\Types;

use DoctrineAnnotationCodingStandard\ImportClassMap;
use DoctrineAnnotationCodingStandard\Types\ArrayType;
use DoctrineAnnotationCodingStandard\Types\IntegerType;
use DoctrineAnnotationCodingStandard\Types\MixedType;
use DoctrineAnnotationCodingStandard\Types\ObjectType;
use DoctrineAnnotationCodingStandard\Types\UnqualifiedObjectType;
use PHPUnit\Framework\TestCase;

class ArrayTypeTest extends TestCase
{
    public function testQualificationWithUnqualifiableItemType()
    {
        $type = new ArrayType(new IntegerType());
        $this->assertSame($type, $type->qualify(null, new ImportClassMap()));
    }

    public function testQualification()
    {
        $type = new ArrayType(new UnqualifiedObjectType('DateTime'));
        $this->assertEquals(new ArrayType(new ObjectType(\DateTime::class)), $type->qualify(null, new ImportClassMap()));
    }

    public function testToString()
    {
        $this->assertSame('int[]', (new ArrayType(new IntegerType()))->toString(null, new ImportClassMap()));
    }

    public function testToStringOnMixedArrayYieldsJustArray()
    {
        $this->assertSame('array', (new ArrayType(new MixedType()))->toString(null, new ImportClassMap()));
    }
}
