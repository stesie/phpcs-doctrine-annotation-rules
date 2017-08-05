<?php declare(strict_types = 1);

namespace DoctrineAnnotationCodingStandardTests\Helper;

use Doctrine\ORM\Mapping as ORM;
use DoctrineAnnotationCodingStandard\Helper\DocBlockHelper;
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

    /**
     * @expectedException \DoctrineAnnotationCodingStandard\Exception\ParseErrorException
     */
    public function testGetVarTagNoContent()
    {
        $file = $this->checkString('/** @var*/', DummySniff::class);
        DocBlockHelper::getVarTagContent($file, 1);
    }

    /**
     * @expectedException \DoctrineAnnotationCodingStandard\Exception\ParseErrorException
     */
    public function testGetVarTagWithJustSpaces()
    {
        $file = $this->checkString('/** @var   */', DummySniff::class);
        DocBlockHelper::getVarTagContent($file, 1);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testFindTagByClassThrowsIfCalledOnWrongToken()
    {
        $file = $this->checkString('/** @var foo */', DummySniff::class);
        DocBlockHelper::findTagByClass($file, 0, [], \stdClass::class);
    }

    /**
     * @expectedException \OutOfRangeException
     */
    public function testFindTagByClassThrowsIfStackPtrBeyondEof()
    {
        $file = $this->checkString('/** @var foo */', DummySniff::class);
        DocBlockHelper::findTagByClass($file, count($file->getTokens()), [], \stdClass::class);
    }

    public function testFindTagByClass()
    {
        $file = $this->checkString('/** @ORM\JoinColumn() */', DummySniff::class);

        $tag = DocBlockHelper::findTagByClass($file, 1, ['orm' => 'Doctrine\\ORM\\Mapping'], ORM\JoinColumn::class);
        $this->assertSame('()', $tag);
    }

    public function testFindTagByClassWithSpaces()
    {
        $file = $this->checkString('/** @ORM\JoinColumn   () */', DummySniff::class);

        $tag = DocBlockHelper::findTagByClass($file, 1, ['orm' => 'Doctrine\\ORM\\Mapping'], ORM\JoinColumn::class);
        $this->assertSame('()', $tag);
    }

    public function testFindTagMissing()
    {
        $file = $this->checkString('/** @ORM\Column() */', DummySniff::class);

        $tag = DocBlockHelper::findTagByClass($file, 1, ['orm' => 'Doctrine\\ORM\\Mapping'], ORM\JoinColumn::class);
        $this->assertSame(null, $tag);
    }
}
