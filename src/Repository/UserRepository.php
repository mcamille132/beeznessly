<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Data\SearchExpertsData;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, User::class);
        $this->paginator = $paginator;
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }


    public function searchExperts(searchExpertsData $search)
    {
        $query = $this
            ->createQueryBuilder('user')
            ->innerJoin('user.provider', 'provider')
            ->leftJoin('user.expertise', 'expertise')
            ->where('user.roles LIKE :roles')
            ->setParameter('roles', '%"' . 'ROLE_EXPERT' . '"%')
            ->andWhere('user.isValidated = 1');

        if (!empty($search->provider)) {
            $query = $query
                ->andWhere('provider.id IN (:provider)')
                ->setParameter('provider', $search->provider);
        }

        if (!empty($search->expertise)) {
            $query = $query
                ->andWhere('expertise.id IN (:expertise)')
                ->setParameter('expertise', $search->expertise);
        }

        if (!empty($search->q)) {
            $query = $query
                ->andWhere('user.companyName LIKE :q 
                OR user.description LIKE :q
                OR user.town LIKE :q
                OR expertise.name LIKE :q
                OR provider.type LIKE :q
                OR user.lastname LIKE :q
                OR user.firstname LIKE :q')
                ->setParameter('q', "%{$search->q}%");
        }

        return $query->getQuery()->getResult();
    }

    public function expertsHome()
    {
        return $this->createQueryBuilder('user')
            ->where('user.isValidated = 1')
            ->andWhere('user.roles LIKE :roles')
            ->setParameter('roles', '%"' . 'ROLE_EXPERT' . '"%')
            ->orderby('user.id', 'DESC')
            ->setMaxResults(3)
            ->getQuery()
            ->getResult();
    }
}
