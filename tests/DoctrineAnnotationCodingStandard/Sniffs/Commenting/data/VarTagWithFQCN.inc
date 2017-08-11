<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

class VarTagCorrect
{
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Customer", inversedBy="services")
     * @ORM\JoinColumn(nullable=false)
     * @var Customer
     */
    private $customer;
}