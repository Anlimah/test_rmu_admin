<?php

namespace Src\Controller;

use Src\System\DatabaseMethods;
use Src\Controller\ExposeDataController;

class AdmissionsController
{
    private $dm = null;
    private $expose = null;

    public function __construct()
    {
        $this->dm = new DatabaseMethods();
        $this->expose = new ExposeDataController();
    }

    

}
