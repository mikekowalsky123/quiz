<?php

namespace App\Repository;

use App\Entity\Questions;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Questions|null find($id, $lockMode = null, $lockVersion = null)
 * @method Questions|null findOneBy(array $criteria, array $orderBy = null)
 * @method Questions[]    findAll()
 * @method Questions[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuestionsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Questions::class);
    }

    /**
     * @return Questions[]
     */

     public function findQuestions(string $slug) {
         $entityManager = $this->getEntityManager();

         $query = $entityManager->createQuery(
             'SELECT qu
             FROM App\Entity\Questions qu
             INNER JOIN App\Entity\Quiz q
             WHERE qu.quiz = q.id AND q.slug = :slug'
         )->setParameter('slug', $slug);

         return $query->getResult();
     }
}
