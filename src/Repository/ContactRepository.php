<?php

namespace App\Repository;

use App\Entity\Contact;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\User;

/**
 * @method Contact|null find($id, $lockMode = null, $lockVersion = null)
 * @method Contact|null findOneBy(array $criteria, array $orderBy = null)
 * @method Contact[]    findAll()
 * @method Contact[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContactRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Contact::class);
    }

     /**
    * @return Contact[] Returns an array of Contact objects
    */
    public function findByEntrepreuneurEmail($email)
    {
        $query = $this
            ->createQueryBuilder('contact')
            ->where('contact.email = :email')
            ->andWhere('contact.user IS NOT NULL')
            ->setParameter('email', $email)
            ->orderBy('contact.id', 'ASC');

           return $query->getQuery()->getResult();
    }
}
