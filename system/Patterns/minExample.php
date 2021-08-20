<?php

use system\Patterns\Singleton;
use App\Interfaces\IFactory;
use system\Interfaces\IDataBase;
use system\Interfaces\IRouter;



class minExample extends Singleton
{
    private IFactory  $factory;
    private IRouter   $router;
    private IDataBase $db;


    public function initInstance(IFactory $factory, ?stdClass $params = null)
    {
      //parent::initInstance($factory);

        $this->factory = $factory;
        $this->router  = $this->factory->getRouter();
        $this->db      = $this->factory->getDB();
    }
}
