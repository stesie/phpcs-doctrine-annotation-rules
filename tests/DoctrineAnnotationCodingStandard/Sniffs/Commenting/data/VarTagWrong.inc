<?php

namespace Foo\Bar;

use Doctrine\ORM\Mapping as ORM;

class VarTagWrong
{
    /**
     * @ORM\Column(name="foo", type="integer")
     * @var string
     */
    private $foo;
}