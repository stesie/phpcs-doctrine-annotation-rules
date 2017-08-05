<?php declare(strict_types = 1);

namespace DoctrineAnnotationCodingStandardTests\Helper;

use Doctrine\ORM\Mapping;
use DoctrineAnnotationCodingStandard\Helper\DoctrineMappingHelper;
use PHPUnit\Framework\TestCase;

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
    public function testIsDoctrineJoin(string $className, array $mappingInfo)
    {
        $annotation = new $className();
        $this->assertSame($mappingInfo['isJoin'], DoctrineMappingHelper::isDoctrineJoin([$annotation]));
    }

    /**
     * @return array
     */
    public function annotationProvider(): array
    {
        return [
            [ 'class' => Mapping\Column::class, 'mappingInfo' => [ 'isMapped' => true, 'isJoin' => false ]],
            [ 'class' => Mapping\Embedded::class, 'mappingInfo' => [ 'isMapped' => true, 'isJoin' => false ]],
            [ 'class' => Mapping\OneToOne::class, 'mappingInfo' => [ 'isMapped' => true, 'isJoin' => true ]],
            [ 'class' => Mapping\OneToMany::class, 'mappingInfo' => [ 'isMapped' => true, 'isJoin' => true ]],
            [ 'class' => Mapping\ManyToOne::class, 'mappingInfo' => [ 'isMapped' => true, 'isJoin' => true ]],
            [ 'class' => Mapping\ManyToMany::class, 'mappingInfo' => [ 'isMapped' => true, 'isJoin' => true ]],

            [ 'class' => \stdClass::class, 'mappingInfo' => [ 'isMapped' => false, 'isJoin' => false ]],
        ];
    }
}
