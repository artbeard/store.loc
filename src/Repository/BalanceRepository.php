<?php

namespace App\Repository;

use App\Entity\Balance;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Balance>
 *
 * @method Balance|null find($id, $lockMode = null, $lockVersion = null)
 * @method Balance|null findOneBy(array $criteria, array $orderBy = null)
 * @method Balance[]    findAll()
 * @method Balance[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BalanceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Balance::class);
    }

    public function save(Balance $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Balance $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
	
	
	/**
	 * Поиск последеней записи для текущего товара
	 * @param $product
	 * @param \DateTimeImmutable $date
	 * @return Balance|null
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	public function findLastPost($product, \DateTimeImmutable $date): ?Balance
    {
        return $this->createQueryBuilder('b')
			->where('b.product = :product_id')
			->andWhere('b.balance_at <= :balanceAt')
			->setParameter('product_id', $product)
			->setParameter('balanceAt', $date->format('Y-m-d'))  //date_format
			->orderBy('b.balance_at', 'DESC')
			->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
	
	/**
	 * Выборка сальдо на текущую дату прямым запросом к БД
	 * @param \DateTimeImmutable $date
	 * @return array
	 * @throws \Doctrine\DBAL\Exception
	 */
    public function getBalance(\DateTimeImmutable $date):array
    {
	    $conn = $this->getEntityManager()->getConnection();
	    $sql = "SELECT
    			`product`.`id`, `product`.`name`, `balance`.`cost`, `balance`.`amount`, `balance`.`balance_at`
			FROM
			     `product`
			INNER JOIN
				(
				    SELECT `product_id`, MAX(`balance_at`) as `max_balance_at` FROM `balance` WHERE `balance_at` <= :dateMark GROUP BY `product_id`) as `tmp_t`
			ON
			    `product`.`id` = `tmp_t`.`product_id`
			INNER JOIN
				`balance`
			ON
			    `balance`.`balance_at` = `tmp_t`.`max_balance_at` AND `product`.`id` = `balance`.`product_id`";
	    $stmt = $conn->prepare($sql);
	    $resultSet = $stmt->executeQuery(['dateMark' => $date->format('Y-m-d')]);
	    return $resultSet->fetchAllAssociative();
    }
    
}
