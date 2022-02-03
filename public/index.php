<?php

use App\Kernel;
use Symfony\Component\HttpFoundation\Session\Session;
//use App\Controller\UserController;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return function (array $context) {

    $session = new Session();
    $session->start();
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
