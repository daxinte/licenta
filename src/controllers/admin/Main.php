<?php
namespace Controllers\Admin;

use Silex\Application
    ,Symfony\Component\HttpFoundation\Request
    ,Symfony\Component\HttpFoundation\Response
    ,Symfony\Component\HttpFoundation\RedirectResponse

    ;

class Main
{
    public function dashboard(Request $request, Application $app) {
        return $app['twig']->render('admin/dashboard.html', array());
    }

}
