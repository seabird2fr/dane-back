<?php

namespace App\Repository;

use App\Entity\Quiz;
use App\Entity\Language;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @method Quiz|null find($id, $lockMode = null, $lockVersion = null)
 * @method Quiz|null findOneBy(array $criteria, array $orderBy = null)
 * @method Quiz[]    findAll()
 * @method Quiz[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuizRepository extends ServiceEntityRepository
{
    private $em;
    private $param;
    private $language;
    private $translator;

    public function __construct(RegistryInterface $registry, EntityManagerInterface $em, ParameterBagInterface $param, TranslatorInterface $translator)
    {
        parent::__construct($registry, Quiz::class);
        $this->em = $em;
        $this->param = $param;
        $this->language = $this->em->getReference(Language::class, $this->param->get('locale'));
        $this->translator = $translator;
    }

    public function create(): Quiz
    {
        $quiz = new Quiz();
        $quiz->setLanguage($this->language);
        $commentLines = "0-24: commentaire 1....\n";
        $commentLines = $commentLines."25-49: commentaire 2....\n";
        $commentLines = $commentLines."50-74: commentaire 3....\n";
        $commentLines = $commentLines."75-100: commentaire 4....\n";
        $quiz->setResultQuizComment($commentLines);
        return $quiz;
    }

    public function find($id, $lockMode = NULL, $lockVersion = NULL)
    {
        $builder = $this->createQueryBuilder('q');
        $builder->andWhere('q.id = :id');
        $builder->setParameter('id', $id);
        $builder->andWhere('q.language = :language');
        $builder->setParameter('language', $this->language);
        $builder->orderBy('q.title', 'ASC');
        return $builder->getQuery()->getOneOrNullResult();
    }

    public function findAll()
    {
        $builder = $this->createQueryBuilder('q');
        $builder->andWhere('q.language = :language');
        $builder->setParameter('language', $this->language);
        $builder->orderBy('q.title', 'ASC');
        return $builder->getQuery()->getResult();
    }

//    /**
//     * @return Quiz[] Returns an array of Quiz objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('q.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Quiz
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */


// Ajout fonction seabird pour avoir le tableau des id des questions correspondant du quiz d'id $id
public function findIdQuestion($id){

$quizzes = $this->findBy(array('id' => $id));
        foreach ($quizzes as $quiz) {

                foreach ($quiz->getCategories() as $value1) {
                    
                    //dump($value1->getQuestions());
                    foreach ($value1->getQuestions() as $value2) {
                                 $tabId[]=$value2->getId();
                                //dump($value2);
                        }

            }
        
        }
//dump($tabId);
return $tabId;

}
    
}
