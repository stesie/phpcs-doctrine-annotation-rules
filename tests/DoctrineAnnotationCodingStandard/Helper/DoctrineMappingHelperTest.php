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
     * @param bool $isMapped
     */
    public function testIsDoctrineMappedProperty(string $className, bool $isMapped)
    {
        $annotation = new $className();
        $this->assertSame($isMapped, DoctrineMappingHelper::isDoctrineMappedProperty([$annotation]));
    }

    /**
     * @return array
     */
    public function annotationProvider(): array
    {
        return [
            [ 'class' => Mapping\Column::class, 'isMapped' => true, 'isJoin' => false ],
            [ 'class' => Mapping\Embedded::class, 'isMapped' => true, 'isJoin' => false ],
            [ 'class' => Mapping\OneToOne::class, 'isMapped' => true, 'isJoin' => true ],
            [ 'class' => Mapping\OneToMany::class, 'isMapped' => true, 'isJoin' => true ],
            [ 'class' => Mapping\ManyToOne::class, 'isMapped' => true, 'isJoin' => true ],
            [ 'class' => Mapping\ManyToMany::class, 'isMapped' => true, 'isJoin' => true ],

            [ 'class' => \stdClass::class, 'isMapped' => false, 'isJoin' => false ],
        ];
    }
}
