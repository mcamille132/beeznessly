<?php

namespace App\Repository;

use App\Entity\Ebook;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Data\SearchEbooksData;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * @method Ebook|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ebook|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ebook[]    findAll()
 * @method Ebook[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EbookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, Ebook::class);
        $this->paginator = $paginator;
    }

    public function searchEbooks(searchEbooksData $search)
    {
        $query = $this
            ->createQueryBuilder('ebook')
            ->leftJoin('ebook.expertise', 'expertise')
            ->where('ebook.isValidated = 1');

        if (!empty($search->expertise)) {
            $query = $query
                ->andWhere('expertise.id IN (:expertise)')
                ->setParameter('expertise', $search->expertise);
        }

        if (!empty($search->user)) {
            $query = $query
                ->andWhere('user.id IN (:user)')
                ->setParameter('user', $search->user);
        }

        if (!empty($search->from)) {
            $query = $query
            ->andWhere('ebook.releaseDate >= :from')
            ->setParameter(':from', $search->from);
        }

        if (!empty($search->to)) {
            $query = $query
                ->andWhere('ebook.releaseDate <= :to')
                ->setParameter(':to', $search->to);
        }

        if (!empty($search->q)) {
            $query = $query
                ->andWhere('ebook.title LIKE :q 
                OR ebook.description LIKE :q
                OR expertise.name LIKE :q
                OR ebook.editorName LIKE :q
                OR ebook.author LIKE :q')
                ->setParameter('q', "%{$search->q}%");
        }

        return $query->getQuery()->getResult();
    }
}
