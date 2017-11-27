<?php declare(strict_types = 1);

namespace DoctrineAnnotationCodingStandardTests\Helper;

use Doctrine\ORM\Mapping;
use DoctrineAnnotationCodingStandard\Helper\DoctrineMappingHelper;
use DoctrineAnnotationCodingStandard\Types\AnyObjectType;
use DoctrineAnnotationCodingStandard\Types\ArrayType;
use DoctrineAnnotationCodingStandard\Types\BooleanType;
use DoctrineAnnotationCodingStandard\Types\CollectionType;
use DoctrineAnnotationCodingStandard\Types\FloatType;
use DoctrineAnnotationCodingStandard\Types\IntegerType;
use DoctrineAnnotationCodingStandard\Types\MixedType;
use DoctrineAnnotationCodingStandard\Types\NullableType;
use DoctrineAnnotationCodingStandard\Types\ObjectType;
use DoctrineAnnotationCodingStandard\Types\ResourceType;
use DoctrineAnnotationCodingStandard\Types\StringType;
use DoctrineAnnotationCodingStandard\Types\Type;
use DoctrineAnnotationCodingStandard\Types\UnqualifiedObjectType;
use DoctrineAnnotationCodingStandardTests\Sniffs\Commenting\DummySniff;
use DoctrineAnnotationCodingStandardTests\Sniffs\TestCase;

class DoctrineMappingHelperTest extends TestCase
{
    /**
     * @dataProvider annotationProvider
     * @param string $className
     * @param bool[] $mappingInfo
     */
    public function testIsDoctrineMappedProperty(string $className, array $mappingInfo)
    {
        $annotation = new $className();
        $this->assertSame($mappingInfo['isMapped'], DoctrineMappingHelper::isDoctrineMappedProperty([$annotation]));
    }

    /**
     * @dataProvider annotationProvider
     * @param string $className
     * @param bool[] $mappingInfo
     */
    public function testIsMappedDoctrineJoin(string $className, array $mappingInfo)
    {
        $annotation = new $className();
        $this->assertSame($mappingInfo['isToOneJoin'], DoctrineMappingHelper::isMappedDoctrineToOneJoin([$annotation]));
    }

    /**
     * @return array
     */
    public function annotationProvider(): array
    {
        return [
            [ 'class' => Mapping\Column::class, 'mappingInfo' => [ 'isMapped' => true, 'isToOneJoin' => false ]],
            [ 'class' => Mapping\Embedded::class, 'mappingInfo' => [ 'isMapped' => true, 'isToOneJoin' => false ]],
            [ 'class' => Mapping\OneToOne::class, 'mappingInfo' => [ 'isMapped' => true, 'isToOneJoin' => true ]],
            [ 'class' => Mapping\OneToMany::class, 'mappingInfo' => [ 'isMapped' => true, 'isToOneJoin' => false ]],
            [ 'class' => Mapping\ManyToOne::class, 'mappingInfo' => [ 'isMapped' => true, 'isToOneJoin' => true ]],
            [ 'class' => Mapping\ManyToMany::class, 'mappingInfo' => [ 'isMapped' => true, 'isToOneJoin' => false ]],

            [ 'class' => \stdClass::class, 'mappingInfo' => [ 'isMapped' => false, 'isToOneJoin' => false ]],
        ];
    }

    public function testIsMappedDoctrineJoinOnInverseSide()
    {
        $annotation = new Mapping\OneToOne();
        $annotation->mappedBy = 'someFiled';

        $this->assertFalse(DoctrineMappingHelper::isMappedDoctrineToOneJoin([$annotation]));
    }

    /**
     * @dataProvider doctrineTypeMappingProvider
     * @param string $doctrineType
     * @param Type $phpType
     */
    public function testGetTypeFromDoctrineType(string $doctrineType, Type $phpType)
    {
        $this->assertEquals($phpType, DoctrineMappingHelper::getTypeFromDoctrineType($doctrineType));
    }

    /**
     * @return mixed[][]
     */
    public function doctrineTypeMappingProvider(): array
    {
        return [
            [ 'smallint', new IntegerType() ],
            [ 'integer', new IntegerType() ],
            [ 'bigint', new StringType() ],

            [ 'decimal', new StringType() ],
            [ 'float', new FloatType() ],

            [ 'string', new StringType() ],
            [ 'text', new StringType() ],
            [ 'guid', new StringType() ],

            [ 'binary', new ResourceType() ],
            [ 'blob', new ResourceType() ],

            [ 'boolean', new BooleanType() ],
            [ 'date', new ObjectType(\DateTime::class) ],
            [ 'date_immutable', new ObjectType(\DateTimeImmutable::class) ],
            [ 'datetime', new ObjectType(\DateTime::class) ],
            [ 'datetime_immutable', new ObjectType(\DateTimeImmutable::class) ],
            [ 'datetimez', new ObjectType(\DateTime::class) ],
            [ 'datetimez_immutable', new ObjectType(\DateTimeImmutable::class) ],
            [ 'time', new ObjectType(\DateTime::class) ],
            [ 'time_immutable', new ObjectType(\DateTimeImmutable::class) ],
            [ 'dateinterval', new ObjectType(\DateInterval::class) ],

            [ 'array', new ArrayType(new MixedType()) ],
            [ 'simple_array', new ArrayType(new MixedType()) ],
            [ 'json', new ArrayType(new MixedType()) ],
            [ 'json_array', new ArrayType(new MixedType()) ],

            [ 'object', new AnyObjectType() ],
        ];
    }

    public function testGetTypeFromAnnotationMappedColumn()
    {
        $this->checkString('use Doctrine\ORM\Mapping as ORM; /** @ORM\Column(type="date") */', DummySniff::class);
        $annotations = $this->getSniff()->getAnnotations();

        $this->assertEquals(new ObjectType(\DateTime::class), DoctrineMappingHelper::getMappedType($annotations));
    }

    public function testGetTypeFromAnnotationMappedNullableColumn()
    {
        $this->checkString('use Doctrine\ORM\Mapping as ORM; /** @ORM\Column(type="date", nullable=true) */', DummySniff::class);
        $annotations = $this->getSniff()->getAnnotations();

        $this->assertEquals(new NullableType(new ObjectType(\DateTime::class)), DoctrineMappingHelper::getMappedType($annotations));
    }

    public function testGetTypeFromAnnotationMappedEmbedded()
    {
        $this->checkString('use Doctrine\ORM\Mapping as ORM; /** @ORM\Embedded(class="Foo") */', DummySniff::class);
        $annotations = $this->getSniff()->getAnnotations();

        $this->assertEquals(new UnqualifiedObjectType('Foo'), DoctrineMappingHelper::getMappedType($annotations));
    }

    public function testGetTypeFromAnnotationMappedOneToOne()
    {
        $this->checkString('use Doctrine\ORM\Mapping as ORM; /** @ORM\OneToOne(targetEntity="Foo") @ORM\JoinColumn(nullable=false) */', DummySniff::class);
        $annotations = $this->getSniff()->getAnnotations();

        $this->assertEquals(new UnqualifiedObjectType('Foo'), DoctrineMappingHelper::getMappedType($annotations));
    }

    public function testGetTypeFromAnnotationMappedOneToOneNullable()
    {
        $this->checkString('use Doctrine\ORM\Mapping as ORM; /** @ORM\OneToOne(targetEntity="Foo") */', DummySniff::class);
        $annotations = $this->getSniff()->getAnnotations();

        $this->assertEquals(new NullableType(new UnqualifiedObjectType('Foo')), DoctrineMappingHelper::getMappedType($annotations));
    }

    public function testGetTypeFromAnnotationMappedManyToOne()
    {
        $this->checkString('use Doctrine\ORM\Mapping as ORM; /** @ORM\ManyToOne(targetEntity="Foo") @ORM\JoinColumn(nullable=false) */', DummySniff::class);
        $annotations = $this->getSniff()->getAnnotations();

        $this->assertEquals(new UnqualifiedObjectType('Foo'), DoctrineMappingHelper::getMappedType($annotations));
    }

    public function testGetTypeFromAnnotationMappedManyToOneNullable()
    {
        $this->checkString('use Doctrine\ORM\Mapping as ORM; /** @ORM\ManyToOne(targetEntity="Foo") */', DummySniff::class);
        $annotations = $this->getSniff()->getAnnotations();

        $this->assertEquals(new NullableType(new UnqualifiedObjectType('Foo')), DoctrineMappingHelper::getMappedType($annotations));
    }

    public function testGetTypeFromAnnotationMappedOneToMany()
    {
        $this->checkString('use Doctrine\ORM\Mapping as ORM; /** @ORM\OneToMany(targetEntity="Foo") */', DummySniff::class);
        $annotations = $this->getSniff()->getAnnotations();

        $this->assertEquals(new CollectionType(new UnqualifiedObjectType('Foo')), DoctrineMappingHelper::getMappedType($annotations));
    }

    public function testGetTypeFromAnnotationMappedManyToMany()
    {
        $this->checkString('use Doctrine\ORM\Mapping as ORM; /** @ORM\ManyToMany(targetEntity="Foo") */', DummySniff::class);
        $annotations = $this->getSniff()->getAnnotations();

        $this->assertEquals(new CollectionType(new UnqualifiedObjectType('Foo')), DoctrineMappingHelper::getMappedType($annotations));
    }
}
