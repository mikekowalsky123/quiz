<?php
namespace App\Service;

use App\Entity\Questions;
use App\Entity\Quiz;
use Doctrine\Common\Collections\ArrayCollection;

class CheckAnswers {
    public function checkAndGetAnswers(ArrayCollection $submittedAnswers, array $quizAnswers) {
        //fetching submittedAnswers to array, because array is easier to compare  
        foreach($submittedAnswers as $answer) {
            $subAnsArr[]['answer'] = $answer->getAnswer();
        }
        //defining a counter of good answers
        $goodAnswersCount = 0;

        //counting good answers and setting submitted as correct (or uncorrect in "else" statement)
        for($i = 0; $i < count($subAnsArr); $i++) {
            if($quizAnswers[$i]->getAnswer() == $subAnsArr[$i]['answer']) {
                $goodAnswersCount++;
                $subAnsArr[$i]['correct'] = 1;
                }
            else {
                $subAnsArr[$i]['correct'] = 0;
            }
        }
        
        //returning an array with count of good answers and submitted answers
        return [
            'goodAnswersCount' => $goodAnswersCount,
            'yourAnswers' => $subAnsArr,
        ];
    }
}