<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Category;
use App\Entity\Quiz;
use App\Entity\Questions;
use App\Form\QuizType;
use Symfony\Component\HttpFoundation\Request;

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
        $repositoryQuestions = $this->getDoctrine()->getRepository(Questions::class);
        $questions = $repositoryQuestions->findQuestions($slug);

        $repositoryQuiz = $this->getDoctrine()->getRepository(Quiz::class);
        $quizTitle = $repositoryQuiz->findQuizName($slug);
        $title = $quizTitle['name'];
        
        $quiz = new Quiz();
        for($i = 0; $i < count($questions); $i++) {
            $quiz->getQuestions()->add(NULL);
        }
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
    public function resultQuiz(string $slug, Request $request) {
        $quiz = new Quiz();
        $quizForm = $this->createForm(QuizType::class, $quiz);
        $quizForm->handleRequest($request);


        if($quizForm->isSubmitted() && $quizForm->isValid()) {
            $repositoryQuestions = $this->getDoctrine()
                ->getRepository(Questions::class);
            $questions = $repositoryQuestions->findQuestions($slug);
            $quiz = $quizForm->getData();
            $submittedQuestions = new Questions();
            $submittedQuestions = $quiz->getQuestions();

            foreach($submittedQuestions as $answer) {
                $answers[]['answer'] = $answer->getAnswer();
            }

            $goodAnswers = 0;
            for($i = 0; $i < count($questions); $i++) {
                if($questions[$i]->getAnswer() == $answers[$i]['answer']) {
                    $goodAnswers++;
                    $answers[$i]['correct'] = 1;
                    }
                else {
                    $answers[$i]['correct'] = 0;
                }
            }
        }
        else {
            dump($quizForm, $request);
            return $this->render('test/test.html.twig', [
                'title' => 'title',
            ]);
        }
        
        return $this->render('quiz/result.html.twig', [
            'title' => "Wyniki",
            'questions' => $questions,
            'answers' => $answers,
            'goodAnswers' => $goodAnswers,
            'countQuestions' => count($questions),
        ]);
    }
}