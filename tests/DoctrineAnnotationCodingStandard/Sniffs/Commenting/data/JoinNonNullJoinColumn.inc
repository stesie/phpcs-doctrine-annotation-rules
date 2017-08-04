<?php

namespace Foo\Bar;

use Doctrine\ORM\Mapping as ORM;

class JoinNoJoinColumn
{
    /**
     * @ORM\ManyToOne(targetEntity="Address")
     * @ORM\JoinColumn(nullable=false)
     */
    private $foo;
}
