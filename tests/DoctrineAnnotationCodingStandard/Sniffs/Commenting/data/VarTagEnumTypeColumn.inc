<?php

namespace Foo\Bar;

use Doctrine\ORM\Mapping as ORM;

class VarTagCorrect
{
    /**
     * @ORM\Column(type="CustomerType", nullable=false)
     * @DoctrineAssert\Enum(entity="AppBundle\DBAL\Types\CustomerType")
     * @var string
     */
    private $customerType;
}