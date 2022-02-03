<?php

use App\Kernel;
use App\Controller\UserController;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return function (array $context) {

    /*session_start();

    if (isset($_GET['ctrl'], $_GET['action']) && !(empty($_GET['ctrl'])) && !(empty($_GET['action']))){
        $ctrl = $_GET['ctrl'];
        $action = $_GET['action'];
    } else {
         $ctrl = 'User';
         $action = 'display';
    }

    require_once dirname(__DIR__)."/src/Controller/" . $ctrl . 'Controller.php';
    $ctrl = $ctrl . 'Controller';
    //echo $ctrl;
    $controller = new UserController();
    $controller->$action();*/
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
//$controller->$action();
