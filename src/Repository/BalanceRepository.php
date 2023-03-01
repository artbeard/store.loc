<?php

namespace App\Repository;

use App\Entity\Balance;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
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
    
    
    public function getBalance()
    {
//	    $sbq = $this->createQueryBuilder('tmp_b');
//	    $sbq->select('tmp_b, MAX(tmp_b.balance_at) as max_balance_at');
//	    //$sbq->from(Balance::class, 'tmp_b');
//	    $sbq->groupBy('tmp_b.product');
//
//	    dump($sbq->getQuery()); exit();
	    $conn = $this->getEntityManager()->getConnection();
	    $sql = "SELECT
    			`product`.`id`, `product`.`name`, `balance`.`cost`, `balance`.`amount`, `balance`.`balance_at`
			FROM
			     `product`
			INNER JOIN
				(
				    SELECT `product_id`, MAX(`balance_at`) as `max_balance_at` FROM `balance` GROUP BY `product_id`) as `tmp_t`
			ON
			    `product`.`id` = `tmp_t`.`product_id`
			INNER JOIN
				`balance`
			ON
			    `balance`.`balance_at` = `tmp_t`.`max_balance_at` AND `product`.`id` = `balance`.`product_id`"
	    ;
	    $stmt = $conn->prepare($sql);
	    //$stmt->execute();
	    dump(
	    	$stmt->executeQuery()->fetchAllAssociative()
	    );exit;
	    
    	$rsm = new ResultSetMapping();
    	$query = $this->getEntityManager()->createNativeQuery("SELECT
    			`product`.`id`, `product`.`name`, `balance`.`cost`, `balance`.`amount`, `balance`.`balance_at`
			FROM
			     `product`
			INNER JOIN
				(
				    SELECT product_id, MAX(balance_at) as max_balance_at FROM `balance` GROUP BY product_id) as `tmp_t`
			ON
			    `product`.`id` = `tmp_t`.`product_id`
			INNER JOIN
				balance
			ON
			    `balance`.`balance_at` = `tmp_t`.max_balance_at AND product.id = balance.product_id", $rsm);
	    
    	return $query->getResult();
    }
    


//    /**
//     * @return Balance[] Returns an array of Balance objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Balance
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
