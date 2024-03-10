<?php

namespace App\Repository;

use App\Entity\Cd;
use App\Entity\Slot;
use App\Entity\Track;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Slot>
 *
 * @method Slot|null find($id, $lockMode = null, $lockVersion = null)
 * @method Slot|null findOneBy(array $criteria, array $orderBy = null)
 * @method Slot[]    findAll()
 * @method Slot[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SlotRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Slot::class);
    }

    /** @return Slot[] */
    public function getAll(): array
    {
        return $this->findBy(criteria: [], orderBy: ['number' => 'ASC']);
    }

    /** @return Paginator<Slot> */
    public function getChargedPaginated(int $page = 1, int $perPage = 10, string $search = ''): Paginator
    {
        $queryBuilder = $this->createQueryBuilder('slot')
            ->leftJoin('slot.Cd', 'cd')
            ->leftJoin('cd.tracks', 'track')
            ->orderBy('slot.number', 'ASC')
            ->setFirstResult($perPage * ($page - 1))
            ->setMaxResults($perPage);
        if ($search) {
            $queryBuilder
                ->andWhere('LOWER(cd.name) LIKE :search OR LOWER(cd.artist) LIKE :search OR LOWER(track.name) LIKE :search')
                ->setParameter('search', '%'.trim(strtolower($search)).'%');
        }

        return new Paginator($queryBuilder->getQuery());
    }
}
