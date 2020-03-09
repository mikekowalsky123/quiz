<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Category;
use App\Entity\Quiz;
use App\Entity\Questions;
use App\Form\QuizType;
use Symfony\Component\HttpFoundation\Request;
use App\Service\CheckAnswers;

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
            'title' => 'Kategorie',
            'categories' => $categories,
        ]);
    }

    /**
     * @Route("/quiz/{slug}", name="app_quiz_list")
     */
    public function listQuiz(string $slug) {
        $repositoryQuiz = $this->getDoctrine()->getRepository(Quiz::class);
        $quizes = $repositoryQuiz->findByCategory($slug);
        
        $repositoryCat = $this->getDoctrine()
            ->getRepository(Category::class);
        $category = $repositoryCat->findCategoryName($slug);
        $title = $category['name'];
        dump($quizes);
        return $this->render('quiz/list.html.twig', [
            'title' => $title,
            'quizes' => $quizes,
        ]);
    }

    /**
     * @Route("/quiz/contest/{slug}", name="app_quiz_contest")
     */
    public function contestQuiz(string $slug, Request $request) {
        //getting questions
        $repositoryQuestions = $this->getDoctrine()->getRepository(Questions::class);
        $questions = $repositoryQuestions->findQuestions($slug);
        //getting title
        $repositoryQuiz = $this->getDoctrine()->getRepository(Quiz::class);
        $quizTitle = $repositoryQuiz->findQuizName($slug);
        $title = $quizTitle['name'];
        //preparing form - adding fields with NULL values to avoid giving answer to user while contesting
        $quiz = new Quiz();
        for($i = 0; $i < count($questions); $i++) {
            $quiz->getQuestions()->add(NULL);
        }
        //creating form
        $quizForm = $this->createForm(QuizType::class, $quiz, [
            'action' => $this->generateUrl('app_quiz_result', [
                'slug' => $slug,
            ]),
        ]);
        return $this->render('quiz/contest.html.twig', [
            'title' => $title,
            'questions' => $questions,
            'quizForm' => $quizForm->createView(),
        ]);
    }
    
    /**
     * @Route("/quiz/result/{slug}", name="app_quiz_result")
     */
    public function resultQuiz(string $slug, Request $request, CheckAnswers $check) {
        //handling form
        $submittedQuiz = new Quiz();
        $quizForm = $this->createForm(QuizType::class, $submittedQuiz);
        $quizForm->handleRequest($request);


        if($quizForm->isSubmitted() && $quizForm->isValid()) {
            //finding correct answers
            $repositoryQuestions = $this->getDoctrine()
                ->getRepository(Questions::class);
            $goodAnswers = $repositoryQuestions->findQuestions($slug);

            //getting data from form and fetching them to array
            $submittedQuiz = $quizForm->getData();
            $submittedAnswers = new Questions();
            $submittedAnswers = $submittedQuiz->getQuestions();

            //getting result - good answers and count of them
            $result = $check->checkAndGetAnswers($submittedAnswers, $goodAnswers);
        }
        return $this->render('quiz/result.html.twig', [
            'title' => "Wyniki",
            'goodAnswers' => $goodAnswers,
            'result' => $result,
            'countQuestions' => count($goodAnswers),
        ]);
    }
}