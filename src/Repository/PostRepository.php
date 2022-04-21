<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Post $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Post $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * Retourne les articles les plus récents qui contiennent le mot-clé $keyword
     * dans le titre ou le contenu de l'article
     *
     * @return Post[]
     */
    public function getLast5byDate (string $keyword): ?array
    {
        $qb = $this->createQueryBuilder('p');
        $qb->where($qb->expr()->like('p.title', $qb->expr()->literal('%'.$keyword.'%')))
            ->orWhere($qb->expr()->like('p.content', $qb->expr()->literal('%'.$keyword.'%')))
            ->orderBy('p.createdAt', 'DESC')
            ->setMaxResults(5)
            // ->setParameter('keyword', $keyword)
            ;
        return $qb->getQuery()
                ->getResult()
                ;
    }

    public function findWithSearchword ($searchword): ?array
    {
        $qb = $this->createQueryBuilder('p');
        $qb->where($qb->expr()->like('p.title', $qb->expr()->literal('%'. $searchword . '%')))
            ->join('p.category', 'c')
            ->orWhere($qb->expr()->like('p.content', $qb->expr()->literal('%'. $searchword . '%')))
            ->orWhere($qb->expr()->like('c.name', $qb->expr()->literal('%'. $searchword .'%')))
        ;
        return $qb->getQuery()->getResult();
    }
}
