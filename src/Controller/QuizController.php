<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Category;
use App\Entity\Quiz;


class QuizController extends AbstractController
{
    /**
     * @Route("/quiz", name="app_quiz")
     */
    public function categoriesQuiz() {
        $categories = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findAll();

        return $this->render('quiz/categories.html.twig', [
            'title' => 'Lista quizów',
            'categories' => $categories,
        ]);
    }

    /**
     * @Route("/quiz/{slug}", name="app_quiz_list")
     */
    public function listQuiz(string $slug) {
        return $this->render('quiz/list.html.twig', [
            'title' => $slug,
        ]);
    }
}