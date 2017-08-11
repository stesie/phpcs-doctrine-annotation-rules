<?php declare(strict_types = 1);

namespace DoctrineAnnotationCodingStandardTests\Helper;

use Doctrine\ORM\Mapping as ORM;
use DoctrineAnnotationCodingStandard\Helper\DocBlockHelper;
use DoctrineAnnotationCodingStandard\ImportClassMap;
use DoctrineAnnotationCodingStandardTests\Sniffs\Commenting\DummySniff;
use DoctrineAnnotationCodingStandardTests\Sniffs\TestCase;

class DocBlockHelperTest extends TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetVarTagContentThrowsIfCalledOnWrongToken()
    {
        $file = $this->checkString('/** @var foo */', DummySniff::class);
        DocBlockHelper::getVarTagContent($file, 0);
    }

    /**
     * @expectedException \OutOfRangeException
     */
    public function testGetVarTagContentThrowsIfStackPtrBeyondEof()
    {
        $file = $this->checkString('/** @var foo */', DummySniff::class);
        DocBlockHelper::getVarTagContent($file, count($file->getTokens()));
    }

    public function testGetVarTagContentPlain()
    {
        $file = $this->checkString('/** @var foo */', DummySniff::class);
        $this->assertSame('foo', DocBlockHelper::getVarTagContent($file, 1));
    }

    public function testGetVarTagNoContent()
    {
        $file = $this->checkString('/** @var*/', DummySniff::class);
        $this->assertSame('', DocBlockHelper::getVarTagContent($file, 1));
    }

    public function testGetVarTagWithJustSpaces()
    {
        $file = $this->checkString('/** @var   */', DummySniff::class);
        $this->assertSame('', DocBlockHelper::getVarTagContent($file, 1));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testFindTagByClassThrowsIfCalledOnWrongToken()
    {
        $file = $this->checkString('/** @var foo */', DummySniff::class);
        $classMap = new ImportClassMap();

        DocBlockHelper::findTagByClass($file, 0, $classMap, \stdClass::class);
    }

    /**
     * @expectedException \OutOfRangeException
     */
    public function testFindTagByClassThrowsIfStackPtrBeyondEof()
    {
        $file = $this->checkString('/** @var foo */', DummySniff::class);
        $classMap = new ImportClassMap();

        DocBlockHelper::findTagByClass($file, count($file->getTokens()), $classMap, \stdClass::class);
    }

    public function testFindTagByClass()
    {
        $file = $this->checkString('/** @ORM\JoinColumn() */', DummySniff::class);

        $classMap = new ImportClassMap();
        $classMap->add('ORM', 'Doctrine\\ORM\\Mapping');

        $tag = DocBlockHelper::findTagByClass($file, 1, $classMap, ORM\JoinColumn::class);
        $this->assertSame('()', $tag);
    }

    public function testFindTagByClassWithSpaces()
    {
        $file = $this->checkString('/** @ORM\JoinColumn   () */', DummySniff::class);

        $classMap = new ImportClassMap();
        $classMap->add('ORM', 'Doctrine\\ORM\\Mapping');

        $tag = DocBlockHelper::findTagByClass($file, 1, $classMap, ORM\JoinColumn::class);
        $this->assertSame('()', $tag);
    }

    public function testFindTagByClassWithSpacesWithinContent()
    {
        $file = $this->checkString('/** @ORM\JoinColumn(onDelete="CASCADE", nullable=true) */', DummySniff::class);

        $classMap = new ImportClassMap();
        $classMap->add('ORM', 'Doctrine\\ORM\\Mapping');

        $tag = DocBlockHelper::findTagByClass($file, 1, $classMap, ORM\JoinColumn::class);
        $this->assertSame('(onDelete="CASCADE", nullable=true)', $tag);
    }

    public function testFindTagMissing()
    {
        $file = $this->checkString('/** @ORM\Column() */', DummySniff::class);

        $classMap = new ImportClassMap();
        $classMap->add('ORM', 'Doctrine\\ORM\\Mapping');

        $tag = DocBlockHelper::findTagByClass($file, 1, $classMap, ORM\JoinColumn::class);
        $this->assertSame(null, $tag);
    }
}
