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

class Builder
{

    /** @var FactoryInterface */
    private $factory;

    public function __construct(FactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    public function createMainMenu()
    {
        $menu = $this->factory->createItem('root', [
            'childrenAttributes' => ['class' => 'main-menu']
        ]);
        $menu->addChild('menu.homepage', ['route' => 'home']);
        $menu->addChild('menu.wall_painting_index' , ['route'=>'wall_painting_index']);

        return $menu;

    }
}