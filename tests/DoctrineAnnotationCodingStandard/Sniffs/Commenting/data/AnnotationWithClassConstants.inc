<?php

namespace Foo\Bar;

use Doctrine\ORM\Mapping as ORM;
use PHPUnit\Framework\Constraint\IsType;

class AnnotationWithClassConstants
{
    /**
     * @var string
     *
     * @ORM\Column(type="string", options={"default": IsType::TYPE_ARRAY})
     */
    private $foo;
}
