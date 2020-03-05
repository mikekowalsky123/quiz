<?php

namespace App\Repository;

use App\Entity\Quiz;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Quiz|null find($id, $lockMode = null, $lockVersion = null)
 * @method Quiz|null findOneBy(array $criteria, array $orderBy = null)
 * @method Quiz[]    findAll()
 * @method Quiz[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuizRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Quiz::class);
    }

    // /**
    //  * @return Quiz[] Returns an array of Quiz objects
    //  */
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
    /**
     * @return Quiz[]
     */
    public function findByCategory(string $slug) {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT q
            FROM App\Entity\Quiz q
            INNER JOIN App\Entity\Category c
            WHERE c.slug = :slug AND c.id = q.category'
            )->setParameter('slug', $slug);

        return $query->getResult();
    }

    /**
     * @return Quiz
     */
    public function findQuizName(string $slug) {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT q.name
            FROM App\Entity\Quiz q
            WHERE q.slug = :slug'
            )->setParameter('slug', $slug)
            ->setMaxResults(1);
        
        return $query->getOneOrNullResult();
    }
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
}
