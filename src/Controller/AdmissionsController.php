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
        $db   = getenv('DB_ADMISSION_DATABASE');
        $user = getenv('DB_ADMISSION_USERNAME');
        $pass = getenv('DB_PASSWORD');
        $this->dm = new DatabaseMethods($db, $user, $pass);
        $this->expose = new ExposeDataController($db, $user, $pass);
    }
}
