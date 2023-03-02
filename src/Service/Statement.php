<?php

namespace App\Service;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Product;
use App\Entity\Statement as StatementEntity;
use App\StatementTypes;
use App\Entity\Balance;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Exception\ApiException;


class Statement
{
	protected ManagerRegistry $doctrine;
	protected ObjectManager $entityManager;
	protected ValidatorInterface $validator;
	protected \DateTimeImmutable $currentDate;
	
	public function __construct(ManagerRegistry $doctrine, ValidatorInterface $validator, RequestStack $requestStack){
		$this->doctrine = $doctrine;
		$this->entityManager = $this->doctrine->getManager();
		$this->validator = $validator;
		$this->currentDate = new \DateTimeImmutable(
			$requestStack->getCurrentRequest()->headers->get('x-current-date') ?? date('Y-m-d')
		);
	}
	
	/**
	 * Список карточек с продуктами
	 * @return Product[]|array|object[]
	 */
	public function getAllProducts()
	{
		return $this->entityManager->getRepository(Product::class)->findAll();
	}
	
	/**
	 * Согдаем карточку продукта
	 * @param $name
	 * @return int|null
	 */
	public function createProduct($name)
	{
		$product = new Product();
		$product->setName($name);
		$this->entityManager->persist($product);
		$this->entityManager->flush();
		return $product->getId();
	}
	
	
	/**
	 * Возвращает массив с проводками на текщую дату
	 * @return array
	 */
	public function getAllPosts(): array
	{
		$statementRepository = $this->entityManager
			->getRepository(StatementEntity::class);
		return $statementRepository->getAllBeforeDate($this->currentDate);
	}
	
	/**
	 * Добавляет входящую проводку
	 * @param array $statementData
	 * @return StatementEntity
	 * @throws \Exception
	 */
	public function addIncomePost(array $statementData): StatementEntity
	{
		$balanceRepository = $this->entityManager->getRepository(Balance::class);
		$statementRepository = $this->entityManager->getRepository(StatementEntity::class);
		$product = $this->entityManager->getRepository(Product::class)
			->find($statementData['product_id']);
		
		$balance = $balanceRepository->findLastPost($product, $this->currentDate);
		
		$statement = new StatementEntity();
		$statement->setAmount($statementData['amount']);
		$statement->setCost($statementData['cost']);
		$statement->setDocumentProp($statementData['document_prop']);
		$statement->setPostType(StatementTypes::POST_IN);
		$statement->setPostedAt($this->currentDate);
		$statement->setProduct($product);
		$statementRepository->save($statement, true);
		//Если проводка успешно прошла
		//Записываем изменения в баланс
		if ($statement->getId())
		{
			//Если проводок по данному товару еще не было
			//или они были в прошлм, создаем новую запись с текущей датой
			if (is_null($balance) || $balance->getBalanceAt() < $this->currentDate)
			{
				$oldAmount = is_null($balance) ? 0 : $balance->getAmount();
				$oldCost = is_null($balance) ? 0 : $balance->getCost();
				$balance = new Balance();
				$balance->setProduct($product);
				$balance->setBalanceAt($this->currentDate);
				$balance->setAmount($oldAmount);
				$balance->setCost($oldCost);
			}
			//Количество в куче увеличивается на величину прихода
			$balance->setAmount(
				$balance->getAmount() +
				$statementData['amount']
			);
			//Стоимость возврастает на величину стоимости партии
			$balance->setCost(
				$balance->getCost() +
				$statementData['cost']
			);
			$balanceRepository->save($balance, true);
			return $statement;
		}
		else
		{
			throw new \Exception('Не удалось провести проводку');
		}
	}
	
	/**
	 * Добавлят исходящую проводку
	 * @param array $statementData
	 * @return StatementEntity
	 * @throws ApiException
	 */
	public function addexpensePost(array $statementData): StatementEntity
	{
		$balanceRepository = $this->entityManager->getRepository(Balance::class);
		$statementRepository = $this->entityManager->getRepository(StatementEntity::class);
		
		$balance =  $balanceRepository->findLastPost($statementData['product'], $this->currentDate);
		if (is_null($balance))
		{
			throw new ApiException('Данного товара на складе нет');
		}
		
		if ($balance->getAmount() < $statementData['amount'])
		{
			throw new ApiException('Данного товара на сладе недостаточно');
		}
		
		$statement = new StatementEntity();
		$statement->setAmount($statementData['amount']);
		$statement->setProduct($statementData['product']);
		$statement->setDocumentProp($statementData['document_prop']);
		$statement->setPostType(StatementTypes::POST_EX);
		$statement->setPostedAt($this->currentDate);
		$statement->setCost(
			round( ($balance->getCost() / $balance->getAmount() * $statementData['amount'] ), 2 )
		);
		$statementRepository->save($statement, true);
		if ($statement->getId())
		{
			//Если баланс "вчерашний" - создаем новый
			if ($balance->getBalanceAt() < $this->currentDate)
			{
				$oldAmount = $balance->getAmount();
				$oldCost = $balance->getCost();
				$balance = new Balance();
				$balance->setProduct($statement->getProduct());
				$balance->setBalanceAt($this->currentDate);
				$balance->setAmount($oldAmount);
				$balance->setCost($oldCost);
			}
			$balance->setAmount(
				$balance->getAmount() - $statement->getAmount()
			);
			$balance->setCost(
				$balance->getCost() - $statement->getCost()
			);
			$balanceRepository->save($balance, true);
		}
		else
		{
			throw new \Exception('Не удалось провести проводку');
		}
		
		return $statement;
	}
	
	/**
	 * возвращает баланс на текущую дату
	 * @return array
	 */
	public function getBalance(): array
	{
		$balanceRepository = $this->entityManager->getRepository(Balance::class);
		return $balanceRepository->getBalance($this->currentDate);
	}
	
}