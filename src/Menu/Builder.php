<?php
/**
 * Created by PhpStorm.
 * User: kendoctor
 * Date: 6/11/18
 * Time: 9:24 PM
 */

namespace App\Menu;


use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class Builder
{

    /** @var FactoryInterface */
    private $factory;

    public function __construct(FactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    public function createMainMenu(RequestStack $requestStack)
    {
        $request = $requestStack->getCurrentRequest();

        $menu = $this->factory->createItem('root', [
            'childrenAttributes' => ['class' => 'main-menu']
        ]);
        $menu->addChild('menu.homepage', ['route' => 'home']);
        $menu->addChild('menu.wall_painting_index' , ['route'=>'wall_painting_index']);

        $menu->addChild('menu.services', ['route'=> 'static_message_category', 'routeParameters' => ['token'=>'services']]);
        $menu->addChild('menu.training', ['route'=> 'static_message_category', 'routeParameters' => ['token'=>'training']]);
        $menu->addChild('menu.about', ['route'=> 'static_message_category', 'routeParameters' => ['token'=>'about']]);

        $params = array_merge($request->attributes->get('_route_params'), $request->query->all());
        $route = $request->attributes->get('_route');

        if ($request->getLocale() == 'en') {
            $params = array_merge($params, ['_locale' => 'zh_CN']);
            $menu->addChild('base.locale.zh_CN', ['route' => $route, 'routeParameters'=> $params, 'attributes' => ['class' => 'locale-switch']] );
        }
        else
        {
            $params = array_merge($params, ['_locale' => 'en']);
            $menu->addChild('base.locale.en', ['route' => $route, 'routeParameters'=> $params, 'attributes' => ['class' => 'locale-switch']] );
        }

        return $menu;

    }
}