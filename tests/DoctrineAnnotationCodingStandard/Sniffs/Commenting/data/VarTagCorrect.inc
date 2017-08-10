<?php

namespace Foo\Bar;

use Doctrine\ORM\Mapping as ORM;

class VarTagCorrect
{
    /**
     * @ORM\Column(name="foo", type="integer")
     * @var int
     */
    private $foo;
}