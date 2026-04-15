<?php
declare(strict_types=1);

namespace App\Core;

use Nette\Application\Routers\RouteList;

final class RouterFactory
{
    public static function createRouter(): RouteList
    {
        $router = new RouteList;
        $router->addRoute('login', 'Sign:in');
        $router->addRoute('register', 'Sign:register');
        $router->addRoute('logout', 'Sign:out');
        $router->addRoute('account', 'Account:default');
        $router->addRoute('admin/add', 'Admin:add');
        $router->addRoute('admin/edit/<id \d+>', 'Admin:edit');
        $router->addRoute('admin', 'Admin:default');
        $router->addRoute('men', 'Men:default');
        $router->addRoute('women', 'Women:default');
        $router->addRoute('product/<id \d+>', 'Product:detail');
        $router->addRoute('cart', 'Cart:default');
        $router->addRoute('', 'Homepage:default');
        return $router;
    }
}
