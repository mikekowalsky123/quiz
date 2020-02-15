<?php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController {
    /**
     * @Route("/", name="app_homepage")
     */
    public function homepage() {
        return $this->render('main/homepage.html.twig', [
            'title' => 'Strona Główna',
        ]);
    }
}