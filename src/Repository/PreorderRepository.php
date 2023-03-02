<?php

namespace App\Repository;

use App\Entity\Preorder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Preorder>
 *
 * @method Preorder|null find($id, $lockMode = null, $lockVersion = null)
 * @method Preorder|null findOneBy(array $criteria, array $orderBy = null)
 * @method Preorder[]    findAll()
 * @method Preorder[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PreorderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Preorder::class);
    }

    public function save(Preorder $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Preorder $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    public function getPreordersByDate(\DateTimeImmutable $date): array
	{
		$est = $date->add(\DateInterval::createFromDateString('-1 day'));
		return $this->createQueryBuilder('p')
			->where('p.sent_at = :sentAt')
			->orWhere('(p.ordered_at < :sentAt AND p.sent_at IS NULL )')
			->setParameter('sentAt', $date->format('Y-m-d'))
            ->orderBy('p.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
	}
}
