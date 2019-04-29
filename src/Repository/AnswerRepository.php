<?php

namespace App\Repository;

use App\Entity\Answer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Answer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Answer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Answer[]    findAll()
 * @method Answer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnswerRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Answer::class);
    }

    public function findAll()
    {
        $builder = $this->createQueryBuilder('a');
        $builder->orderBy('a.text', 'ASC');
        return $builder->getQuery()->getResult();
    }


// Ajout fonction seabird pour avoir le tableau des id des reponses: 
public function findId(){

$listeReponse = $this->findBy(array(), array('id' => 'ASC'));
foreach ($listeReponse as $reponse) {
   $tabId[] =$reponse->getId();
}

return $tabId;

}




// Ajout fonction seabird pour avoir le tableau des reponses aux questions: 
public function findReponses(){

$listeReponse = $this->findBy(array(), array('text' => 'ASC'));
foreach ($listeReponse as $reponse) {
   $tabReponse[]=[$reponse->getQuestion()->getId() => $reponse->getText()];
}

return $tabReponse;

}


//    /**
//     * @return Answer[] Returns an array of Answer objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Answer
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
