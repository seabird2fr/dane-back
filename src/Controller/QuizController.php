<?php

namespace App\Controller;

use App\Entity\AnswerHistory;
use App\Entity\Question;
use App\Entity\QuestionHistory;
use App\Entity\Quiz;
use App\Entity\User;
use App\Entity\Workout;
use App\Form\QuestionType;
use App\Form\QuizType;
use App\Repository\AnswerHistoryRepository;
use App\Repository\AnswerRepository;
use App\Repository\CategoryRepository;
use App\Repository\QuestionHistoryRepository;
use App\Repository\QuestionRepository;
use App\Repository\QuizRepository;
use App\Repository\WorkoutRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @Route("/quiz")
 */
class QuizController extends Controller
{


    /**
     * @Route("/results-eleve", name="quiz_result_eleve" )
     *@IsGranted("ROLE_TEACHER") 
     */
    public function viewResultEleve(WorkoutRepository $results,QuizRepository $quiz,Request $request)
    {

// on récupère toutes les quizzes disponibles
        $quizzes= $quiz->findAll();

        if (count($quizzes)>0){    
// on met dans un tableau les quiz pour créer le formulaire de choix des résultats dans twig
            foreach ($quizzes as $value) 
            {

                $choix_quiz[$value->getTitle()]=$value->getId();

            }
//dump($choix_quiz);


// création du formulaire 
            $form = $this->createFormBuilder()
            ->add('quiz', ChoiceType::class, [
                'label' => 'Choisissez le quiz ',
                'group_by' =>'null',
                'choices'  => [
                    $choix_quiz
                ],
                'attr' => array(
                    'onchange' => 'submit()',
                ),                   
            ])
            ->getForm();

        }
        else{

            $form = $this->createFormBuilder()
            ->add('quiz', ChoiceType::class, [
                'label' => 'Choisissez le quiz ',
                'group_by' =>'null',
                'choices'  => [
                    'Pas de quiz' => 'Pas de quiz'
                ],
                'attr' => array(
                    'onchange' => 'submit()',
                ),                   
            ])
            ->getForm();


        }




        $form->handleRequest($request);
//dump($request->request->get('form')['quiz']);


    // si le form est soumis
        if ($form->isSubmitted() && $form->isValid())
        {

            //dump($request->request->get('form')['quiz']);
            if ($results->findAll())
                $results=$results->findBy(array('quiz' => $request->request->get('form')['quiz']),array('score' => 'desc'));
            else $results=[];

            if (count($results)>0){

                foreach ($results as $result )
                {

//dump($result->getQuiz()->getTitle());
//dump($result->getStudent()->getUsername());
//dump($result->getScore());

                    $tabResults[$result->getStudent()->getUsername()][]=['id-quiz'=>$result->getQuiz()->getId(),'titre-quiz'=>$result->getQuiz()->getTitle(),'student'=>$result->getStudent()->getUsername(),'score'=>$result->getScore(),'nbr_passation'=>count($results)];

                }
            //dump($tabResults);


                return $this->render('quiz/showResultsStudent.html.twig',[
                    'results' => $tabResults,
                    'form' => $form->createView(),

                ]);


            }
            else
            {

                return $this->render('quiz/showResultsStudent.html.twig',[
                    'results' => null,
                    'form' => $form->createView(),

                ]);

            }






        } else

        {

            if ($results->findAll())
             $results=$results->findBy(array('quiz' => current($choix_quiz)),array('score' => 'desc'));
         else $results=[];

         if (count($results)>0){

            foreach ($results as $result )
            {



                $tabResults[$result->getStudent()->getUsername()][]=['id-quiz'=>$result->getQuiz()->getId(),'titre-quiz'=>$result->getQuiz()->getTitle(),'student'=>$result->getStudent()->getUsername(),'score'=>$result->getScore(),'nbr_passation'=>count($results)];

            }
            dump($tabResults);

            return $this->render('quiz/showResultsStudent.html.twig',[
                'results' => $tabResults,
                'form' => $form->createView(),

            ]);


        }
        else
        {

            return $this->render('quiz/showResultsStudent.html.twig',[
                'results' => null,
                'form' => $form->createView(),

            ]);

        }




    }



    return $this->render('quiz/showResultsStudent.html.twig',[
        'results' => null,
        'form' => $form->createView(),

    ]);


}



// AJOUT FONCTION SEABIRD POUR AFFICHAGE STATISTIQUES
/**
* @Route("/results", name="quiz_result" )
*@IsGranted("ROLE_TEACHER") 
*/
public function viewResult(Request $request, QuizRepository $quiz, WorkoutRepository $results, QuestionHistoryRepository $historyQuestion, QuestionRepository $question, AnswerRepository $answer, AnswerHistoryRepository $historyAnswer, CategoryRepository $categorie  ){






/*
// on récupère toutes les catégories disponibles
$categories= $categorie->findAll();
//dump($categories);
// on met dans un tableau les catégories pour créer le formulaire de choix des résultats
foreach ($categories as $value) 
    {

    $choix_quiz[$value->getLongname()]=$value->getId();

    }
*/


// on récupère toutes les quizzes disponibles
    $quizzes= $quiz->findAll();

    if (count($quizzes)>0){    
// on met dans un tableau les quiz pour créer le formulaire de choix des résultats dans twig
        foreach ($quizzes as $value) 
        {

            $choix_quiz[$value->getTitle()]=$value->getId();

        }
//dump($choix_quiz);


// création du formulaire 
        $form = $this->createFormBuilder()
        ->add('quiz', ChoiceType::class, [
            'label' => 'Choisissez le quiz ',
            'group_by' =>'null',
            'choices'  => [
                $choix_quiz
            ],
            'attr' => array(
                'onchange' => 'submit()',
            ),                   
        ])
        ->getForm();

    }
    else{

        $form = $this->createFormBuilder()
        ->add('quiz', ChoiceType::class, [
            'label' => 'Choisissez le quiz ',
            'group_by' =>'null',
            'choices'  => [
                'Pas de quiz' => 'Pas de quiz'
            ],
            'attr' => array(
                'onchange' => 'submit()',
            ),                   
        ])
        ->getForm();


    }







//$this->denyAccessUnlessGranted('ROLE_USER', null, 'Access not allowed');
    $results=$results->findAll();

    if (count($results)>0){

// récupère l'historique des questions qui ont le champ question_success = true
        $historyQuestionJuste = $historyQuestion->findBy( array('question_success' => true));

// récupère tout l'historique des questions
        $historyQuestionTotal = $historyQuestion->findAll();

// calcul du pourcentage de réussite globale
        $pourcentage_reussite_globale= round(count($historyQuestionJuste)/count($historyQuestionTotal)*100,2);


// on récupère un tableau contenant les ids des questions
       // $tableauIdQuestion=$question->findId();

// on récupère un tableau contenant les les réponses aux questions
        $tableauIdReponse=$answer->findReponses();
        //dump($tableauIdReponse);




// recupération de la requête
        $form->handleRequest($request);
//dump($request->request->get('form')['quiz']);


    // si le form est soumis
        if ($form->isSubmitted() && $form->isValid())
        {

                    // recupération des id des questions de la catégorie choisie que l'on met dans un tableau
                    // 4 categorie francais 1  ; 5 categorie francais 2

                    //dump($categorie->findIdQuestion(5));
                        //$tableauIdQuestion=$categorie->findIdQuestion($request->request->get('form')['quiz']);


                    // recupération des id des questions du quiz choisi que l'on met dans un tableau
            $tableauIdQuestion=$quiz->findIdQuestion($request->request->get('form')['quiz']);

                // données du quiz concerné
            $donnees_quiz=$quiz->find($request->request->get('form')['quiz']);
                //dump($tableauIdQuestion);

        }
        else {

                        //$tableauIdQuestion=$categorie->findIdQuestion(current($choix_quiz));

                        // recupération des id des questions du quiz choisi que l'on met dans un tableau
            $tableauIdQuestion=$quiz->findIdQuestion(current($choix_quiz));

                       // données du quiz concerné
            $donnees_quiz=$quiz->find(current($choix_quiz));
                        //dump($tableauIdQuestion);

        }



// on parcourt toutes les questions disponibles
        foreach ($tableauIdQuestion as $id ) {

            //initialisation tableau des réponses données a une question
            $tabReponseDonnee=[];

        //initialisation tableau des réponses aléatoires données a une question
            $tabReponseAleatoires=[];

        //initialisation tableau de tous les réponses données a une question
            $toutes_les_reponses=[];

            // recupération de la bonne réponse à la question donnée
            $reponse = $answer->findBy( array('question' => $id, 'correct' => 1));


            //*********** début calcul réussite question d'id $id **********************
            $question = $historyQuestion->findBy( array('question_id' => $id));
            $nbrsQuestion = count($historyQuestion->findBy( array('question_id' => $id))); // 

            // si on n'a pas répondu à un quiz on sort du foreach
            if (empty($question)) break;

            //nbrs total de question d'id $id juste
            $nbrsQuestionJuste = count($historyQuestion->findBy( array('question_id' => $id,'question_success' => true))); // 

            // pourcentage question d'id $id juste
            $pourcentage_reussite=round($nbrsQuestionJuste/$nbrsQuestion*100,2);
            //*********** fin calcul réussite question d'id $id **********************


            //********************* debut recup quelques réponses données par les élèves*********************
            //$reponseDonnee=$historyAnswer->findBy( array('id' => $question->getId()));
            foreach ($question as $questionEleve) {
               //dump($questionEleve->getId());
                //$tabIdQuestionEleve[]=$questionEleve->getId();

                $reponseDonnee=$historyAnswer->findBy( array('question_history' => $questionEleve->getId(),'correct_given'=> 1));
                $tabReponseDonnee[] = $reponseDonnee;
                //dump($questionEleve);

            }



            //********************** NBRS DE REPONSES ALEATOIRES**************************************
            // on prend deux cles du tableau repose élève aléatoirement
       //dump($tabReponseDonnee);
            if (count($tabReponseDonnee)>1)
            {

                $nbrs_reponse = 2;

                        // verif si nbre reponse aléatoires pas superieur à la taille du tableau
                if ($nbrs_reponse > count($tabReponseDonnee)) $nbrs_reponse = count($tabReponseDonnee);

                $reponseAleatoire= array_rand($tabReponseDonnee,$nbrs_reponse);

                        //dump($reponseAleatoire);


                        // on récupère les réponses et le commentaire associés
                foreach ($reponseAleatoire as $cle) {
                    //dump($tabReponseDonnee);
                    //dump(count($tabReponseDonnee[$cle]));
                    // si plusieurs réponses fausses par exemple et que l'on veut toutes les réponses fausses, il faut boucler sur le deuxième indice

                    if (count($tabReponseDonnee[$cle])>0)
                        $tabReponseAleatoires[]=['reponse_donnee'=>$tabReponseDonnee[$cle][0]->getAnswerText(),'commentaire'=>$tabReponseDonnee[$cle][0]->getJustifyReponse()];
                }
            }
            elseif (count($tabReponseDonnee[0])!=0)
             $tabReponseAleatoires[]=['reponse_donnee'=>$tabReponseDonnee[0][0]->getAnswerText(),'commentaire'=>$tabReponseDonnee[0][0]->getJustifyReponse()];


            //************************ fin recup quelques réponses données par les élèves***********************
//dump($tabReponseDonnee);

         /*   dump($tableauIdQuestion);
            dump($question);
            dump($question[0]->getQuestionText());
            dump($nbrsQuestion);
            dump($nbrsQuestionJuste);
            dump($pourcentage_reussite);
            dump($reponse);
            
            dump($reponseAleatoire);*/



                //********************* debut recup toutes les réponses données par les élèves à la question *********************
            foreach ($tableauIdReponse as  $value) {

                if (array_key_exists($id,$value)) 
                {
                          //      dump($value[$id]);
                           // nbre de chaque reponses à la question     
                    $toutes_les_reponses[$value[$id]][0]=count($historyAnswer->findBy( array('answer_text' => $value[$id],'correct_given'=> 1)));

                          //pourcentage sur le nombre de question 
                    $toutes_les_reponses[$value[$id]][1]=round(count($historyAnswer->findBy( array('answer_text' => $value[$id],'correct_given'=> 1)))/$nbrsQuestion*100,2);

                }
            }

                //********************* fin recup toutes les réponses données par les élèves à la question********************* 


//dump($toutes_les_reponses);


         // on fabrique un tableau des pourcentages de réussite des questions et leur réponse correcte et le nombre de chaque réponse pour transmttre à twig
            $tabResults[]=['titre'=>$question[0]->getQuestionText(),'pourcentage_reussite_question'=>$pourcentage_reussite,'reponse'=> $reponse[0]->getText(),'reponse_donnee'=>$tabReponseAleatoires,'toutes_les_reponses'=>$toutes_les_reponses];



        }

//dump($tabResults);

    // si on n'a pas répondu à un quiz 
        if (empty($question)) $tabResults=null;

        return $this->render('quiz/showResults.html.twig',[
            'results' => $results,
            'pourcentage_global' => $pourcentage_reussite_globale,
            'pourcentage_reussite_question' => $tabResults,
            'donnees_quiz' => $donnees_quiz,
            'form' => $form->createView(),
        ]);

} // fin si il y a des résultats

else {

 return $this->render('quiz/showResults.html.twig',[
    'results' => $results,
    'form' =>  $form->createView(),

]);


}

}



    /**
     * @Route("/{id}/workout", name="quiz_workout", methods="POST")
     */
    public function workout(Request $request, Workout $workout, EntityManagerInterface $em, UserInterface $user = null, \Swift_Mailer $mailer): Response
    {
        //////////////
        // TODO mettre ces opérations d'historique dans un service

        // Check that he does not cheat
        // $workoutRepository = $em->getRepository(Workout::Class);
        // $workoutInDatabase = $workoutRepository->findLastNotCompletedByStudent($user);
        // if ($workout != $workoutInDatabase) {
        //     throw $this->createNotFoundException();
        // }

        $questionNumber = $workout->getNumberOfQuestions();
        $questionResult = 0;
        $workoutScore = 0;
        $quiz = $workout->getQuiz();

        if (!$quiz->getAllowAnonymousWorkout()) {
            $this->denyAccessUnlessGranted('ROLE_USER', null, 'Access not allowed');
        }

        if (!$user) {
            $user = $workout->getStudent();
        }

        // Re-read (from the database) the previous question
        $questionHistoryRepository = $em->getRepository(QuestionHistory::class);
        $questionRepository = $em->getRepository(Question::class);
        //$lastQuestionHistory = $questionHistoryRepository->findLastByWorkout($workout);
        $questionsHistory = $questionHistoryRepository->findAllByWorkout($workout);
        if ($questionsHistory) {
            $lastQuestionHistory = $questionsHistory[0];
            $currentQuestionResult = +1;
            if (!$lastQuestionHistory->getEndedAt()) {
                $lastQuestion = $questionRepository->findOneById($lastQuestionHistory->getQuestionId());
                $form = $this->createForm(QuestionType::class, $lastQuestion, array('form_type'=>'student_questioning'));
                $form->handleRequest($request);
                if ($form->isSubmitted() && $form->isValid()) {
                    foreach ($lastQuestion->getAnswers() as $key => $lastAnswer) {
                        // Save answers history
                        $newAnswerHistory = new AnswerHistory();
                        $newAnswerHistory->setQuestionHistory($lastQuestionHistory);
                        $newAnswerHistory->setAnswerId($lastAnswer->getId());
                        $newAnswerHistory->setAnswerText($lastAnswer->getText());
                        $newAnswerHistory->setAnswerCorrect($lastAnswer->getCorrect());

                        //dump($lastQuestion->getAnswers());
                //dump($lastAnswer->getQuestion()->justify_question);
                $newAnswerHistory->setJustifyReponse($lastAnswer->getQuestion()->justify_reponse); // ajout seabird enregistrement champ justification réponse


                $newAnswerHistory->setCorrectGiven($lastAnswer->getWorkoutCorrectGiven());
                $currentAnswerResult = $lastAnswer->getWorkoutCorrectGiven() == $lastAnswer->getCorrect();
                if (!$currentAnswerResult) {
                    $currentQuestionResult = -1;
                }
                $newAnswerHistory->setAnswerSucces($currentAnswerResult);
                $em->persist($newAnswerHistory);
            }
        }
        $lastQuestionHistory->setQuestionSuccess($currentQuestionResult==+1);
        $questionResult = $currentQuestionResult;
        $em->persist($lastQuestionHistory);

        $lastQuestionHistory->setEndedAt(new \DateTime());
        $lastQuestionHistory->setDuration(date_diff($lastQuestionHistory->getEndedAt(), $lastQuestionHistory->getStartedAt()));
        $em->persist($lastQuestionHistory);
        $workout->setEndedAt(new \DateTime());
                ////////////////////////////
                // Calc score
        $workoutSuccess = 0;
        foreach ($questionsHistory as $questionHistory) {
            if ($questionHistory->getQuestionSuccess()) {
                $workoutSuccess++;
            }
        }
        $workoutScore = round(($workoutSuccess / $quiz->getNumberOfQuestions()) * 100);
        $workout->setScore($workoutScore);
                ////////////////////////////
        $em->persist($workout);
        $em->flush();

        if ($quiz->getShowResultQuestion()) {
            $form = $this->createForm(QuestionType::class, $lastQuestion, array('form_type'=>'student_marking'));
            return $this->render(
                'quiz/workout.html.twig',
                [
                    'id' => $workout->getId(),
                    'quiz' => $quiz,
                    'question' => $lastQuestion,
                    'questionNumber' => $questionNumber,
                    'questionResult' => $questionResult,
                    'progress' => ($questionNumber/$quiz->getNumberOfQuestions())*100,
                    'form' => $form->createView(),
                ]
            );
        }
    }
}


        // Check if enough questions for this quiz
$questionsCount = $questionRepository->countByCategories($quiz->getCategories());
if ($questionsCount < $quiz->getNumberOfQuestions()) {
    $this->addFlash('danger', 'Not enough questions for this quiz');
    $form = $this->createForm(QuizType::class, $quiz, array('form_type'=>'student_questioning'));
    return $this->render(
        'quiz/end.html.twig',
        [
            'id' => $workout->getId(),
            'quiz' => $quiz,
            'form' => $form->createView(),
        ]
    );
}

        // Next question
if ($questionNumber < $quiz->getNumberOfQuestions()) {
    $questionNumber++;
    $workout->setNumberOfQuestions($questionNumber);

    $questionHasBeenPosted = true;
    while ($questionHasBeenPosted) {
                // Draw a random question
        $nextQuestion = $questionRepository->findOneRandomByCategories($quiz->getCategories());
                // Check if this question has not already been posted
        $questionHasBeenPosted = false;
        foreach ($questionsHistory as $questionHistory) {
            if ($questionHistory->getQuestionId() == $nextQuestion->getId()) {
                $questionHasBeenPosted = true;
            }
        }
    }

            // Save the history of the new question
    $newQuestionHistory = new QuestionHistory();
    $newQuestionHistory->setWorkout($workout);
    $newQuestionHistory->setStartedAt(new \DateTime());
    $newQuestionHistory->setQuestionId($nextQuestion->getId());
    $newQuestionHistory->setQuestionText($nextQuestion->getText());
    $newQuestionHistory->setCompleted(false);
    $em->persist($newQuestionHistory);
            //////////////

    $em->flush();

    $form = $this->createForm(QuestionType::class, $nextQuestion, array('form_type'=>'student_questioning'));
    return $this->render(
        'quiz/workout.html.twig',
        [
            'id' => $workout->getId(),
            'quiz' => $quiz,
            'question' => $nextQuestion,
            'questionNumber' => $questionNumber,
            'questionResult' => 0,
            'progress' => (($questionNumber-1)/$quiz->getNumberOfQuestions())*100,
            'form' => $form->createView(),
        ]
    );
} else {
            // Quiz is completed then display end
    $score = $workout->getScore();
    $comment = '';
    $commentLines = explode("\n", $quiz->getResultQuizComment());
    foreach ($commentLines as $commentLine) {
        list($commentInterval, $commentText) = explode(":", $commentLine);
        list($min, $max) = explode("-", $commentInterval);
        if ( ($score>=$min) && ($score<=$max) ){
            $comment = $comment.$commentText. ' ';
        }
    }
    $workout->setComment($comment);
    $workout->setCompleted(true);
    $em->persist($workout);
    $em->flush();

    $message = (new \Swift_Message('Un quiz vient d\'être complété'))
    ->setFrom('eric.devolder@ac-nice.fr')
    ->setTo('eric.devolder@ac-nice.fr')
    ->setBody(
        $this->renderView(
            'emails/quiz_result.html.twig',
            array(
                'username' => $user->getUsername(),
                'email' => $user->getEmail(),
                'quiz' => $quiz,
                'score' => $score,
            )
        ),
        'text/html'
    )
                // //If you also want to include a plaintext version of the message
                // ->addPart(
                //     $this->renderView(
                //         'emails/registration.txt.twig',
                //         array('name' => $name)
                //     ),
                //     'text/plain'
                // )
    ;
    $mailer->send($message);

    $form = $this->createForm(QuizType::class, $quiz, array('form_type'=>'student_questioning'));

    return $this->render(
        'quiz/end.html.twig',
        [
            'id' => $workout->getId(),
            'quiz' => $quiz,
            'score' => $score,
            'questionsHistory' => $questionsHistory,
            'comment' => $workout->getComment(),
            'form' => $form->createView(),
        ]
    );
}
}

    /**
     * @Route("/{id}/start", name="quiz_start", methods="GET")
     */
    public function start(Request $request, Quiz $quiz, EntityManagerInterface $em, UserInterface $user = null): Response
    {
        if (!$quiz->getAllowAnonymousWorkout()) {
            $this->denyAccessUnlessGranted('ROLE_USER', null, 'Access not allowed');
        }
        else {
            if (!$user) {
                $userRepository = $em->getRepository(User::class);
                $user = $userRepository->findOneBy(['username'=>'anonymous']);
                if (!$user) {
                    $user = new User();
                    $user->setUsername('anonymous');
                    $user->setPassword(bin2hex(random_bytes(10)));
                    $user->setEmail('anonymous@domain.tld');
                    $em->persist($user);
                }
            }
        }

        $workoutRepository = $em->getRepository(Workout::class);
        $workout = new Workout();
        $workout->setStudent($user);
        $workout->setQuiz($quiz);
        $workout->setStartedAt(new \DateTime());
        $workout->setNumberOfQuestions(0);
        $em->persist($workout);
        $em->flush();

        return $this->render(
            'quiz/start.html.twig',
            [
                'id' => $workout->getId(),
                'quiz' => $quiz,
            ]
        );
    }

    /**
     * @Route("/", name="quiz_index", methods="GET")
     */
    public function index(QuizRepository $quizRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER', null, 'Access not allowed');

        return $this->render('quiz/index.html.twig', ['quizzes' => $quizRepository->findAll()]);
    }

    /**
     * @Route("/new", name="quiz_new", methods="GET|POST")
     */
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_TEACHER', null, 'Access not allowed');

        $quiz = $em->getRepository(Quiz::class)->create();

        $form = $this->createForm(QuizType::class, $quiz);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $questionRepository = $em->getRepository(Question::class);

            // Check if enough questions for this quiz
            $questionsCount = $questionRepository->countByCategories($quiz->getCategories());
            if ($questionsCount < $quiz->getNumberOfQuestions()) {
                $this->addFlash('danger', 'Not enough questions ('.$questionsCount.') for this quiz');
            } else {
                $em->persist($quiz);
                $em->flush();

                $this->addFlash('success', sprintf('Quiz "%s" is created.', $quiz->getTitle()));

                return $this->redirectToRoute('quiz_index');
            }
        }
        return $this->render('quiz/new.html.twig', [
            'quiz' => $quiz,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="quiz_show", methods="GET")
     */
    public function show(Quiz $quiz): Response
    {
        $this->denyAccessUnlessGranted('ROLE_TEACHER', null, 'Access not allowed');

        return $this->render('quiz/show.html.twig', ['quiz' => $quiz]);
    }

    /**
     * @Route("/{id}/edit", name="quiz_edit", methods="GET|POST")
     */
    public function edit(Request $request, Quiz $quiz): Response
    {
        $this->denyAccessUnlessGranted('ROLE_TEACHER', null, 'Access not allowed');

        $form = $this->createForm(QuizType::class, $quiz);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $quiz->setUpdatedAt(new \DateTime());

            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', sprintf('Quiz "%s" is updated.', $quiz->getTitle()));

            return $this->redirectToRoute('quiz_edit', ['id' => $quiz->getId()]);
        }

        return $this->render('quiz/edit.html.twig', [
            'quiz' => $quiz,
            'form' => $form->createView(),
        ]);
    }


/**
     * @Route("/workout/{id}", name="quiz_workout_delete")
     */
    public function deleteWorkout(Request $request,$id,WorkoutRepository $results, Quiz $quiz, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_TEACHER', null, 'Access not allowed');

         $results=$results->findBy(array('quiz' => $id));
        //dump($results);
            foreach ($results as  $workout) {
                 $em->remove($workout);
                $em->flush();
            }
           

            $this->addFlash('success', sprintf('Quiz "%s" is réinitialisé.', $quiz->getTitle()));
        

        return $this->redirectToRoute('quiz_index');
    }



    /**
     * @Route("/{id}", name="quiz_delete", methods="DELETE")
     */
    public function delete(Request $request, Quiz $quiz, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_TEACHER', null, 'Access not allowed');

        if ($this->isCsrfTokenValid('delete'.$quiz->getId(), $request->request->get('_token'))) {
            //$em = $this->getDoctrine()->getManager();
            $em->remove($quiz);
            $em->flush();

            $this->addFlash('success', sprintf('Quiz "%s" is deleted.', $quiz->getTitle()));
        }

        return $this->redirectToRoute('quiz_index');
    }
}
