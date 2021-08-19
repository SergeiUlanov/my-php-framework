<?php

namespace system\Patterns;

use App\Interfaces\IFactory;



class Controller
{
    protected IFactory $factory;


    public function __construct(IFactory $factory)
    {
        $this->factory = $factory;
    }
}