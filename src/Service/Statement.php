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
	
	public function addIncomePost(array $statementData)
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
		//todo позже протестировать работу валидатора
//		$errors = $this->validator->validate($statement);
//		if (count($errors) > 0)
//		{
//			$err=[];
//			foreach ($errors as $_err)
//			{
//				$err[$_err->getPropertyPath()] = $_err->getMessage();
//			}
//			throw new ApiException(json_encode($err));
//		}
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
	
	
	
	public function getBalance()
	{
		$balanceRepository = $this->entityManager->getRepository(Balance::class);
		return $balanceRepository->getBalance($this->currentDate);
	}
	
	
	
	
	
	//todo вынести отсюда списывающий метод
	public function addPost(array $statementData, string $statementType = StatementTypes::POST_IN)
	{
		$statementRepository = $this->entityManager
			->getRepository(Balance::class);

		$date = new \DateTimeImmutable($statementData['posted_at'].' 00:00:00');
		$product = $this->entityManager
			->getRepository(Product::class)
			->find($statementData['product_id']);

		$balance = $statementRepository
			->findLastPost($product, $date);

		$statement = new StatementEntity();
		$statement->setAmount($statementData['amount']);
		$statement->setCost($statementData['cost']);
		$statement->setDocumentProp($statementData['document_prop']);
		$statement->setPostType($statementType);
		$statement->setPostedAt($date);
		$statement->setProduct($product);
		//$this->entityManager->persist($statement);
		//$this->entityManager->flush();

		$errors = $this->validator->validate($statement);
		//todo рализовать свой exception
		if (count($errors) > 0) {
			/*
			 * Uses a __toString method on the $errors variable which is a
			 * ConstraintViolationList object. This gives us a nice string
			 * for debugging.
			 */
			$err=[];
			foreach ($errors as $_err)
			{
				$err[$_err->getPropertyPath()] = $_err->getMessage();
			}
			//$errorsString = (string) $errors;
			throw new \Exception(json_encode($err));
			//dump($errorsString);
			dump($errors); exit();
		}

		if ($statementType == StatementTypes::POST_EX) //проводка расхода
		{
			if (is_null($balance))
				throw new \Exception('Невозможно выполнить расходную операцию с пустого баланса');

			$newAmount = $balance->getAmount() - $statement->getAmount();

			if ($newAmount < 0 )
				throw new \Exception('Невозможно списать с баланса больше, чем на нем есть');

			$this->entityManager->persist($statement);
			$this->entityManager->flush();
			//Если проводка успешно прошла
			//Записываем изменения в баланс
			if ($statement->getId())
			{
				$newCost = $newAmount * ($balance->getCost() / $balance->getAmount());
				if ($balance->getBalanceAt() < $date)
				{
					$balance = new Balance();
					$balance->setProduct($product);
					$balance->setBalanceAt($date);
				}
				$balance->setAmount($newAmount);
				$balance->setCost($newCost);
				$statementRepository->save($balance, true);
			}
			else
			{
				throw new \Exception('Не удалось провести проводку');
			}
		}
		else
		{
			$this->entityManager->persist($statement);
			$this->entityManager->flush();
			//Если проводка успешно прошла
			//Записываем изменения в баланс
			if ($statement->getId())
			{
				//Если проводок по данному товару еще не было
				//или они были в прошлм, создаем новую запись с текущей датой
				if (is_null($balance) || $balance->getBalanceAt() < $date)
				{
					$oldAmount = is_null($balance) ? 0 : $balance->getAmount();
					$oldCost = is_null($balance) ? 0 : $balance->getCost();
					$balance = new Balance();
					$balance->setProduct($product);
					$balance->setBalanceAt($date);
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
				$statementRepository->save($balance, true);
			}
			else
			{
				throw new \Exception('Не удалось провести проводку');
			}
		}
		dump($balance); exit();
	}
	
	
	
	
}