<?php

use
     Silex\Application
    ,Symfony\Component\HttpFoundation\Request
    ,Symfony\Component\HttpFoundation\Response
;

$app = new Application();
$app['debug'] = true;

//service registration
include "app_reg.php";

//routes
include "routes.php";


$app->error(function (\Exception $e, $code) {
    switch ($code) {
        case 404:
            $message = 'Aceasta pagina nu exista.';
            break;
        default:
            //echo $e->getStackTrace();
            $message = 'Ne pare rau, dar a aparut o eroare.';
            $message .= "<hr />".$e->getMessage();
    }

    return new Response($message);
});

return $app;
