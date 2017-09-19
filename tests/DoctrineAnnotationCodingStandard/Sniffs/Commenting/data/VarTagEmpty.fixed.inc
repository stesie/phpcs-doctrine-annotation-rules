<?php

namespace Foo\Bar;

use Doctrine\ORM\Mapping as ORM;

class VarTagEmpty
{
    /**
     * @var int
     *
     * @ORM\Column(name="foo", type="integer")
     */
    private $foo;
}