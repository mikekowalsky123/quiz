<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Menu;


class MenuController extends AbstractController
{
    /**
     * @Route("/menu", name="app_menu")
     */
    public function menu()
    {
        $menu = $this->getDoctrine()
            ->getRepository(Menu::class)
            ->findAll();

        return $this->render('menu.html.twig', [
            'menu' => $menu,
        ]);
    }
}
