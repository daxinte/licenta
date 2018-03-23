<?php
namespace Controllers;

use Silex\Application
    ,Symfony\Component\HttpFoundation\Request
    ,Symfony\Component\HttpFoundation\Response
    ,Symfony\Component\HttpFoundation\RedirectResponse

    ;

class Store
{

    public function index(Request $request, Application $app) {

        //$mapper = $app['spot']->mapper('BSWS\Entity\Product');

        //$prod = $mapper->get(1);

        return "Store Homepage";
    }

}
